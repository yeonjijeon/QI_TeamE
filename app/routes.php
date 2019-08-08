<?php
// Routes

$app->get('/', 'App\Controller\UserController:index')
    ->setName('homepage');


// rending pages

$app->get('/heartdog/user_index', 'App\Controller\UserController:user_index')
    ->setName('idcancellationpage');

$app->get('/heartdog/signin', 'App\Controller\UserController:sign_in')
    ->setName('signinpage');

$app->get('/heartdog/signup', 'App\Controller\UserController:sign_up')
    ->setName('signuppage');

$app->get('/heartdog/change_pwd', 'App\Controller\UserController:change_pwd')
->setName('changepwdpage');

$app->get('/heartdog/forget_pwd', 'App\Controller\UserController:forget_pwd')
    ->setName('forgetpwdpage');

$app->get('/heartdog/id_cancellation', 'App\Controller\UserController:id_cancellation')
    ->setName('idcancellationpage');

$app->get('/heartdog/mypage', 'App\Controller\UserController:mypage')
    ->setName('mypage');

$app->get('/heartdog/change_pwd2', 'App\Controller\UserController:change_pwd2')
    ->setName('change_pwd2');




$app->get('/heartdog/signin/activate/{nonce}', 'App\Controller\UserController:account_activate')
    ->setName('accountactivate');

$app->post('/heartdog/signin/process', 'App\Controller\UserController:sign_in_process')
    ->setName('signinprocess');

$app->post('/heartdog/signup/process', 'App\Controller\UserController:sign_up_process')
    ->setName('signupprocess');

$app->post('/heartdog/signout/process', 'App\Controller\UserController:sign_out_process')
    ->setName('signoutprocess');

$app->post('/heartdog/duplicatecheck', 'App\Controller\UserController:duplicate_checking')
    ->setName('duplicatecheck');


$app->post('/heartdog/id_cancellation/process', 'App\Controller\UserController:id_cancellation_process')
->setName('idcancellationprocess');

$app->post('/heartdog/change_pwd/process', 'App\Controller\UserController:change_pwd_process')
    ->setName('changepwdprocess');

$app->post('/heartdog/change_pwd2/process', 'App\Controller\UserController:change_pwd2_process')
    ->setName('changepwd2process');

$app->post('/heartdog/forget_pwd/process', 'App\Controller\UserController:forget_pwd_process')
    ->setName('forgetpwdprocess');

$app->get('/account/resetpwd/{nonce}', 'App\Controller\UserController:resetpwd')
    ->setName('accountreset');



// APP

$app->post('/heartdog/app/signup', 'App\Controller\AppController:app_sign_up_process')
    ->setName('appsignup');

$app->post('/heartdog/app/signin', 'App\Controller\AppController:app_sign_in_process')
    ->setName('appsignin');

$app->post('/heartdog/app/signout', 'App\Controller\AppController:app_sign_out_process')
    ->setName('appsignout');

$app->post('/heartdog/app/account/actiavte/{nonce}', 'App\Controller\AppController:app_account_activate')
    ->setName('appaccountactivate');

    
$app->post('/heartdog/app/id_cancellation', 'App\Controller\AppController:app_ID_cancellation_process')
    ->setName('appidcancellation');

$app->post('/heartdog/app/change_pwd', 'App\Controller\AppController:app_change_pwd_process')
    ->setName('appchangepwd');

$app->post('/heartdog/app/forget_pw', 'App\Controller\AppController:app_forget_pw_process')
    ->setName('forgottonpwchange');





//--------------SENSOR CONTROLLER-----------------------------------------------------------------------


// $app->get('/sensor/listview', 'App\Controller\SensorController:sensor_list_view')
//     ->setName('sensorlistview');



$app->post('/sensor/userlistview/process', 'App\Controller\SensorController:sensor_userlist_view_process')
->setName('sensorlisttview');

    
// APP

$app->post('/heartdog/sensor/app/registration', 'App\Controller\SensorController:app_sensor_register')
    ->setName('appsensorregister');


$app->post('/heartdog/sensor/app/deregistration', 'App\Controller\SensorController:app_sensor_deregister')
    ->setName('appsensorderegister');


$app->post('/heartdog/sensor/app/listview', 'App\Controller\SensorController:app_sensor_list_view')
    ->setName('appsensorlistview');
    
    
//--------------DATA CONTROLLER-----------------------------------------------------------------------


$app->post('/heartdog/airquality/transfer', 'App\Controller\DataController:airquality_transfer_process')
    ->setName('datatransfer');

    
$app->post('/heartdog/heartrate/transfer', 'App\Controller\DataController:heartrate_transfer_process')
->setName('datatransfer');


$app->post('/heartdog/airquality/get', 'App\Controller\DataController:get_airquality_process')
    ->setName('getairquality');

$app->post('/heartdog/heartrate/get', 'App\Controller\DataController:get_heartrate_proces')
    ->setName('getairquality');


