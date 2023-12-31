<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy\lib\action;

use Drupal\n1ed\FlmngrLegacy\lib\file\FileUploaded;
use Drupal\n1ed\FlmngrLegacy\lib\action\resp\Message;
use Drupal\n1ed\FlmngrLegacy\lib\action\resp\RespOk;
use Drupal\n1ed\FlmngrLegacy\lib\MessageException;

class ActionUploadRemoveFile extends AActionUploadId
{
    public function getName()
    {
        return 'uploadRemoveFile';
    }

    public function run($req)
    {
        $this->validateUploadId($req);
        $file = new FileUploaded(
            $this->m_config,
            $req->uploadId,
            $req->name,
            $req->name
        );
        $file->checkForErrors(true);

        if ($file->getErrors()->size() > 0) {
            throw new MessageException(
                Message::createMessageByFile(
                    Message::UNABLE_TO_DELETE_UPLOAD_DIR,
                    $file->getData()
                )
            );
        }

        $file->delete();
        return new RespOk();
    }
}
