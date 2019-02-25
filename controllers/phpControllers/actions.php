<?php


//////////////////////////////////////////////
// deconnecte un membre
//////////////////////////////////////////////
if(ROOT.$infosRoot['page'] === $liens['logout']['url']){
  foreach ($_SESSION as $key => $value) {
    unset($_SESSION[$key]);
    header('Location: '.$liens['index']['url']);
    die();
  }
}


//////////////////////////////////////////////
// Modifie un avatar
//////////////////////////////////////////////
elseif(ROOT.$infosRoot['page'] === $liens['changeAvatar']['url']){

  if(isset($_FILES['avatar']) && !empty($_FILES['avatar'])){

    $idUser = (int) $_POST['idUser'];

    $userPseudo = getInDb(['pseudo'], DB_USERS, ['id' => $idUser], false, '', 'fetchColumn');
    $userPseudo = simpleName($userPseudo);

    // j'enregistre l'extension de l’image
  	$ext = substr(strrchr($_FILES['avatar']['name'],'.'),1);
  	// j'enregistre un nom d'image unique   time() et l'extension du fichier.
  	//$imageName = $imageName.'-'.time().'.'.$ext;
  	$imageName = $userPseudo.'.'.$ext;

    $nbr = 0;
    while (file_exists('upload/'.$imageName)) {
      $nbr++;
      $imageName = $userPseudo.'-'.$nbr.'.'.$ext;
    }



  	$image_src = $config['imagePath'].$imageName;

  	//je verifie et déplace l’image uploadée
  	$msgs_errors = uploadImage('avatar', $image_src, $config['imageMaxSize'], $config['imageExtention']);

  	//si il n'y a pas de message d'erreur
  	if(!$msgs_errors){

      // création de la miniature
      $image_dest = $config['imagePath'].'thumbs/'.$imageName;
      $max_size = 150;
      if(imageThumb($image_src, $image_dest, $max_size, false, true)){
        if(updtInDb(['avatar' => $imageName], DB_USERS, ['id' => $idUser])){
          add_message('Avatar enregistré.');
        }else{
          add_message('L`avatar n`a pas ete créee', 'error');
        }
      }
    }
  }


  header("location:".  $_SERVER['HTTP_REFERER']);
  die();

}

//////////////////////////////////////////////
// sinon
//////////////////////////////////////////////
else{
  header('Location: '.$liens['index']['url']);
  die();
}
