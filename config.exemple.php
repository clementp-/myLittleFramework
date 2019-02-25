<?php

// TODO: d'une façon générale, tous les noms de variables devraient être en EN et non pas FR

DEFINE("ENVIRONMENT", $_SERVER['SERVER_NAME'] == 'localhost' ? "DEV" : "PROD");
DEFINE("ROOT", ENVIRONMENT === "PROD" ? "https://my-website.fr/" : "http://localhost/myLittleFramwork/");


//////////////////////////////////////////////
// identifiant BDD
//////////////////////////////////////////////
if (ENVIRONMENT === "PROD") {
  // PRODUCTION
  $dbname = "";
  $dbhost = "";
  $dbuser = "";
  $dbpwd = "";
} else {
  // DEV
  $dbname = "";
  $dbhost = "";
  $dbuser = "";
  $dbpwd = "";
}
$bdd = array($dbname, $dbhost, $dbuser, $dbpwd);

//////////////////////////////////////////////
// Les tables de la BDD
//////////////////////////////////////////////
DEFINE("DB_USERS", "table_users"); // view file table.structure


//////////////////////////////////////////////
// Configuration du site
//////////////////////////////////////////////
$config = array();
$config['template'] = 'views/template.php';
$config['siteTitle'] = 'My Little Framwork';
$config['siteUrl'] = 'my-website.fr';
$config['pageTitle'] = '';
$config['metaRobot'] = 'All';
$config['metaDesc'] = '';
$config['keywords'] = '';
$config['mail'] = 'info@my-website.fr';
$config['imageMaxSize'] = 10485760; // = 10Mo en octés - 6291456 = 6Mo en octés
$config['imageExtention'] = ['jpg', 'gif', 'jpeg', 'png'];
$config['imagePath'] = 'upload/';


//////////////////////////////////////////////
// liens ['name' => 'Accueil', 'file' => 'index.php' , 'agree' => 'all'];
// agree = all, user (only), visitor (only), admin
//////////////////////////////////////////////

// Pas de majuscules ni de caractères spéciaux (accents) dans les urls
// Par convention on met plutôt les urls en EN
$liens = array();
$liens['index'] = array(
  "url" => ROOT."accueil",
  "title" => "Accueil",
  "file" => "index.php",
  "agree" => "all"
);
$liens['404'] = array(
  "url" => ROOT."page-inconnue",
  "title" => "Page inconnue",
  "file" => "404.php",
  "agree" => "all"
);
$liens['login'] = array(
  "url" => ROOT."connexion",
  "title" => "Connexion",
  "file" => "login.php",
  "agree" => "visitor"
);
$liens['logout'] = array(
  "url" => ROOT."deconnexion",
  "title" => "Déconnexion",
  "file" => "actions.php",
  "agree" => "user"
);
$liens['forgetPassword'] = array(
  "url" => ROOT."mot-de-passe-oublie",
  "title" => "Mot de passe oublié",
  "file" => "forgetPassword.php", // mauvaise traduction => forgotPassword.php
  "agree" => "user"
);
$liens['contact'] = array(
  "url" => ROOT."contactez-nous",
  "title" => "Contact",
  "file" => "contact.php",
  "agree" => "all"
);
$liens['account'] = array(
  "url" => ROOT."mon-compte",
  "title" => "Mon compte",
  "file" => "account.php",
  "agree" => "user"
);
