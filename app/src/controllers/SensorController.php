<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\Driver\PDOException;

final class SensorController extends BaseController
{
  protected $logger;
  protected $SensorModel;
  protected $view;

  public function __construct($logger, $SensorModel, $view)
  {
    $this->logger = $logger;
    $this->SensorModel = $SensorModel;
    $this->view = $view;
  }
  
  //============================================================================================
  //  page rendering
  //============================================================================================

  public function sensor_list_view(Request $request, Response $response, $args)
  {
    // $this->view->render($response, 'sensorlistview.twig');
    // return $response;
  }


  public function sensor_userlist_view_process(Request $request, Response $response, $args)
  {
    try{
      if (isset($_POST['USN'])) { 
        $user['USN'] =  $_POST['USN'];
      } else {
        $json = ['error_message' => 'Please, Sign-in first.', 'result_code' => 1];
        return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($json));
      }

      $results = $this->SensorModel->select_sensor($user);

      if (count($results)==0){

        $json = ['error_message' => 'There is no sensor inforamtion ', 'result_code' => 1];
        return $response->withHeader('Content-type', 'application/json')
        ->write(json_encode($json));
      }else{
        return $response->withHeader('Content-type', 'application/json')
        ->write(json_encode($results));
      }     
    }
    catch(Exception $e )
    {
      $json = ['error_message' => 'Some errors occurred during Sensor list view.', 'result_code' => 1];
      return $response->withHeader('Content-type', 'application/json')
        ->write(json_encode($json)); 
    }
  }


  


  // SENSOR-APP

    
  public function app_sensor_register(Request $request, Response $response, $args)
  {
    $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
    $data = json_decode($json, true);
      
  
    try{
      if (isset($data['USN'])||isset($data['MAC_ADD']) ||isset($data['DEVICE'])) {
        $user['USN'] =  $data['USN'];
        $user['MAC_ADD'] =  $data['MAC_ADD'];
        $user['DEVICE'] =  $data['DEVICE'];
      } else {
        $json = ['error_message' => 'Please, Sign-in first.', 'result_code' => 1];
        return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($json));
      } 

      $results = $this->SensorModel->select_sensor_by_USN_AND_MAC($user);
      if(count($results)==1){
        $json = ['error_message' => 'Already registered MAC address', 'result_code' => 1
      ,'SSN'=>$results[0]['SSN']];

      }else{
        $results2 = $this->SensorModel->select_USN_by_MAC($user);
        if(count($results2)==0){
          $user['REG_ACTIVE'] = 1;
            if($this->SensorModel->insert_sensor($user)>0){
              if(count($this->SensorModel->select_SSN_by_MAC($user))>0){
                $json = ['success_message' => 'Sensor registration is completed.', 'result_code' => 0];
              }else{
                $json = ['error_message' => 'Some errors occurred during Sensor registration1.', 'result_code' => 1];
              }
            }
            else{
              $json = ['error_message' => 'Some errors occurred during Sensor registration2.', 'result_code' => 1];
            }
        }
        else{
        $json = ['error_message' => 'Some errors occurred during Sensor registration3.', 'result_code' => 1];
        }
      }
      return $response->withHeader('Content-type', 'application/json')
      ->write(json_encode($json));
    }
    catch(PDOException $e)     
      {
      $json = ['error_message' => 'Some errors occurred during Sensor registration4', 'result_code' => 1];
      return $response->withHeader('Content-type', 'application/json')
        ->write(json_encode($json)); 
    }
  }



    public function app_sensor_deregister(Request $request, Response $response, $args)
    {
      $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
      $data = json_decode($json, true);

      try{
        if (isset($data['USN'])) {
          $user['USN'] =  $data['USN'];
          $user['SSN'] =  $data['SSN'];
          $user['REG_ACTIVE'] = 0;
        } else {
          $json = ['error_message' => 'Please, Sign-in first.', 'result_code' => 1];
          return $response->withHeader('Content-type', 'application/json')
            ->write(json_encode($json));
        }  
        $results = $this->SensorModel->update_REG_ACTIVE($user);

        if (($results)>0){

          $json = ['success_message' => 'Sensor deregistration is completed.',
           'result_code' => 0];
          return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($json));
        }else{
          $json = ['error_message' => 'Sensor deregistration is failed',
           'result_code' => 1];
          return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($json));
        }     
      }
      catch(Exception $e )
      {
        $json = ['error_message' => 'Some errors occurred during Sensor deregistration.', 'result_code' => 1];
        return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($json)); 
      }
    }

    
    public function app_sensor_list_view(Request $request, Response $response, $args)
    {
      $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
      $data = json_decode($json, true);

      try{
        if (isset($data['USN'])) {
          $user['USN'] =  $data['USN'];
        } else {
          $json = ['error_message' => 'Please, Sign-in first.', 'result_code' => 1];
          return $response->withHeader('Content-type', 'application/json')
            ->write(json_encode($json));
        }

        $results = $this->SensorModel->select_sensor($user);

        if (count($results)==0){

          $json = ['error_message' => 'There is no sensor inforamtion ', 'result_code' => 1];
          return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($json));
        }else{
          return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($results));
        }     
      }
      catch(Exception $e)
      {
        $json = ['error_message' => 'Some errors occurred during Sensor list view.', 'result_code' => 1];
        return $response->withHeader('Content-type', 'application/json')
          ->write(json_encode($json)); 
      }
    }









}
