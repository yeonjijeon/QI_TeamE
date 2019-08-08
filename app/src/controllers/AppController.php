<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


final class AppController extends BaseController
{


  public function __construct($view, $logger, $UserModel)
  {
    $this->logger = $logger;
    $this->view = $view;
    $this->UserModel = $UserModel;
  }

  //Sign up
  public function app_sign_up_process(Request $request, Response $response, $args)
  {
    $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
    $data = json_decode($json, true);
    
    try {
        $user['EMAIL'] =  $data['Email'];
        $user['FNAME'] =  $data['FirstName'];
        $user['LNAME'] =  $data['LastName'];
        $user['PHONE'] =  $data['PhoneNumber'];
        $user['HASHED_PW'] = password_hash($data['Password'], PASSWORD_DEFAULT);
        $user['IS_ACTIVE'] = 0;
        $user['IS_SIGNIN'] = 0;

        $temp =  password_hash(strval(mt_rand()), PASSWORD_DEFAULT);
        $user['NONCE'] = str_replace(array('\\', '/', '.'), 'b', $temp);

        for ($i = 0; $i <= 6; $i++) {
          $user['NONCE'][$i] =  strval(mt_rand());
         }
      // else{
    
      //   $response = array(
      //     'error_message' => 'No information received.',
      //     'result_code' => 1
      //   );
      //   return json_encode($response);
      //   }

      // // PASSWORD와 CONFIRMPASSWORD 불일치
      // if (strcmp($user['Password'], $user['ConfirmPassword']) !== 0) { 

      //   $response = array(
      //     'error_message' => 'There is no e_mail or pssword.',
      //     'result_code' => 1
      //   );
      //   return json_encode($response);
      // }

      
      // 이메일 중복 검사
      if (count($this->UserModel->duplicate_checking($user)) > 0) {   //User does exist in "User" table
        
        $response = array(
          'error_message' => 'E-mail address already exist.',
          'result_code' => 1
        );
        return json_encode($response);
      } else {  
          $query_results =  $this->UserModel->insert_user($user);

          if ($query_results) {
            $mail = new PHPMailer(true);                            
            try {
              //Server settings
              $mail->isSMTP();                                   
              $mail->Host = 'smtp.gmail.com';                    
              $mail->SMTPAuth = true;                             
              $mail->Username = 'iotteame2019@gmail.com';           
              $mail->Password = 'teame!ucsd';                       
              $mail->SMTPSecure = 'tls';                         
              $mail->Port = 587;                               

              //Recipients
              $url =  'http://teame-iot.calit2.net/heartdog/signin/activate/' . $user['NONCE'];
              $mail->setFrom('jeonyeonji1028@gmail.com', 'HEART DOG');
              $mail->addAddress($user['EMAIL'], 'HEART DOG');       
              $mail->isHTML(true);                                 
              $mail->Subject = 'Welcome to HEART DOG!';
              $mail->Body    = 'Hi there!
              <br>We are happy that you are almost HEART DOG member!
              <br>If you want to activate your account, Click the below link and Finish the Sign-up!
              <br>http://teame-iot.calit2.net/heartdog/signin/activate/' . $user['NONCE'];

              $mail->send();

              $response = array(
                'success_message' => 'Authentication-mail has been sent. Please, check your E-mail.',
                'result_code' => 0
              );
              return json_encode($response);
            } catch (Exception $e) {

              $response = array(
                'error_message' => 'Authentication-mail could not be sent. Try again.',
                'result_code' => 1
              );
              return json_encode($response);
            }
          } else {  //else 
            $response = array(
              'error_message' => 'Some errors occurred during sign-up.',
              'result_code' => 1
            );
            return json_encode($response);
          }
        
      }
    } catch (PDOException $e) {
      $response = array(
        'error_message' => 'Some errors occurred during sign-up.',
        'result_code' => 1
      );
      return json_encode($response);
      // $json = ['error_message' => 'Some errors occurred during sign-up.', 'result_code' => 1];
      // return $response->withHeader('Content-type', 'application/json')
      //   ->write(json_encode($json));
    }
  }


