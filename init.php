<?php $logpass=""; //FORMAT: md5(loginIMRANDpassword); ?>
<?php
define('DEBUG', FALSE);
define('DEMO', FALSE);

if(DEBUG) {
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
} else {
	ini_set('display_errors',0);
	ini_set('display_startup_errors',0);
	error_reporting(0);
}
$timezone='Europe/Moscow';
date_default_timezone_set($timezone);
/**
Запрос авторизации
*/
if($logpass!="") {
	if(!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="ImageRandomizer"');
	    header('HTTP/1.0 401 Unauthorized');
	    print "Authentification required!";
	    exit;
	} else {
		if(md5($_SERVER['PHP_AUTH_USER']."IMRAND".$_SERVER['PHP_AUTH_PW'])!=$logpass) {
			header('WWW-Authenticate: Basic realm="ImageRandomizer"');
	    	header('HTTP/1.0 401 Unauthorized');
			print 'Wrong login or password!';
			exit;
		}
	}
}
?>
@@include('class/RandomizerAPI.php')
@@include('class/RandImg.php')
@@include('class/Translation.php')
<?php
$translation=new Translation("ru");
RandomizerAPI::selfDiagnostics();
$api=new RandomizerAPI($_SERVER['REQUEST_METHOD'], $_POST, $_GET, $_SERVER['QUERY_STRING'], $translation);
?>
@@include('lang/ru.php')
@@include('lang/en.php')
<?php 
$result=$api->processRequest();
if($result!==false) {
	print $result;
	exit;
}
$translation->Begin(); ?>
@@include('iface/main.html')
<?php $translation->End(); ?>