<?php
namespace Drupal\n1ed\Controller;

use Drupal\n1ed\Flmngr\lib\IFmRequest;

class FlmngrControllerDrupalRequest extends IFmRequest
{
    public function parseRequest()
    {
        $request = $this->config['drupalRequestStack']->getCurrentRequest();
        $this->requestMethod = $request->getMethod();
        $this->files = $_FILES;
        $this->post = $request->request->all();
        $this->get = $request->query->all();
    }
}
