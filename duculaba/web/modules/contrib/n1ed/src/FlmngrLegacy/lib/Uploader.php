<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy\lib;

use Drupal\n1ed\FlmngrLegacy\lib\action\req\ReqError;
use Drupal\n1ed\FlmngrLegacy\lib\action\resp\Message;
use Drupal\n1ed\FlmngrLegacy\lib\action\resp\RespFail;

class Uploader
{
    protected $m_actions;
    protected $m_config;

    public function __construct($config, $actions)
    {
        $this->m_config = $config;
        $this->m_actions = $actions;
    }

    public function run($req)
    {
        $actionName = $req->action;
        $action = $this->m_actions->getAction($actionName);
        if ($action === null) {
            $action = $this->m_actions->getActionError();
            $req = ReqError::createReqError(
                Message::createMessage(Message::ACTION_NOT_FOUND)
            );
        }
        $action->setConfig($this->m_config);
        $resp = null;
        try {
            $resp = $action->run($req);
        } catch (MessageException $e) {
            error_log($e);
            $resp = new RespFail($e->getFailMessage());
        }

        return $resp;
    }
}
