<?php

namespace Drupal\features\Plugin\FeaturesGeneration;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Archiver\ArchiveTar;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\features\FeaturesBundleInterface;
use Drupal\features\FeaturesGenerationMethodBase;
use Drupal\features\Package;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class for generating a compressed archive of packages.
 *
 * @Plugin(
 *   id = \Drupal\features\Plugin\FeaturesGeneration\FeaturesGenerationArchive::METHOD_ID,
 *   weight = -2,
 *   name = @Translation("Download Archive"),
 *   description = @Translation("Generate packages and optional profile as a compressed archive for download."),
 * )
 */
class FeaturesGenerationArchive extends FeaturesGenerationMethodBase implements ContainerFactoryPluginInterface {

  /**
   * The app root.
   *
   * @var string
   */
  protected $root;

  /**
   * The CSRF token generator.
   *
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  protected $csrfToken;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Creates a new FeaturesGenerationArchive instance.
   *
   * @param string $root
   *   The app root.
   * @param \Drupal\Core\Access\CsrfTokenGenerator $csrf_token
   *   The CSRF token generator.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   */
  public function __construct($root, CsrfTokenGenerator $csrf_token, FileSystemInterface $file_system) {
    $this->root = $root;
    $this->csrfToken = $csrf_token;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->getParameter('app.root'),
      $container->get('csrf_token'),
      $container->get('file_system')
    );
  }

  /**
   * The package generation method id.
   */
  const METHOD_ID = 'archive';

  /**
   * The filename being written.
   *
   * @var string
   */
  protected $archiveName;

  /**
   * Reads and merges in existing files for a given package or profile.
   */
  protected function preparePackage(Package $package, array $existing_packages, FeaturesBundleInterface $bundle = NULL) {
    if (isset($existing_packages[$package->getMachineName()])) {
      $existing_directory = $existing_packages[$package->getMachineName()];
    }
    else {
      $existing_directory = $package->getDirectory();
    }
    $existing_directory = $this->root . '/' . $existing_directory;

    if (is_dir($existing_directory)) {
      // Scan for all files.
      $files = $this->fileSystem->scanDirectory($existing_directory, '/.*/');
      // Skip any existing .features.yml as it will be replaced.
      $exclude_files = [
        $package->getMachineName() . '.features',
      ];
      foreach ($files as $file) {
        // Skip files in the any existing configuration directory, as these
        // will be replaced.
        foreach (array_keys($this->featuresManager->getExtensionStorages()->getExtensionStorages()) as $directory) {
          if (strpos($file->uri, $directory) !== FALSE) {
            continue 2;
          }
        }
        // Merge in the info file.
        if ($file->name == $package->getMachineName() . '.info') {
          $files = $package->getFiles();
          $files['info']['string'] = $this->mergeInfoFile($package->getFiles()['info']['string'], $file->uri);
          $package->setFiles($files);
        }
        // Read in remaining files.
        elseif (!in_array($file->name, $exclude_files)) {
          // Determine if the file is within a subdirectory of the
          // extension's directory.
          $file_directory = dirname($file->uri);
          if ($file_directory !== $existing_directory) {
            $subdirectory = substr($file_directory, strlen($existing_directory) + 1);
          }
          else {
            $subdirectory = NULL;
          }
          $package->appendFile([
            'filename' => $file->filename,
            'subdirectory' => $subdirectory,
            'string' => file_get_contents($file->uri),
          ]);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function generate(array $packages = [], FeaturesBundleInterface $bundle = NULL) {

    // If no packages were specified, get all packages.
    if (empty($packages)) {
      $packages = $this->featuresManager->getPackages();
    }

    // Determine the best name for the tar archive.
    // Single package export, so name by package name.
    if (count($packages) == 1) {
      $filename = current($packages)->getMachineName();
    }
    // Profile export, so name by profile.
    elseif (isset($bundle) && $bundle->isProfile()) {
      $filename = $bundle->getProfileName();
    }
    // Non-default bundle, so name by bundle.
    elseif (isset($bundle) && !$bundle->isDefault()) {
      $filename = $bundle->getMachineName();
    }
    // Set a fallback name.
    else {
      $filename = 'generated_features';
    }

    $return = [];

    $this->archiveName = $filename . '.tar.gz';
    $archive_name = $this->fileSystem->getTempDirectory() . '/' . $this->archiveName;
    if (file_exists($archive_name)) {
      $this->fileSystem->delete($archive_name);
    }

    $archiver = new ArchiveTar($archive_name);

    // Add package files.
    foreach ($packages as $package) {
      if (count($packages) == 1) {
        // Single module export, so don't generate entire modules dir structure.
        $package->setDirectory($package->getMachineName());
      }
      $this->generatePackage($return, $package, $archiver);
    }

    return $return;
  }

  /**
   * Writes a package or profile's files to an archive.
   *
   * @param array &$return
   *   The return value, passed by reference.
   * @param \Drupal\features\Package $package
   *   The package or profile.
   * @param \Drupal\Core\Archiver\ArchiveTar $archiver
   *   The archiver.
   */
  protected function generatePackage(array &$return, Package $package, ArchiveTar $archiver) {
    $success = TRUE;
    foreach ($package->getFiles() as $file) {
      try {
        $this->generateFile($package->getDirectory(), $file, $archiver);
      }
      catch (\Exception $exception) {
        $this->failure($return, $package, $exception);
        $success = FALSE;
        break;
      }
    }
    if ($success) {
      $this->success($return, $package);
    }
  }

  /**
   * Registers a successful package or profile archive operation.
   *
   * @param array &$return
   *   The return value, passed by reference.
   * @param \Drupal\features\Package $package
   *   The package or profile.
   */
  protected function success(array &$return, Package $package) {
    $type = $package->getType() == 'module' ? $this->t('Package') : $this->t('Profile');
    $return[] = [
      'success' => TRUE,
      // Archive writing doesn't merit a message, and if done through the UI
      // would appear on the subsequent page load.
      'display' => FALSE,
      'message' => '@type @package written to archive.',
      'variables' => [
        '@type' => $type,
        '@package' => $package->getName(),
      ],
    ];
  }

  /**
   * Registers a failed package or profile archive operation.
   *
   * @param array &$return
   *   The return value, passed by reference.
   * @param \Drupal\features\Package $package
   *   The package or profile.
   * @param \Exception $exception
   *   The exception object.
   * @param string $message
   *   Error message when there isn't an Exception object.
   */
  protected function failure(array &$return, Package $package, \Exception $exception = NULL, $message = '') {
    $type = $package->getType() == 'module' ? $this->t('Package') : $this->t('Profile');
    $return[] = [
      'success' => FALSE,
      // Archive writing doesn't merit a message, and if done through the UI
      // would appear on the subsequent page load.
      'display' => FALSE,
      'message' => '@type @package not written to archive. Error: @error.',
      'variables' => [
        '@type' => $type,
        '@package' => $package->getName(),
        '@error' => isset($exception) ? $exception->getMessage() : $message,
      ],
    ];
  }

  /**
   * Writes a file to the file system, creating its directory as needed.
   *
   * @param string $directory
   *   The extension's directory.
   * @param array $file
   *   Array with the following keys:
   *   - 'filename': the name of the file.
   *   - 'subdirectory': any subdirectory of the file within the extension
   *      directory.
   *   - 'string': the contents of the file.
   * @param \Drupal\Core\Archiver\ArchiveTar $archiver
   *   The archiver.
   *
   * @throws Exception
   */
  protected function generateFile($directory, array $file, ArchiveTar $archiver) {
    $filename = $directory;
    if (!empty($file['subdirectory'])) {
      $filename .= '/' . $file['subdirectory'];
    }
    $filename .= '/' . $file['filename'];
    // Set the mode to 0644 rather than the default of 0600.
    if ($archiver->addString($filename, $file['string'], FALSE, ['mode' => 0644]) === FALSE) {
      throw new \Exception($this->t('Failed to archive file @filename.', ['@filename' => $file['filename']]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function exportFormSubmit(array &$form, FormStateInterface $form_state) {
    // Redirect to the archive file download.
    $form_state->setRedirect('features.export_download', ['uri' => $this->archiveName, 'token' => $this->csrfToken->get($this->archiveName)]);
  }

}
