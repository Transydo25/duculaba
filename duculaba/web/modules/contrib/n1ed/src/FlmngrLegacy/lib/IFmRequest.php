<?php
namespace Drupal\n1ed\FlmngrLegacy\lib;

abstract class IFmRequest
{
    public $post;

    public $get;

    public $files;

    public $requestMethod;

    abstract public function parseRequest();

    public function __construct($config = null)
    {
        $this->config = $config;
    }
}
