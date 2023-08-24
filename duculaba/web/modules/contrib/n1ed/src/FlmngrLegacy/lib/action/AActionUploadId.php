<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy\lib\action;

use Drupal\n1ed\FlmngrLegacy\lib\action\resp\Message;
use Drupal\n1ed\FlmngrLegacy\lib\MessageException;

abstract class AActionUploadId extends AAction
{
    protected function validateUploadId($req)
    {
        if ($req->uploadId === null) {
            throw new MessageException(
                Message::createMessage(Message::UPLOAD_ID_NOT_SET)
            );
        }

        $dir = $this->m_config->getTmpDir() . '/' . $req->uploadId;
        if (!file_exists($dir) || !is_dir($dir)) {
            throw new MessageException(
                Message::createMessage(Message::UPLOAD_ID_INCORRECT)
            );
        }
    }
}
