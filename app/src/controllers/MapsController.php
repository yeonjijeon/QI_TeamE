<?php
namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class UCSDController extends BaseController
{
    public function helloworld(Request $request, Response $response, $args)
    {
      echo"Hello World";
 /* $this->logger->info("Home page action dispatched");

        $this->flash->addMessage('info', 'Sample flash message');

        $this->view->render($response, 'home.twig');
        return $response;
 */   }


}
