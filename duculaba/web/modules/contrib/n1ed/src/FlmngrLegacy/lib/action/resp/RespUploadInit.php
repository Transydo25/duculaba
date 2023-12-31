<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy\lib\action\resp;

class RespUploadInit extends RespOk
{
    public $uploadId;
    public $settings;

    public function __construct($uploadId, $config)
    {
        $this->uploadId = $uploadId;
        $this->settings = new Settings();
        $this->settings->maxImageResizeWidth = $config->getMaxImageResizeWidth();
        $this->settings->maxImageResizeHeight = $config->getMaxImageResizeHeight();
    }
}
