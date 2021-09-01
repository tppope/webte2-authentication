<?php
require_once "controllers/RegistrationController.php";
require_once "controllers/LoginController.php";
date_default_timezone_set("Europe/Bratislava");
$response =array();

session_start();
define('MYDIR','google-api-php-client--PHP8/');
require_once(MYDIR."vendor/autoload.php");

$redirect_uri = 'http://wt122.fei.stuba.sk/7243zadanie3webtech2/oauth.php';

$client = new Google_Client();
$client->setAuthConfig('../../configs/google-credentials.json');
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");

$service = new Google_Service_Oauth2($client);

if(isset($_GET['code'])){
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    $_SESSION['upload_token'] = $token;

    // redirect back to the example
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

// set the access token as part of the client
if (!empty($_SESSION['upload_token'])) {
    $client->setAccessToken($_SESSION['upload_token']);
    if ($client->isAccessTokenExpired()) {
        unset($_SESSION['upload_token']);
    }
}

if ($client->getAccessToken()) {
    //Get user profile data from google
    $UserProfile = $service->userinfo->get();
    if(!empty($UserProfile)){
        $controller = new LoginController();
        $userId = $controller->getUserId($UserProfile['email']);
        if($userId==false){
            $regController = new RegistrationController();
            $regController->performRegistration($UserProfile['given_name'],$UserProfile['family_name'],$UserProfile['email'],'google',null,$UserProfile['id'], null);
            $_SESSION["user_id"] = $controller->getUserId($UserProfile['email']);
            header('Location: account.html');
        }else{
            if($controller->hasPassword($userId) == false){
                $controller->setLoginTime($UserProfile['email'],'google');
                $_SESSION["user_id"] = $controller->getUserId($UserProfile['email']);
                header('Location: account.html');
            }
            else{
                $_SESSION["google_error"] = "already-used";
                unset($_SESSION['upload_token']);
                $client->revokeToken();
                header('Location: index.html');
            }

        }

    }else{
        $_SESSION["google_error"] = "unknown";
        unset($_SESSION['upload_token']);
        $client->revokeToken();
        header('Location: index.html');
    }
}


