<?php

//////////////////////////////////////////////
// Configuration ⤵
//////////////////////////////////////////////
$config['pageTitle'] = $liens['404']['title'];
$config['metaDesc']  = '';
$config['keywords']  = '';
$config['metaRobot'] = 'none';


//////////////////////////////////////////////
// Controller ⤵
//////////////////////////////////////////////
$mail = '';

if(isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['email']) && !empty($_POST['password'])){

  $mail       = htmlspecialchars($_POST['email']);
  $password   = htmlspecialchars($_POST['password']);

  if($user = login($mail, $password)){
    $_SESSION['userId'] = $user['id'];
    header('Location: '.$liens['account']['url']);
    die();
  }
  else{
    $msgs[] = ['Erreur de connection', 'error'];
  }
}

if(isset($_POST['mail']) && empty($_POST['mail'])){
  $msgs[] = ['Le champs Couriel est vide', 'error'];
}

if(isset($_POST['password']) && empty($_POST['password'])){
  $msgs[] = ['Le champs Couriel est vide', 'error'];
}
