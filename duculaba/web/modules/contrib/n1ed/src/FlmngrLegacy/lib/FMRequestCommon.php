<?php
namespace Drupal\n1ed\FlmngrLegacy\lib;

use Drupal\n1ed\FlmngrLegacy\lib\IFmRequest;

class FMRequestCommon extends IFmRequest
{
    public function parseRequest()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->files = $_FILES;
        $this->post = $_POST;
        $this->get = $_GET;
    }
}
