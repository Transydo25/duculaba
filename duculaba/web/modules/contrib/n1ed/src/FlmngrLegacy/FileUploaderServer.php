<?php


/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy;

use Drupal\n1ed\FlmngrLegacy\servlet\UploaderServlet;
use Exception;

class FileUploaderServer {

    static function fileUploadRequest($config, $post, $files) {
        try {
            $servlet = new UploaderServlet();
            $servlet->init($config);
            $servlet->doPost($post, $files);
            die;
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }
}