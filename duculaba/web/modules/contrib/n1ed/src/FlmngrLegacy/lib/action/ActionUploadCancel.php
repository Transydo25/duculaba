<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy\lib\action;

use Drupal\n1ed\FlmngrLegacy\lib\file\Utils;
use Drupal\n1ed\FlmngrLegacy\lib\action\resp\Message;
use Drupal\n1ed\FlmngrLegacy\lib\action\resp\RespOk;
use Drupal\n1ed\FlmngrLegacy\lib\MessageException;
use Exception;

class ActionUploadCancel extends AActionUploadId
{
    public function getName()
    {
        return 'uploadCancel';
    }

    public function run($req)
    {
        $this->validateUploadId($req);
        if (!$this->m_config->doKeepUploads()) {
            try {
                Utils::delete(
                    $this->m_config->getTmpDir() . '/' . $req->uploadId
                );
            } catch (Exception $e) {
                error_log($e);
                throw new MessageException(
                    Message::createMessage(Message::UNABLE_TO_DELETE_UPLOAD_DIR)
                );
            }
        }
        return new RespOk();
    }
}
