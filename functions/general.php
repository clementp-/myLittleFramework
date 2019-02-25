<?php

/**
 * Cette fonction crée une connexion à la base de données,
 * telle que définie dans la variable $bdd, puis la renvoie.
 *
 * @return PDO la connexion à la base de données
 * @throws Exception si la connexion ne peut pas se faire
 */
function connect_db() {
    global $bdd;
    $dsn = 'mysql:dbname='.$bdd['dbname'].';host='.$bdd['dbhost'].';charset=utf8';
    $db = new PDO($dsn, $bdd['dbuser'], $bdd['dbpassword']);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
}

/**
 * Cette fonction enregistre un message dans la session
 *
 * @param string $msg Le message à enregistrer
 * @param string $icon La class css de l’icone (voir font-awesome)
 * @param string $color la couleur de fond (voir css pour les couleurs dispo "base.css > .info")
 */
function add_message($msg, $class = 'success') {
    if(!isset($_SESSION['messages'])){
        $_SESSION['messages'] = [];
    }
    $_SESSION['messages'][] = [$msg, $class];
}

/**
 * Cette fonction récupère la liste des messages existants et les
 * supprime de la session (pour qu’on ne puisse y accéder qu’une fois)
 *
 * @return array la liste des messages
 */
function get_messages() {
    if(isset($_SESSION['messages'])) {
        $messages = $_SESSION['messages'];
        unset($_SESSION['messages']); // on supprime la variable
        return $messages;
    } else {
        return [];
    }
}


/**
 * recupere les donnée de l’url et renvoi les parametre trier
 * url doit resenblé a ceci:
 * https://site.fr/[page][-id]/[getParam1]/[getParam2]/[page-4]
 * https://site.fr/[page]/[page-4]/[getParam1]/[getParam2]
 * https://site.fr/[page]/[page-4]
 * https://site.fr/[page][-id]/[getParam1]
 *
 * @param $infosUrl est $_GET
 * @return array
 * page             - est la page a charger voir $liens dans config.php
 * id               - est l’id
 * numPage          - est le param pour un systeme evnetuel de pagination [page-4]
 * getParam(num)    - est/sont tous les autre paramtre
 * controller       - lien vers le controler
 * view             - lien vers la vue
 */
function rootPage($infosUrl) {
  global $liens, $config;


  if(isset($infosUrl['page']) && !empty($infosUrl['page']) && strlen($infosUrl['page']) <= 150){

    $infosRoot      = explode('/', $infosUrl['page']);

    foreach ($infosRoot as $key => $value) {

      // on retire les valeur vide
      if(empty($infosRoot[$key])) {
        unset($infosRoot[$key]);
      }
      else{
        //on ne laisse que des letter-numero-tiret
        $infosRoot[$key] = preg_replace('#[^0-9a-z_-]+#i', '',  $value);

        // le prenier paramtre est la page a charger
        //je crée le controller eet la vue
        if($key == 0){
          $infosRoot['page'] = explode('-', $infosRoot[0]);

          if(is_array($infosRoot['page']) AND is_numeric(end($infosRoot['page']))){
            $infosRoot['id'] = (int) array_pop($infosRoot['page']);
          }

          $infosRoot['page'] = implode('-', $infosRoot['page']);

          foreach ($liens as $infosLien) {
            if(ROOT.$infosRoot['page'] == $infosLien['url']){
              $infosRoot['controller']  = 'controllers/phpControllers/'.$infosLien['file'];
              $infosRoot['view']      = 'views/'.$infosLien['file'];
              // je redefinie la variable de titre de la page
              $config['pageTitle'] = $infosLien['title'];
              // pour la balise meta robot
              if($infosLien['agree'] === 'admin') $config['metaRobot'] = 'none';
            }
          }
        }

        //je crée la varible des param get
        if(!isset($infosRoot['getParam'])) $infosRoot['getParam'] = [];

        // Un paramtre pour gere la pagination
        // exemple : monsite.fr/news/page-4
        if (preg_match("#page-([0-9]+)#", $value)) {
          $infosRoot['numPage'] = explode('-', $infosRoot[$key]);

          if(is_array($infosRoot['numPage']) AND is_numeric(end($infosRoot['numPage']))){
            $infosRoot['numPage'] = (int) array_pop($infosRoot['numPage']);
          }

        }
        // Le dernier c’est pour recupere tout autre parametre eventuel
        // exemple : monsite.fr/news/un-param-autre (param1 = un-param-y, param2 = un-param-x,)
        elseif($key >= 1){
          $infosRoot['getParam'][] = $value;
        }
        //suppresion de la cle car on la deja reecree avec le bon nom
        unset($infosRoot[$key]);
      }
    }
  }
  else{
    $infosRoot['page']        = $liens['index']['title']; // $liens['index']['url'];
    $infosRoot['controller']  = 'controllers/phpControllers/'.$liens['index']['file'];
    $infosRoot['view']        = 'views/'.$liens['index']['file'];
    $config['pageTitle']      = $liens['index']['title'];
  }

  //verifie si les fichier existe bien
  if(!isset($infosRoot['controller']) || !isset($infosRoot['view']) ||  !file_exists($infosRoot['controller']) || !file_exists($infosRoot['view'])){
    $infosRoot['controller']  = 'controllers/phpControllers/'.$liens['404']['file'];
    $infosRoot['view']        = 'views/'.$liens['404']['file'];
    $infosRoot['page']        = $liens['404']['url'];
    $config['pageTitle']      = $liens['404']['title'];
  }

  return $infosRoot;
}









