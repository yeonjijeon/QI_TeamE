<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class UserController extends BaseController 
{
  protected $logger;
  protected $UserModel;
  protected $view;

  public function __construct($logger, $UserModel, $view) 
  {
      $this->logger = $logger;
      $this->UserModel = $UserModel;
      $this->view = $view;
  }


public function sign_up(Request $request, Response $response, $args) {

    $this->view->render($response, 'register.twig');

            return $response;
}

public function sign_in(Request $request, Response $response, $args) {
         
    $this->view->render($response, 'signin.twig');

         return $response;
       
}

public function forget_pwd(Request $request, Response $response, $args) {
        $this->view->render($response, 'forget_pwd.twig');
 
              return $response;
}

public function change_pwd(Request $request, Response $response, $args) {
    $this->view->render($response, 'change_pwd.twig');

          return $response;
}

public function change_pwd2(Request $request, Response $response, $args) {
  $this->view->render($response, 'change_pwd2.twig');

        return $response;
}

public function id_cancellation(Request $request, Response $response, $args) {
    $this->view->render($response, 'id_cancellation.twig');

          return $response;
}

public function mypage(Request $request, Response $response, $args) {
  $this->view->render($response, 'mypage.twig');

        return $response;
}



//---------------------------WEB PROCESS----------------------------------


// SIGN UP
public function sign_up_process(Request $request, Response $response, $args) 
{ 
  $user=[];
  try {
    $user['EMAIL'] = $_POST["Email"];
    $user['HASHED_PW'] = password_hash($_POST["Password"], PASSWORD_DEFAULT);
    $user['FNAME'] = $_POST["FirstName"];
    $user['LNAME'] = $_POST["LastName"];
    $user['PHONE'] =  $_POST["PhoneNumber"];
    $user['IS_ACTIVE'] = 0;
    $user['IS_SIGNIN'] = 0;
        
    $temp =  password_hash(strval(mt_rand()), PASSWORD_DEFAULT);
    $user['NONCE'] = str_replace(array('\\', '/', '.'), 'b', $temp);

    // PASSWORD와 CONFIRMPASSWORD 불일치
    if (strcmp($_POST['Password'], $_POST['ConfirmPassword']) !== 0) {
      $this->view->render(
      $response,
      'register.twig',
      ['error_message' => 'Please check password', 'result_code' => 1]
      );
      return $response;
    }


    // 이메일 중복 검사
    if (count($this->UserModel->duplicate_checking($user)) == 1) {   // 존재

        $this->view->render(
        $response,
        'register.twig',
        ['error_message' => 'E-mail address already exist.', 'result_code' => 1] //0으로 나중에 바꾸자
        );
        return $response;
    } else {   // 존재하지 않음
      $query_results =  $this->UserModel->insert_user($user);   // 사용자 INSERT
  
      if ($query_results) { // INSERT 완료하면 메일 전송
        $mail = new PHPMailer(true);                             
        try {
            //Server settings
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'iotteame2019@gmail.com';                 // SMTP username
            $mail->Password = 'teame!ucsd';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;   

            //Recipients
            $mail->setFrom('jeonyeonji1028@gmail.com', 'HEART DOG');
            $mail->addAddress($user['EMAIL'], 'HEART DOG');     // Add a recipient
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Welcome to HEART DOG!';
            $mail->Body    = 'Hi there!
            <br>We are happy that you are almost HEART DOG member!
            <br>If you want to activate your account, Click the below link and Finish the Sign-up!
            <br>http://teame-iot.calit2.net/heartdog/signin/activate/' . $user['NONCE'];

            $mail->send();
            return $this->view->render($response, 'register.twig', ['success_message'
            => 'E-mail has been sent. Please check your E-mail!', 'result_code' => 0]);   //메일 전송 성공
            } catch (Exception $e) {   // 예외 발생
            $this->view->render($response, 'register.twig', ['error_message'
            => $e->getMessage(), 'result_code' => 1]);  //실패 
            return $response;
            }
          } 
          else {
            $this->view->render($response, 'register.twig', ['error_message'
            => 'This is Communication Error. Try again please.', 'result_code' => 1]); //3..???으로 바꾸자
            return $response;
          }
        }
      } catch (PDOException $e) { // insert 실패 (커뮤니케이션 오류)
          $this->view->render($response, 'register.twig', ['error_message'
          => 'PDOException. Try again please.', 'result_code' => 1]);
          return $response;
    }
}

public function account_activate(Request $request, Response $response, $args) {
  try {
        $email = $this->UserModel->select_email($args['nonce']); //nonce를 통해 email 가져옴

        if ($email['count'] == 1) {   
          if ($this->UserModel->update_active($email)==0) {
            $this->view->render($response, 'signin.twig', ['success_message'
            => 'Congratulation! You can start HEART DOG, right now!', 'result_code' => 0]);
            return $response;
          } else { //사용자 정보 저장 실패
            $this->view->render($response, 'signin.twig', ['error_message'
            => 'Insert User Information query is failed.', 'result_code' => 1]);  //통신오류?
            return $response;
          }
          } else {
                // $this->view->render($response,'signin.twig',['error_message'
                // =>'Select user information from temp table query is failed']);
                // return $response;
        }
      } catch (PDOException $e) {
        $this->view->render($response, 'signin.twig', ['error_message'
        => 'PDOException error', 'result_code' => 1]);
        return $response;
    }
}

public function index(Request $request, Response $response, $args) {
  $this->view->render($response, 'index.twig');
  return $response;
}

public function user_index(Request $request, Response $response, $args) {
  $this->view->render($response, 'user_index.twig');
  return $response;
}


public function sign_in_process(Request $request, Response $response, $args) {
  $json = [];
  $user = [];

  try {
    if (isset($_POST['Email'])||isset($_POST['Password']) )  
    {
      $user['Email'] =  $_POST['Email'];
      $user['Password'] =  $_POST['Password'];
    }
    else {
      $json = ['error_message' => 'Enter E-mail address and Password please!','result_code'=>1];
      return $response->withHeader('Content-type', 'application/json')
        ->write(json_encode($json));
    }

    $results = $this->UserModel->select_USN_PW($user);

    if ($results) {
      if (password_verify($user['Password'], $results['HASHED_PW'])) {
        $user['USN'] = $results['USN'];
        $results['IS_SIGNIN'] = 1;    

        if ($this->UserModel->update_user_set_IS_SIGNIN($results) == 0) {
          $json = [
            'success_message' => 'Sign-In is completed.', 'USN' => $user['USN'], 
            // 'permission' => $user['permission'], 
            'result_code' => 0
          ];
          return $response->withHeader('Content-type', 'application/json')
            ->write(json_encode($results));
        } else {
          $json = ['error_message' => 'Communication error', 'result_code' => 3];
          return $response->withHeader('Content-type', 'application/json')
            ->write(json_encode($results));
        }
          } else {
            $json = ['error_message' => 'Password is wrong.', 'result_code' => 2];
            return $response->withHeader('Content-type', 'application/json')
            ->write(json_encode($results));
            }
              } else {
                $json = ['error_message' => 'E-mail address doesn\'t exist.', 'result_code' => 1];
                return $response->withHeader('Content-type', 'application/json')
            ->write(json_encode($results));
              }
            }
          catch (PDOException $e) {
            $json = ['error_message' => 'Some errors occurred during sign-in.', 'result_code' => 4];
            return $response->withHeader('Content-type', 'application/json')
            ->write(json_encode($results));
          }
        }
                  
public function sign_out_process(Request $request, Response $response, $args) {
  $json = [];
  $user = [];
  
  try {
    if (isset($_POST['USN'])) {
      $user['USN'] =  $_POST['USN'];
      $user['IS_SIGNIN'] = 0;
    } else{
      $json = ['error_message' => 'Please, Sign-in first.', 'result_code' => 1];
      return $response->withJson($json);
    }
    if ($this->UserModel->update_user_set_IS_SIGNIN($user) == 0) {
      $json = ['success_message' => 'Sign out', 'result_code' => 0];
      return $response->withJson($json);
      } else {
        $json = ['error_message' => 'Sign-out is failed ', 'result_code' => 1];
        return $response->withJson($json);
      }
    }catch (PDOException $e) {
        $json = ['error_message' => 'Some errors occurred during sign-out', 'result_code' => 1];
        return $response->withJson($json);
      }
  }


  

public function id_cancellation_process(Request $request, Response $response, $args) 
  {
    $json = [];
    $user = [];
    try {

      if (isset($_POST['password'])) {
        $user['password'] =  $_POST['password'];
        $user['USN'] =  $_POST['USN'];
      } else {
        $json = ['error_message' => 'There is no password or Please, Sign-in first.', 'result_code' => 1];
        return $response->withJson($json);
      }

      $results = $this->UserModel->select_HASHED_PW($user);
      

      if ($results) {

        if (password_verify($user['password'], $results['HASHED_PW'])) {
          $user['IS_ACTIVE'] = 2;  // set isActive flag to 0. it is mean that user account is cancelled.
          
          if ($this->UserModel->update_user_set_IS_ACTIVE($user) == 0) { //update database 

            $json = [
              'success_message' => 'ID cancellation is completed.', 'result_code' => 0
            ];
            return $response->withJson($json);
          } else {

            $json = [
              'error_message' => 'Some errors occurred during ID Cancellation.', 'result_code' => 1
            ];
            return $response->withJson($json);
          }
        } else {
          $json = [
            'error_message' => 'ID cancellation is Failed, Password is wrong.', 'result_code' => 1
          ];
          return $response->withJson($json);
        }
      } else {

        $json = [
          'error_message' => 'Some errors occurred during ID Cancellation.', 'result_code' => 1
        ];
        return $response->withJson($json);
      }
    } catch (PDOException $e) {
      $json = ['error_message' => 'Some errors occurred during ID Cancellation.', 'result_code' => 1];
      return $response->withJson($json);
    }
  }


public function change_pwd_process(Request $request, Response $response, $args) {
  $json = [];
  $user = [];
  try {
    if (
        isset($_POST['USN']) && isset($_POST['password']) &&
        isset($_POST['new_password']) && isset($_POST['confirm_new_password'])
        && ($_POST['new_password'] == $_POST['confirm_new_password'])
      ) {
        $user['Password'] =  $_POST['password'];
        $user['USN'] =  $_POST['USN'];
      } else {
        $json = ['error_message' => 'No information received.', 'result_code' => 1];
        return $response->withJson($json);
      }
      $results = $this->UserModel->select_HASHED_PW($user);

      if ($results) {
        if (password_verify($user['Password'], $results['HASHED_PW'])) {
          $user['new_password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

          if ($this->UserModel->update_user_set_password($user) == 0) {

            $json = [
              'success_message' => 'Password change is completed.', 'result_code' => 0
            ];
            return $response->withJson($json);
          } else {

            $json = [
              'error_message' => 'Some errors occurred during password change.', 'result_code' => 1
            ];
            return $response->withJson($json);
          }
        } else {
          $json = [
            'error_message' => 'Password Change is Failed, Password is wrong.', 'result_code' => 1
          ];
          return $response->withJson($json);
        }
      } else {

        $json = [
          'error_message' => 'Some errors occurred during password change.', 'result_code' => 1
        ];
        return $response->withJson($json);
      }
    } catch (PDOException $e) {
      $json = ['error_message' => 'Some errors occurred during password change.', 'result_code' => 1];
      return $response->withJson($json);
    }
}


public function change_pwd2_process(Request $request, Response $response, $args) {
  $json = [];
  $user = [];
  try {
    if (isset($_POST['new_password']) || isset($_POST['confirm_new_password'])) {
      $user['new_password'] =  $_POST['new_password'];
      $user['confirm_new_password'] =  $_POST['confirm_new_password'];
    } else {
      $json = ['error_message' => 'No information received.', 'result_code' => 1];
      return $response->withJson($json);
    }


    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $parts = parse_url($url);

    $nonce = substr($parts['path'], 60);

    $results = $this->UserModel->select_USN_by_nonce($nonce);
    if (count($results) == 1) {  // Only one account is exist with the parsed nonce
      $user['USN'] = $results[0]['USN'];
      $user['HASHED_PW'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT); //hasing password
      $user['NONCE'] =  NULL;   //update auth_code 

      if ($this->UserModel->update_user_set_password_NONCE($user)==0) {
        $json = ['success_message' => 'Forgotten password change is completed. ', 'result_code' => 0];
        return $response->withJson($json);
      } else {
        $json = ['error_message' => 'Some errors occurred during forgotten password change1.', 'result_code' => 1];
        return $response->withJson($json);
      }
    } else {
      $json = ['error_message' => 'Some errors occurred during forgotten password change2.', 'result_code' => 1];
      return $response->withJson($json);
    }
  } catch (PDOException $e) {
    $json = ['error_message' => 'Some errors occurred during forgotten password change3.', 'result_code' => 1];
    return $response->withJson($json);
  }
}


public function resetpwd(Request $request, Response $response, $args) {
  try {
        $email = $this->UserModel->select_email($args['nonce']); //nonce를 통해 email 가져옴

        if ($email['count'] == 1) {   
          if ($this->UserModel->update_active($email)==0) {
            header("Location: http://teame-iot.calit2.net/heartdog/change_pwd2");
            exit();
          } else { //사용자 정보 저장 실패
            header("Location: http://teame-iot.calit2.net/heartdog/signin");
            exit();
          }
          } else {
                // $this->view->render($response,'signin.twig',['error_message'
                // =>'Select user information from temp table query is failed']);
                // return $response;
        }
      } catch (PDOException $e) {
        $this->view->render($response, 'change_pwd2.twig', ['error_message'
        => 'PDOException error', 'result_code' => 1]);
    }
}








public function forget_pwd_process(Request $request, Response $response, $args) {

  $json = [];
  $user = [];
  try {

    if (isset($_POST['Email'])) {
      $user['EMAIL'] =  $_POST['Email'];
    } else {
      $json = ['error_message' => 'There is no E-mail.', 'result_code' => 1];
      return $response->withJson($json);
    }

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
            $mail->Body    = 'Hello! This is HotDog account administration team.
            <br>We attached a link to help you reset your password. Please enter the below link and set a new password.
            <br>http://teame-iot.calit2.net/account/resetpwd/' . $user['NONCE'];

          $mail->send();
          $this->view->render($response, 'change_pwd2.twig', ['success_message'
          => 'Authentication-mail has been sent. Please, check your E-mail.', 'result_code' => 0]);
          return $response;
        } catch (Exception $e) {

          $json = [
            'error_message' => 'Authentication-mail could not be sent. Try again.', 'result_code' => 1
          ];
          return $response->withJson($json);
        } // end of catch statement 
      } else {

        $json = [
          'error_message' => 'Some errors occurred during forgotten password change.', 'result_code' => 1
        ];
        return $response->withJson($json);
      }
    } else {  // There is no user information or There is more than two user information.
      $json = [
        'error_message' => 'Some errors occurred during forgotten password change.', 'result_code' => 1
      ];
      return $response->withJson($json);
    }
  } catch (PDOException $e) {
    $json = ['error_message' => 'Some errors occurred during forgotten password change.', 'result_code' => 1];
    return $response->withJson($json);
  }


  
}







//---------------------------APP PROCESS----------------------------------
          
public function app_sign_up_process(Request $request, Response $response, $args) {
  $cp_data_json = file_get_contents('php://input');
  $cp_data = json_decode($cp_data_json);
}

          



      
}