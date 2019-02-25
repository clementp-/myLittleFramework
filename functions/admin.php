<?php

/**
* Cette fonction verifie si un membres est admin
*
* @return true ou false
*/
function adminCheck(){
  global $db;

  if($_SESSION['userId']){
    $admin = getInDb(['level'], DB_USERS, ['id' => $_SESSION['userId']], false, '', 'fetchColumn');
    if($admin == 1)  return true;
  }
  return false;

}
