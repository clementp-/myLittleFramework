<?php

//////////////////////////////////////////////
// identifiant BDD
//////////////////////////////////////////////
$bdd = [];
if ($_SERVER['SERVER_NAME'] != 'localhost'){
  $bdd['dbname']        = ''; // database name     - production
  $bdd['dbhost']        = ''; // database host     - production
  $bdd['dbuser']        = ''; // database user     - production
  $bdd['dbpassword']    = ''; // database password - production
}
else {
  $bdd['dbname']        = ''; // database name     - localhost
  $bdd['dbhost']        = ''; // database host     - localhost - 'localhost;charset=utf8'
  $bdd['dbuser']        = ''; // database user     - localhost - 'root'
  $bdd['dbpassword']    = ''; // database password - localhost
}


//////////////////////////////////////////////
// Constante declaration
//////////////////////////////////////////////
if ($_SERVER['SERVER_NAME'] != 'localhost'){
  define ('ROOT', 'https://my-website.fr/');
}
else {
  define ('ROOT', 'http://localhost/myLittleFramwork/');
}


//////////////////////////////////////////////
// Les tables de la BDD
//////////////////////////////////////////////
define ('DB_USERS', 'table_users'); // view file table.structure


//////////////////////////////////////////////
// Configuration du site
//////////////////////////////////////////////
$config = [];
$config['template'] 				          = 'views/template.php';
$config['siteTitle'] 				          = 'My Little Framwork';
$config['siteUrl'] 				            = 'my-website.fr';
$config['pageTitle']	                = '';
$config['metaRobot']	                = 'All';
$config['metaDesc']                   = '';
$config['keywords']                   = '';
$config['mail']	                      = 'info@my-website.fr';
$config['imageMaxSize']               = 10485760; // = 10Mo en octer - 6291456 = 6Mo en octer
$config['imageExtention']             = ['jpg', 'gif', 'jpeg', 'png'];
$config['imagePath']                  = 'upload/';


//////////////////////////////////////////////
// liens ['name' => 'Accueil', 'file' => 'index.php' , 'agree' => 'all'];
// agree = all, user (only), visitor (only), admin
//////////////////////////////////////////////
$liens = [];
$liens['index']                = ['url' =>  ROOT.'Accueil',                      'title' => 'Accueil',                           'file' => 'index.php' ,              'agree' => 'all'];
$liens['404']                  = ['url' =>  ROOT.'Page-inconnue',                'title' => 'Page inconnue',                     'file' => '404.php' ,                'agree' => 'all'];
$liens['login']                = ['url' =>  ROOT.'Connexion',                    'title' => 'Connexion',                         'file' => 'login.php' ,              'agree' => 'visitor'];
$liens['logout']               = ['url' =>  ROOT.'Deconnection',                 'title' => 'Déconnection',                      'file' => 'actions.php' ,            'agree' => 'user'];
$liens['forgetPassword']       = ['url' =>  ROOT.'Mot-de-passe-oublie',          'title' => 'Mot de passe oublié',               'file' => 'forgetPassword.php' ,     'agree' => 'user'];
$liens['contact']              = ['url' =>  ROOT.'Contactez-nous',               'title' => 'Contact',                           'file' => 'contact.php' ,            'agree' => 'all'];
$liens['account']              = ['url' =>  ROOT.'Mon-compte',                   'title' => 'Mon compte',                        'file' => 'account.php' ,            'agree' => 'user'];