/**
* Cette fonction verifie si le visiteur peut charger la page demander
* et renvoi la page ou il faut le rediriger eventuelement
*
* @param string Titre de la page
* @return bool|str False si tout va bien sinon le nom de la cle de la page a rediriger
*/
function checkAgree($page){
  global $liens;

  foreach ($liens as $infosLien) {

    // si la page demander correspond a une page recu
    if($infosLien['url'] === ROOT.$page){

      // premision pour le utilisateur
      if($infosLien['agree'] === 'all'){
        return false; //redirection inutile
      }
      // Si le visiteur cherche a acceder a une page réserver au membre
      elseif($infosLien['agree'] === 'user' && !$_SESSION['userId']){
        return 'login';  //redirection sur login/connection
      }
      // Si le visiteur cherche a acceder a une page réserver au membre
      elseif($infosLien['agree'] === 'admin' && !adminCheck()){
        return 'index';  //redirection sur index
      }
      // Si le  membrer cherche a acceder a une page réserver au visiteu
      elseif($infosLien['agree'] === 'visitor' && $_SESSION['userId']){
        return 'index';  //redirection inutile
      }
    }
  }

  //si il n’y a pas eux de retour deja
  //aucune redirection le visiteur a le droit de ce trouvel sur cette page
  return false;

}


