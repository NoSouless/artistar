<?php
global $db;

define("PROJETO", "artistar");

define("ROOT", 'http://'.$_SERVER['HTTP_HOST']."/".PROJETO);
define("KEY", "Art1st4r-C4t-S0u1");

$host = $_SERVER['HTTP_HOST'];
$raizSetup = dirname( __FILE__, 3)."/setup";

if (file_exists("$raizSetup/$host.json")) {
    $setup = file_get_contents("$raizSetup/$host.json");
} else {
    $setup = file_get_contents("$raizSetup/default.json");
}

$setup = json_decode($setup, true);


$host = $setup['db']['host'];
$db = $setup['db']['name'];
$user = $setup['db']['user'];
$password = $setup['db']['pass'];
$port = $setup['db']['port'];
$timezone = $setup['db']['timezone'];
$recaptchaSiteKey = $setup['recaptcha']['site_key'];
$recaptchaSecretKey = $setup['recaptcha']['secret_key'];

//Set php timezone
date_default_timezone_set("America/Sao_Paulo");

define("EMAIL_CREDENTIALS", $setup['email']);
define("STORAGE_CONFIG", $setup['storage']);
define('RECAPTCHA_SITE_KEY', trim((string) $recaptchaSiteKey));
define('RECAPTCHA_SECRET_KEY', trim((string) $recaptchaSecretKey));
define('RECAPTCHA_ENABLED', RECAPTCHA_SITE_KEY !== '' && RECAPTCHA_SECRET_KEY !== '' && function_exists('curl_init'));

try{
    $db = new PDO("mysql:host=$host;dbname=$db;port=$port;charset=utf8;", $user, $password, array(
        PDO::MYSQL_ATTR_FOUND_ROWS => true,
        PDO::ATTR_TIMEOUT => 60,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$timezone}'"
    ));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    die($e->getMessage());
}


function url($path)
{
    if ($path) {
        // return ROOT . "/{$path}";
        return "/{$path}";
    }
    return "/";
}

include 'Functions/function.php';
