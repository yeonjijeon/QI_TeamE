<?php
namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;




final class HeartDogController extends BaseController
{

 

    public function handleSignUp(Request $request, Response $response, $args) {
         echo "I am going to check the database for your email address";
         var_dump($_POST);
         print_r($_POST);
         $email_found = 1;
         if ($email_found){$status = "error";}
         else $status = "good";
         // write sql query
         // if $email found, then write error message
         // if $email not found, then save to database and then send email

         $this->view->render($response, 'hendlesignup.twig', ['status'=>$status, 'post' => $_POST]);
           return $response;
    }


}
