<?php

//////////////////////////////////////////////
// Configuration ⤵
//////////////////////////////////////////////
$config['pageTitle'] = $liens['forgetPassword']['title'];
$config['metaDesc']  = '';
$config['keywords']  = '';
$config['metaRobot'] = 'none';


//////////////////////////////////////////////
// Controller ⤵
//////////////////////////////////////////////

if(isset($_POST['mail']) && !empty($_POST['mail'])){

  $mail = htmlspecialchars($_POST['mail']);
  $newPassword = generatorStrRandom(10);
  $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

  if($updtPassword = updtInDb(['passwordForget' => $newPasswordHash], DB_USERS, ['mail' => $mail])){

    $mailHtml = 'Voici votre nouveau mot de pass [newPassword] ne le perdez plus.';
    $mailText= 'Voici votre nouveau mot de pass [newPassword] ne le perdez plus.';
    $replace = ['[newPassword]' => $newPassword];

    mailSend($mail, 'Mot de pass', $mailHtml, $mailText, $replace);
    add_message('Un email vous a été envoyé', 'success');
    header('Location: '.$liens['forgetPassword']['url']);
    die();
  }
  else{
    $msgs[] = 'Si cette email est en base de donnée un mail lui sera envoyé';
  }
}

if(isset($_POST['mail']) && empty($_POST['mail'])){
  $msgs[] = 'Aucun mail n`a été renseigné;
}
