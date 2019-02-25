<?php

/**
* Cette fonction verifie si un email existe dans la BDD
*
* @param string $mail
* @return true ou false
*/
function verifMail($mail){
  global $db;

  $mail = trim($mail);
  $mail = mb_strtolower($mail);
  $mail = preg_replace('# #','',$mail); // espace insecable
  $mail = preg_replace('# #','',$mail); // espace
  $mail = preg_replace('#   #','',$mail); // tab

  $req = $db->prepare('SELECT login FROM '.DB_USERS.' WHERE login = :mail');
  $req->bindValue(':mail', htmlspecialchars($mail), PDO::PARAM_STR);
  $req->execute();
  $mail = $req->fetchColumn();

  if($mail) return true;

  return  false;
}

/**
* Cette fonction verifie si un login correspond a un mdp
*
* @param string $login
* @param string $pass
* @return true ou false
*/
function login($mail, $password){
  global $db, $config;

  $mail = mb_strtolower(trim($mail));

  $user = getInDb(['id', 'password', 'passwordForget'], DB_USERS, ['mail' => $mail], false, '', 'fetch');

  if(password_verify($password, $user['password']))               return $user;
  elseif(password_verify($password, $user['passwordForget']))     return $user;
  else                                                            return false;

}