  //Sign in
  public function app_sign_in_process(Request $request, Response $response, $args)
  {
  
    $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
    $data = json_decode($json, true);

    try {

      $user['Email'] =  $data['Email'];
      $user['Password'] =  $data['Password'];

      $results = $this->UserModel->select_USN_PW($user); // select USN and hashed_pwd from User table 

      // print_r($data);
      if ($results) {
        if (password_verify($user['Password'], $results['HASHED_PW'])) {
          $user['USN'] = $results['USN'];
          $results['IS_SIGNIN'] = 1; //set login state flage to 1

          if ($this->UserModel-> update_user_set_IS_SIGNIN($results) == 0) {
            $response = array(
              // 'success_message' => 'Sign-In is completed.',
              'USN' => $user['USN'],
              'result_code' => 0
            );
            return json_encode($response);
          } else {
            $response = array(
              // 'error_message' => 'Some errors occurred during sign-in.',
              'result_code' => 3
            );
            return json_encode($response);
          }
        } else {
          $response = array(
            // 'error_message' => 'Sign-In is Failed, Password is wrong.',
            'result_code' => 2
          );
          return json_encode($response);
        }
      } else {
        $response = array(
          // 'error_message' => 'E-mail does not exist, Please, sign-up first.',
          'result_code' => 1
        );
        return json_encode($response);
      }
    } catch (PDOException $e) {
      $response = array(
        // 'error_message' => 'Some errors occurred during sign-in.',
        'result_code' => 4
      );
      return json_encode($response);
    }
  }


  //Sign out
  public function app_sign_out_process(Request $request, Response $response, $args)
  {
    $json = file_get_contents('php://input'); 
    $data = json_decode($json, true);
  
    try {
      if (isset($data['USN'])) {
        $user['USN'] =  $data['USN'];
        $user['IS_SIGNIN'] = 0; 
      } else {
        $response = array(
          'error_message' => 'Please, Sign-in first.',
          'result_code' => 1
        );
        return json_encode($response);
      }

      if ($this->UserModel->update_user_set_IS_SIGNIN($user) == 0) {
        $response = array(
          'success_message' => 'Copmlete Sign-out.',
          'result_code' => 0
        );
        return json_encode($response);
      } else {
        $response = array(
          'error_message' => 'Some errors occurred during sign-out.',
          'result_code' => 2
        );
        return json_encode($response);
      }
    } catch (PDOException $e) {
      $response = array(
        'error_message' => 'Some errors occurred during sign-out',
        'result_code' => 3
      );
      return json_encode($response);
    }
  }


  //Change password
  public function app_change_pwd_process(Request $request, Response $response, $args)
  {
    $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
    $data = json_decode($json, true);
    
    try {
      if (isset($data['USN'])) {
        $user['USN'] =  $data['USN'];
        $user['Password'] =  $data['Password'];
        $user['new_password'] =  $data['new_password'];
        $user['confirm_new_password'] =  $data['confirm_new_password'];
      } else {
        $response = array(
          'error_message' => 'Please, Sign-in first.',
          'result_code' => 1
        );
        return json_encode($response);
      }


      $results = $this->UserModel->select_USN_PW_by_usn($user);

      if ($results) {
        if (password_verify($user['Password'], $results['HASHED_PW'])) {
          $user['new_password'] = password_hash($user['new_password'], PASSWORD_DEFAULT);


          $temp =  password_hash(strval(mt_rand()), PASSWORD_DEFAULT);
          $user['NONCE'] = str_replace(array('\\', '/', '.'), 'b', $temp);

          for ($i = 0; $i <= 6; $i++) {
            $user['NONCE'][$i] =  strval(mt_rand());
          }
          $query_results =  $this->UserModel->update_user_set_password($user);

          $response = array(
            'success_message' => 'Copmlete change password.',
            'result_code' => 0
          );
          return json_encode($response);
         
        } else {
          $response = array(
             'error_message' => 'Password is wrong.',
            'result_code' => 2
          );
          return json_encode($response);
        }
      } else {
        $response = array(
          'error_message' => 'Please, Sign-in first.',
          'result_code' => 4
        );
        return json_encode($response);
      }
    } catch (PDOException $e) {
      $response = array(
        'error_message' => 'Some errors occurred during sign-in.',
        'result_code' => 3
      );
      return json_encode($response);
    }


  }

  // ID Cancellation
  // public function app_ID_cancellation_process(Request $request, Response $response, $args)
  // {
    
  //   $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
  //   $data = json_decode($json, true);

  //   $json = [];
  //   $user = [];
  //   try {

  //     if (isset($data['USN']) && isset($data['Password'])) {
  //       $user['Password'] =  $data['Password'];
  //       $user['USN'] =  $data['USN'];
  //     } else {

  //       $response = array(
  //         'error_message' => 'There is no password or Please, Sign-in first.',
  //         'result_code' => 1
  //       );
  //       return json_encode($response);
  //     }

  //     $results = $this->UserModel->select_HASHED_PW($user);

  //     if ($results) {

