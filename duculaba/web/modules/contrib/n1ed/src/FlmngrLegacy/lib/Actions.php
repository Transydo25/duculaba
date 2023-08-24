<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy\lib;

use Drupal\n1ed\FlmngrLegacy\lib\action\ActionError;
use Drupal\n1ed\FlmngrLegacy\lib\action\ActionUploadAddFile;
use Drupal\n1ed\FlmngrLegacy\lib\action\ActionUploadCancel;
use Drupal\n1ed\FlmngrLegacy\lib\action\ActionUploadCommit;
use Drupal\n1ed\FlmngrLegacy\lib\action\ActionUploadInit;
use Drupal\n1ed\FlmngrLegacy\lib\action\ActionUploadRemoveFile;
use Drupal\n1ed\FlmngrLegacy\lib\action\ActionQuickUpload;

class Actions
{
    protected $m_actions = [];

    public function __construct()
    {
        $this->m_actions[] = new ActionError();

        $this->m_actions[] = new ActionUploadInit();
        $this->m_actions[] = new ActionUploadAddFile();
        $this->m_actions[] = new ActionUploadRemoveFile();
        $this->m_actions[] = new ActionUploadCommit();
        $this->m_actions[] = new ActionUploadCancel();
        $this->m_actions[] = new ActionQuickUpload();
    }

    public function getActionError()
    {
        return $this->getAction('error');
    }

    public function getAction($name)
    {
        for ($i = 0; $i < count($this->m_actions); $i++) {
            if ($this->m_actions[$i]->getName() === $name) {
                return $this->m_actions[$i];
            }
        }
        return null;
    }
}
