<?php
require_once "controllers/RegistrationController.php";
require_once "controllers/LoginController.php";
require_once "controllers/AccountController.php";
require_once 'GoogleAuthenticator/GoogleAuthenticator-master/PHPGangsta/GoogleAuthenticator.php';


header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set("Europe/Bratislava");
$response =array();
if ($_GET["type"] == "reg") {
    session_start();
    $ga = new PHPGangsta_GoogleAuthenticator();
    $secret = $_SESSION['secret-code'];

    $controller = new RegistrationController();
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $name = trim($_POST["name"]);
    $surname = trim($_POST["surname"]);
    $email = trim($_POST["email"]);
    $code = trim($_POST["code"]);
    try {
        if($ga->verifyCode($secret, $code)!=1)
            throw new Exception("2FA-error");
        if (strstr($email, '@') =="@stuba.sk")
            throw new Exception("domain");
        $controller->performRegistration($name, $surname, $email, 'own', $password, null, $secret);
        $response = array(
            "status" => "success",
            "error" => false
        );
        echo json_encode($response);
    } catch (Exception $e) {
        $response = array(
            "status" => "failed",
            "error" => true,
            "message" => $e->getMessage(),
        );
        echo json_encode($response);
    }
}
elseif($_GET["type"]=="login"){
    session_start();
    $controller = new LoginController();
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    try {
        if (strstr($email, '@') =="@stuba.sk")
            throw new Exception("domain");
        $login = $controller->loginUser($email,$password);
        $_SESSION["email"] = $email;

        $response = array(
            "status" => "success",
            "error" => false,
            "login"=>$login,
        );
        echo json_encode($response);
    } catch (Exception $e) {
        $response = array(
            "status" => "failed",
            "error" => true,
            "message" => $e->getMessage()
        );
        echo json_encode($response);
    }
}
elseif($_GET["type"]=="2FA"){
    session_start();
    $controller = new LoginController();
    $ga = new PHPGangsta_GoogleAuthenticator();
    $code = trim($_POST["code"]);
    try {
        if (!isset($_SESSION["email"]))
            throw new Exception("first-login");

        $email = $_SESSION["email"];
        $secret = $controller->get2FaCode($email);
        if($ga->verifyCode($secret, $code)!=1)
            throw new Exception("2FA-error");

        unset($_SESSION["email"]);

        $controller->setLoginTime($email, 'own');
        $_SESSION["user_id"] = $controller->getUserId($email);

        $response = array(
            "status" => "success",
            "error" => false,
        );
        echo json_encode($response);
    } catch (Exception $e) {
        $response = array(
            "status" => "failed",
            "error" => true,
            "message" => $e->getMessage()
        );
        echo json_encode($response);
    }
}
elseif ($_GET["type"]=="get2FA"){
    session_start();
    $websiteTitle = 'webtech2-Autentifikácia';
    $ga = new PHPGangsta_GoogleAuthenticator();
    try {
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($websiteTitle, $secret);
        $_SESSION["secret-code"] = $secret;
        $response = array(
            "status" => "success",
            "error" => false,
            "qrCode"=>$qrCodeUrl,
        );
        echo json_encode($response);
    } catch (Exception $e) {
        $response = array(
            "status" => "failed",
            "error" => true,
            "message" => $e->getMessage()
        );
        echo json_encode($response);
    }
}
elseif($_GET["type"]=="loginCheck"){
    session_start();
    if (isset($_SESSION["user_id"])) {
        $response = array(
            "status" => "success",
            "error" => false,
            "login"=>true,
        );
        echo json_encode($response);
    }
    else{
        $response = array(
            "status" => "success",
            "error" => false,
            "login"=>false,
        );
        echo json_encode($response);
    }
}
elseif($_GET["type"]=="emailSetCheck"){
    session_start();
    if (isset($_SESSION["email"])) {
        $response = array(
            "status" => "success",
            "error" => false,
            "emailSet"=>true,
        );
        echo json_encode($response);
    }
    else{
        $response = array(
            "status" => "success",
            "error" => false,
            "emailSet"=>false,
        );
        echo json_encode($response);
    }
}
elseif ($_GET["type"]=="acc"){

    $controller = new AccountController();
    $user = $controller->getUser();
    if ($user === null){
        $response = array(
            "status" => "failed",
            "error" => false,
            "user"=>$user,
        );
        echo json_encode($response);
    }
    else{
        $user->setLoginType($controller->getLoginType($user->getId()));
        $response = array(
            "status" => "success",
            "error" => false,
            "user"=>$user,
        );
        echo json_encode($response);
    }
}
elseif ($_GET["type"]=="logout"){
    session_start();
    define('MYDIR','google-api-php-client--PHP8/');
    require_once(MYDIR."vendor/autoload.php");
    $client = new Google_Client();
    $client->setAuthConfig('../../configs/google-credentials.json');

    try {
        unset($_SESSION["user_id"]);
        unset($_SESSION['upload_token']);
        $client->revokeToken();
        $response = array(
            "status" => "success",
            "error" => false,
        );
        echo json_encode($response);
    }
    catch (Exception $exception){
        $response = array(
            "status" => "failed",
            "error" => true,
            "message"=>$exception->getMessage(),
        );
        echo json_encode($response);
    }
}
elseif ($_GET["type"]=="ownHistory"){
    $controller = new AccountController();
    try {
        $ownHistory = $controller->getHistoryList();
        $response = array(
            "status" => "success",
            "error" => false,
            "listHistory"=>$ownHistory,
        );
        echo json_encode($response);
    }
    catch (Exception $exception){
        $response = array(
            "status" => "failed",
            "error" => true,
            "message"=>$exception->getMessage(),
        );
        echo json_encode($response);
    }
}
elseif ($_GET["type"]=="loginStats"){
    $controller = new AccountController();
    try {
        $loginStats = $controller->getLoginStats();
        $response = array(
            "status" => "success",
            "error" => false,
            "loginStats"=>$loginStats,
        );
        echo json_encode($response);
    }
    catch (Exception $exception){
        $response = array(
            "status" => "failed",
            "error" => true,
            "message"=>$exception->getMessage(),
        );
        echo json_encode($response);
    }
}
elseif ($_GET["type"]=="ldap"){
    $controller = new LoginController();
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $domain = strstr($email, '@');
    $uid = strstr($email, '@', true);
    $dn = 'ou=People, DC=stuba, DC=sk';
    $ldaprdn = "uid=$uid, $dn";
    $ldapconn = ldap_connect("ldap.stuba.sk");

    try {
        if ($domain!="@stuba.sk")
            throw new Exception("domain");
        if (!ldap_connect())
            throw new Exception("ldap connect failed");
        $set = ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION,3);
        $ldapbind = ldap_bind($ldapconn,$ldaprdn,$password);
        if ($ldapbind){
            $results = ldap_search($ldapconn,$dn,"uid=".$uid,array("givenname","surname"),0,1);
            $info = ldap_get_entries($ldapconn,$results);
            if($controller->getUserId($email)==false){
                $regController = new RegistrationController();
                $regController->performRegistration($info[0]['givenname'][0],$info[0]['sn'][0],$email,'ldap','ldap_password', null, null);
            }else
                $controller->setLoginTime($email,'ldap');
            session_start();
            $_SESSION["user_id"] = $controller->getUserId($email);
        }

        $response = array(
            "status" => "success",
            "error" => false,
            "ldapStatus"=>$ldapbind,
        );
        echo json_encode($response);
    }
    catch (Exception $exception){
        $response = array(
            "status" => "failed",
            "error" => true,
            "message"=>$exception->getMessage(),
        );
        echo json_encode($response);
    }

    ldap_unbind($ldapconn);
}
elseif ($_GET["type"]=="google"){
    session_start();
    define('MYDIR','google-api-php-client--PHP8/');
    require_once(MYDIR."vendor/autoload.php");

    $redirect_uri = 'http://wt122.fei.stuba.sk/7243zadanie3webtech2/oauth.php';

    $client = new Google_Client();
    $client->setAuthConfig('../../configs/google-credentials.json');
    $client->setRedirectUri($redirect_uri);
    $client->addScope("email");
    $client->addScope("profile");
    try {
        $response = array(
            "status" => "success",
            "error" => false,
            "googleLink"=>filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL),
            "googleStatus"=>$_SESSION["google_error"],
        );
        echo json_encode($response);
    }
    catch (Exception $exception){
        $response = array(
            "status" => "failed",
            "error" => true,
            "message"=>$exception->getMessage(),
            "googleStatus"=>$_SESSION["google_error"],
        );
        echo json_encode($response);
    }
    unset($_SESSION["google_error"]);
}