  //       if (password_verify($user['Password'], $results['HASHED_PW'])) {
  //         $user['IS_ACTIVE'] = 2;  // set isActive flag to 0. it is mean that user account is cancelled.

  //         if ($this->UserModel->update_user_set_IS_ACTIVE($user) == 0) { //update database 


            
  //       $response = array(
  //         'success_message' => 'ID cancellation is completed.',
  //         'result_code' => 0
  //       );
  //       return json_encode($response);

  //         } else {
  //           $response = array(
  //             'success_message' => 'ID cancellation is completed.',
  //             'result_code' => 0
  //           );
  //           return json_encode($response);
            
  //           $json = [
  //             'error_message' => 'Some errors occurred during ID Cancellation.', 'result_code' => 1
  //           ];
  //           return $response->withHeader('Content-type', 'application/json')
  //             ->write(json_encode($json));
  //         }
  //       } else {
  //         $json = [
  //           'error_message' => 'ID cancellation is Failed, Password is wrong.', 'result_code' => 1
  //         ];
  //         return $response->withHeader('Content-type', 'application/json')
  //           ->write(json_encode($json));
  //       }
  //     } else {

  //       $json = [
  //         'error_message' => 'Some errors occurred during ID Cancellation.', 'result_code' => 1
  //       ];
  //       return $response->withHeader('Content-type', 'application/json')
  //         ->write(json_encode($json));
  //     }
  //   } catch (PDOException $e) {
  //     $json = ['error_message' => 'Some errors occurred during ID Cancellation.', 'result_code' => 1];
  //     return $response->withHeader('Content-type', 'application/json')
  //       ->write(json_encode($json));
  //   }

  // }
  
  public function app_forget_pw_process(Request $request, Response $response, $args)
  {
    $json = file_get_contents('php://input'); //allows the server to read raw POST data from the request body.
    $data = json_decode($json, true);

    try{
      $user['EMAIL'] =  $data['Email'];
      
      $results = $this->UserModel->duplicate_checking($user);

      if (count($results) == 1) { // Only one account is exist with the e-mail entered
        $user['USN'] = $results[0]['USN'];
        $temp =  password_hash(strval(mt_rand()), PASSWORD_DEFAULT);
        $user['NONCE'] = str_replace(array('\\', '/', '.'), 'b', $temp);
        
      if ($this->UserModel-> update_user_set_NONCE($user) == 0) {

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
          // Server settings
          $mail->isSMTP();                                        // Set mailer to use SMTP
          $mail->Host = 'smtp.gmail.com';                         // Specify main and backup SMTP servers
          $mail->SMTPAuth = true;                                 // Enable SMTP authentication
          $mail->Username = 'iotteame2019@gmail.com';                 // SMTP username
          $mail->Password = 'teame!ucsd';                           // SMTP password
          $mail->SMTPSecure = 'tls';                              // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 587;                                      // TCP port to connect to

          //Recipients
          $mail->setFrom('dmsrb1595@gmail.com', 'HEART DOG');
          $mail->addAddress($user['EMAIL'], 'HEART DOG');         // Add a recipient
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = 'Welcome to HEART DOG!';
            $mail->Body    = 'Hi there!
            <br>We are happy that you are almost HEART DOG member!
            <br>If you want to activate your account, Click the below link and Finish the Sign-up!
            <br>http://teame-iot.calit2.net/account/resetpwd/' . $user['NONCE'];

          $mail->send();
          
          $response = array(
            'success_message' => 'Authentication-mail has been sent. Please, check your E-mail.',
            'result_code' => 0
          );
          return json_encode($response);


        } catch (Exception $e) {

          $response = array(
            'error_message' => 'Authentication-mail could not be sent. Try again.',
            'result_code' => 1
          );
          return json_encode($response);
        } // end of catch statement 
      } else {
        $response = array(
            'error_message' => 'Some errors occurred during forgotten password change.',
            'result_code' => 1
          );
          return json_encode($response);
      }
    } else {  // There is no user information or There is more than two user information.
      $response = array(
        'error_message' => 'Some errors occurred during forgotten password change.',
        'result_code' => 1
      );
      return json_encode($response);
    }
  } catch (PDOException $e) {
    $response = array(
      'error_message' => 'Some errors occurred during forgotten password change.',
      'result_code' => 1
    );
    return json_encode($response);
  }

    

    // $json = ['error_message' => 'Some errors occurred during sign-up.', 'result_code' => 1];
    // return $response->withHeader('Content-type', 'application/json')
    //   ->write(json_encode($json));
  }





}