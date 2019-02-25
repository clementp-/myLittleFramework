<?php

//start////////////////////////////////
// redirection http to https
if ($_SERVER['SERVER_NAME'] != 'localhost'){
  if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
  }
}
//end//////////////////////////////////

//start////////////////////////////////
// initialisation
require_once('init.php');
//end//////////////////////////////////

//start////////////////////////////////
//Routage des page
// récuperation des info de url
$infosRoot = rootPage($_GET);
//end//////////////////////////////////

//start////////////////////////////////
//Securiter
//verification des droit des visiteurs
$checkAgree = checkAgree($infosRoot['page']);
if($checkAgree){
  header('Location: '.ROOT.$liens[$checkAgree]['title']);
}
//end//////////////////////////////////

//start////////////////////////////////
// Controler
require_once($infosRoot['controller']);
//end//////////////////////////////////

//start////////////////////////////////
// views
ob_start();
require_once($infosRoot['view']);
$contents_template = ob_get_clean();
require_once($config['template']);
//end//////////////////////////////////