/**
* Convertie un datetime en fr
*
* @param format       c'est le format PHP normale exemple d M Y
* @param date_ajout   c'est la date au format MYSQL
* @return
*/
function datetimeFr($format,$date_ajout) {

	$annee = (int) substr($date_ajout, 0, -15);
	$mois = (int) substr($date_ajout, 5, -12);
	$jour = (int) substr($date_ajout, 8, -9);
	$heure = (int) substr($date_ajout, 11, -6);
	$minute = (int) substr($date_ajout, 14, 3);
	$seconde = (int) substr($date_ajout, -2);

	$date_ajout = mktime($heure, $minute, $seconde, $mois, $jour, $annee);

	return dateFr($format, $date_ajout);

}
//** cette fonction est lié a celle du haut) **/
function dateFr($format, $timestamp = null) {
	$param_D = array('', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
	$param_l = array('', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	$param_F = array('', 'Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');
	$param_M = array('', 'Jan', 'F&eacute;v', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Ao&ucirc;', 'Sep', 'Oct', 'Nov', 'D&eacute;c');

	$return = '';
	if(is_null($timestamp)) { $timestamp = mktime(); }
	for($i = 0, $len = strlen($format); $i < $len; $i++) {
		switch($format[$i]) {
			case '\\' : // double.slashes
				$i++;
				$return .= isset($format[$i]) ? $format[$i] : '';
				break;
			case 'D' :
				$return .= $param_D[date('N', $timestamp)];
				break;
			case 'l' :
				$return .= $param_l[date('N', $timestamp)];
				break;
			case 'F' :
				$return .= $param_F[date('n', $timestamp)];
				break;
			case 'M' :
				$return .= $param_M[date('n', $timestamp)];
				break;
			default :
				$return .= date($format[$i], $timestamp);
				break;
		}
	}
	return $return;
}

/**
* Cette fonction retire tout les carracter qui ne sont pas alphanumerique ou tirer
*
* @param $chaine        string   la chaine a simplifier
* @param $charset       string
*
*
* @return la chaine simplifier
*/
function simpleName($chaine, $charset='utf-8'){

	$chaine = mb_strtolower($chaine, 'UTF-8');
    $chaine = str_replace(
        array(
            'à', 'â', 'ä', 'á', 'ã', 'å',
            'î', 'ï', 'ì', 'í',
            'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
            'ù', 'û', 'ü', 'ú',
            'é', 'è', 'ê', 'ë',
            'ç', 'ÿ', 'ñ',
            'œ', 'æ', ' ', '\'',
			'&amp;', '&#039;', '&lt;', '&gt;', '&quot;', '&apos;',
        ),
        array(
            'a', 'a', 'a', 'a', 'a', 'a',
            'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u',
            'e', 'e', 'e', 'e',
            'c', 'y', 'n',
            'oe', 'ae', '-', '',
			'-et-', '-', '', '', '', '',
        ),
        $chaine
    );

	$string = preg_replace ('#[^0-9a-z_]+#i', '-',  $chaine); // Garde uniquement ce qui est des letre
	$string = preg_replace('#-{2,}#','-',$string); // si il y en a plusieur tiré - de suite il est remplacer par 1
	$string = preg_replace('#-{2,}#','_',$string); // si il y en a plusieur tiré _ de suite il est remplacer par 1
	$string = preg_replace('#-_#','-',$string); // si il y a -_ remplace par -
	$string = preg_replace('#_-#','-',$string); // si il y a _- remplace par -
	$string = preg_replace('#-$#','',$string);  // si caracter - en fin il est remplacer
	$string = preg_replace('#^-#','',$string); // si carracter - ne debut il est remplacer


  return $string;
}

/**
* Cette fonction envoi un email
*
* @param string $mailSend email du destinataire
* @param string $title titre du mail
* @param string $msgHtml message format html
* @param string $msgTxt message format text
* @param array $replace les element a remplacer ex ['[code]' => '12345']
* @return boolean
*/
function mailSend($mailSend, $title, $msgHtml, $msgTxt, $replace = []){
  global $config;

  // Déclaration de l'adresse de destination.
  $mail = $mailSend;

  // On filtre les serveurs qui présentent des bogues.
  if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) {
    $passage_ligne = "\r\n";
  }else{
    $passage_ligne = "\n";
  }

  // remplacement de element type BBcode
  $msgTxt = str_replace('[br]', $passage_ligne, $msgTxt);
  $replaceCode = $replaceValue = [];
  foreach ($replace as $code => $value) {
    $replaceCode = [$code];
    $replaceValue = [$value];
  }
  $msgHtml = str_replace($replaceCode, $replaceValue, $msgHtml);
  $msgTxt = str_replace($replaceCode, $replaceValue, $msgTxt);

  // Déclaration des messages au format texte et au format HTML.
  $message_txt = $msgTxt;
  $message_html = "<html><head></head><body>".$msgHtml."</body></html>";

  // Lecture et mise en forme de la pièce jointe.
    // $fichier   = fopen("image.jpg", "r");
    // $attachement = fread($fichier, filesize("image.jpg"));
    // $attachement = chunk_split(base64_encode($attachement));
    // fclose($fichier);

  // Création de la boundary.
  $boundary = "-----=".md5(rand());
  $boundary_alt = "-----=".md5(rand());

  // Définition du sujet.
  $sujet = $title;

  // Création du header de l'e-mail.
  $header = "From: \"".$config['siteTitle']."\"<".$config['mail'].">".$passage_ligne;
  $header.= "Reply-to: \"".$config['siteTitle']."\" <".$config['mail'].">".$passage_ligne;
  $header.= "MIME-Version: 1.0".$passage_ligne;
  $header.= "Content-Type: multipart/mixed;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;

  // Création du message.
  $message = $passage_ligne."--".$boundary.$passage_ligne;
  $message.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary_alt\"".$passage_ligne;
  $message.= $passage_ligne."--".$boundary_alt.$passage_ligne;

  // Ajout du message au format texte.
  $message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
  $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
  $message.= $passage_ligne.$message_txt.$passage_ligne;

  $message.= $passage_ligne."--".$boundary_alt.$passage_ligne;

  // Ajout du message au format HTML.
  $message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
  $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
  $message.= $passage_ligne.$message_html.$passage_ligne;

  // On ferme la boundary alternative.
  $message.= $passage_ligne."--".$boundary_alt."--".$passage_ligne;

  $message.= $passage_ligne."--".$boundary.$passage_ligne;

  // Ajout de la pièce jointe.
    // $message.= "Content-Type: image/jpeg; name=\"image.jpg\"".$passage_ligne;
    // $message.= "Content-Transfer-Encoding: base64".$passage_ligne;
    // $message.= "Content-Disposition: attachment; filename=\"image.jpg\"".$passage_ligne;
    // $message.= $passage_ligne.$attachement.$passage_ligne.$passage_ligne;
    // $message.= $passage_ligne."--".$boundary."--".$passage_ligne;

  // Envoi de l'e-mail.
  if(mail($mail,$sujet,$message,$header)) return true;
  return false;
}

/**
* Générer un mot de passe aléatoire
*
* @param int $longueur nombre de carractere
* @return string
*/
function generatorStrRandom($longueur = 8){
  // initialiser la variable $mdp
  $mdp = "";

  // Définir tout les caractères possibles dans le mot de passe,
  // Il est possible de rajouter des voyelles ou bien des caractères spéciaux
  $possible = "-_12346789abcdefghijklmnOpqrtuvwxyzABCDEFGHJIKLMNOPQRTUVWXYZ";

  // obtenir le nombre de caractères dans la chaîne précédente
  // cette valeur sera utilisé plus tard
  $longueurMax = strlen($possible);

  if ($longueur > $longueurMax) {
    $longueur = $longueurMax;
  }

  // initialiser le compteur
  $i = 0;

  // ajouter un caractère aléatoire à $mdp jusqu'à ce que $longueur soit atteint
  while ($i < $longueur) {
    // prendre un caractère aléatoire
    $caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);

    // vérifier si le caractère est déjà utilisé dans $mdp
    if (!strstr($mdp, $caractere)) {
      // Si non, ajouter le caractère à $mdp et augmenter le compteur
      $mdp .= $caractere;
      $i++;
    }
  }

  // retourner le résultat final
  return $mdp;
}


/**
 * verifie les champs envoyés , et le cas échéant si le téléchargement se passe bien enregistre l'image dans $destination
 *
 * @param string $index nom de l'input $_files
 * @param string $destination chemin de destination du fichier image
 * @param string $maxsize taille max du fichier
 * @param sting $extensions les extensions autorisées
 * @return $msgs message d'erreur
 */
function uploadFile($index, $destination, $maxsize = FALSE, $extensions = FALSE){

    $msgs = [];

    $ext = substr(strrchr($_FILES[$index]['name'],'.'),1);



    //Test1: fichier correctement uploadé
    if (!isset($_FILES[$index]) || $_FILES[$index]['error'] > 0 || $_FILES[$index]==null) {

        if($_FILES[$index]['error'] == 1)                               $msgs[] = 'La taille du fichier téléchargé excède la valeur max du serveur';
        elseif($_FILES[$index]['error'] == 2)                           $msgs[] = 'La taille du fichier téléchargé excède la valeur max spécifiée dans le formulaire HTML.';
        elseif($_FILES[$index]['error'] == 3)                           $msgs[] = 'Le fichier n’a été que partiellement téléchargé.';
        elseif($_FILES[$index]['error'] == 4)                           $msgs[] = 'Aucun fichier n’a été téléchargé. ';
        elseif($_FILES[$index]['error'] == 6)                           $msgs[] = 'Un dossier temporaire est manquant.';
        elseif($_FILES[$index]['error'] == 7)                           $msgs[] = 'Échec de l’écriture du fichier sur le disque.';
        elseif($_FILES[$index]['error'] == 8)                           $msgs[] = 'Une extension PHP a arrêté l’envoi de fichier.';
        else                                                            $msgs[] = 'Erreur inconnue.';
    }

    //Test2: taille limite
    elseif ($maxsize !== FALSE && $_FILES[$index]['size'] > $maxsize)   $msgs[] = 'Le fichier est trop volumineux.' ;

    //Test3: extension
    elseif ($extensions !== FALSE && !in_array($ext,$extensions))       $msgs[] = 'Le fichier n’est pas au bon format.';

    // verifier les dimensions de l’image
    //$image_sizes = getimagesize($_FILES['icone']['tmp_name']);
    //if ($image_sizes[0] > $maxwidth OR $image_sizes[1] > $maxheight) $erreur = "Image trop grande";

    //Déplacement
    if(!$msgs){
        if(!move_uploaded_file($_FILES[$index]['tmp_name'],$destination)){
            $msgs[] = 'Le fichier n’a pas été déplacé correctement.';
        }
    }

    return $msgs;
}


/**
 * retourne une chaine de carratere avec un nombre limité de carratere avec trois petit point (ex : Mon titre il est tr...)
 *
 * @param $string
 * @param $max
 *
 * @return string
 */
function limitCaratere($string, $max){

  if(strlen($string) > $max){
    $string = substr($string, 0, $max).'...';
  }

  return $string;
}


/**
 * Definie si la personne est sur mobile
 *
 * @param useragent c'est user agent $_SERVER['HTTP_USER_AGENT']
 *
 * @return boolea
 */
function mobileOrNotMobile($useragent){

  if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
  {
    return true;
  }
  return false;

}

/**
 * Transform un objet (exemple retour json ou xml) en Tableau Php
 * // informatix.fr/tutoriels/php/convertir-recursivement-un-objet-php-en-tableau-187
 *
 * @param objet
 *
 * @return table
 */
function objectToArray($d) {
  if (is_object($d)) {
    $d = get_object_vars($d);
  }

  if (is_array($d)) {
    return array_map(__FUNCTION__, $d);
  } else {
    return $d;
  }
}
