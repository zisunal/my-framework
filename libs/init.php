<?php ob_start() ?>
<?php require_once("conf.php"); ?>
<?php
date_default_timezone_set(TM_ZONE);
ini_set('memory_limit', '4G');
ini_set('max_execution_time', '1500');
require_once("Common.php"); 
require_once("Db.php"); 
require_once("User.php"); 
require_once("Session.php"); 
require_once("Lan.php");
require_once("Transl.php");
require_once("functions.php"); 
include_config("core");
const _APP = new App();
include_config("models");
include_config("controllers");
include_config("routes");
_APP->run();
require_once("App_info.php");
?>