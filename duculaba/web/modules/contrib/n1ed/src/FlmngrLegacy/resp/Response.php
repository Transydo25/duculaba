<?php

/**
 * Flmngr Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace Drupal\n1ed\FlmngrLegacy\resp;

class Response {

    public $error;
    public $data;

    function __construct($message, $data) {
        $this->error = $message;
        $this->data = $data;
    }

}
