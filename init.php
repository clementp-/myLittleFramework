<?php
session_start();

//start////////////////////////////////
// config
require_once('config.php');
//end//////////////////////////////////


//start////////////////////////////////
// Model
require_once('functions/general.php');
require_once('functions/requetesSql.php');
require_once('functions/admin.php');
require_once('functions/login.php');
require_once('functions/image.php');
//end//////////////////////////////////

//start////////////////////////////////
//Connexion a la BDD
try {
    $db = connect_db();
} catch(PDOException $e) {
     die('Erreur de connection à la base de données : '.$e->getMessage());
}
//end//////////////////////////////////

//start////////////////////////////////
//Initialisation des sessions et variables
$msgs = get_messages();

if(!isset($_SESSION['userId'])) {
    $_SESSION['userId'] = false;
}
