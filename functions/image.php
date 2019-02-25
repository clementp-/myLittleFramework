<?php


/**
 * verifie les champs envoyés , et le cas échéant si le téléchargement se passe bien enregistre l'image dans $destination
 *
 * @param string $index nom de l'input $_files
 * @param string $destination chemin de destination du fichier image
 * @param string $maxsize taille max du fichier
 * @param array $extensions les extensions autorisées
 * @return $msgs message d'erreur
 */
function uploadImage($index, $destination, $maxsize = FALSE, $extensions = FALSE){

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
 *
 * @param image_src - Chemin vers l'image source.
 * @param image_dest - Le chemin de destination. S'il n'est pas défini ou s'il vaut NULL, le flux brut de l'image sera affiché Pour.
 * directement éviter de fournir cet argument afin de fournir l'argument max_size, utilisez une valeur NULL.
 * @param max_size - La taille maximale (largeur ou hauteur) de l'image de destination. Ce paramètre optionnel a pour valeur par défaut 100.
 * @param expand - Si ce paramètre vaut TRUE, imagethumb() pourra éventuellement agrandir l'image pour atteindre la taille max_size dans le cas ou la taille de image_src est plus petite que max_size
 * @param square - Si ce paramètre vaut TRUE, la miniature générée sera carrée.
 * @return Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
*/
function imageThumb( $image_src , $image_dest = NULL , $max_size = 100, $expand = FALSE, $square = FALSE )
{
    if( !file_exists($image_src) ) return FALSE;

    // Récupère les infos de l'image
    $fileinfo = getimagesize($image_src);
    if( !$fileinfo ) return FALSE;

    $width     = $fileinfo[0];
    $height    = $fileinfo[1];
    $type_mime = $fileinfo['mime'];
    $type      = str_replace('image/', '', $type_mime);

    if( !$expand && max($width, $height)<=$max_size && (!$square || ($square && $width==$height) ) )
    {
        // L'image est plus petite que max_size
        if($image_dest)
        {
            return copy($image_src, $image_dest);
        }
        else
        {
            header('Content-Type: '. $type_mime);
            return (boolean) readfile($image_src);
        }
    }

    // Calcule les nouvelles dimensions
    $ratio = $width / $height;

    if( $square )
    {
        $new_width = $new_height = $max_size;

        if( $ratio > 1 )
        {
            // Paysage
            $src_y = 0;
            $src_x = round( ($width - $height) / 2 );

            $src_w = $src_h = $height;
        }
        else
        {
            // Portrait
            $src_x = 0;
            $src_y = round( ($height - $width) / 2 );

            $src_w = $src_h = $width;
        }
    }
    else
    {
        $src_x = $src_y = 0;
        $src_w = $width;
        $src_h = $height;

        if ( $ratio > 1 )
        {
            // Paysage
            $new_width  = $max_size;
            $new_height = round( $max_size / $ratio );
        }
        else
        {
            // Portrait
            $new_height = $max_size;
            $new_width  = round( $max_size * $ratio );
        }
    }

    // Ouvre l'image originale
    $func = 'imagecreatefrom' . $type;
    if( !function_exists($func) ) return FALSE;

    $image_src = $func($image_src);
    $new_image = imagecreatetruecolor($new_width,$new_height);

    // Gestion de la transparence pour les png
    if( $type=='png' )
    {
        imagealphablending($new_image,false);
        if( function_exists('imagesavealpha') )
            imagesavealpha($new_image,true);
    }

    // Gestion de la transparence pour les gif
    elseif( $type=='gif' && imagecolortransparent($image_src)>=0 )
    {
        $transparent_index = imagecolortransparent($image_src);
        $transparent_color = imagecolorsforindex($image_src, $transparent_index);
        $transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($new_image, 0, 0, $transparent_index);
        imagecolortransparent($new_image, $transparent_index);
    }

    // Redimensionnement de l'image
    imagecopyresampled(
        $new_image, $image_src,
        0, 0, $src_x, $src_y,
        $new_width, $new_height, $src_w, $src_h
    );

    // Enregistrement de l'image
    $func = 'image'. $type;
    if($image_dest)
    {
        $func($new_image, $image_dest);
    }
    else
    {
        header('Content-Type: '. $type_mime);
        $func($new_image);
    }

    // Libération de la mémoire
    imagedestroy($new_image);

    return TRUE;
}


function isImage($image){
    $ext_position = strrpos($image, '.'); // donne la position du dernier point ou
    // $ext_position = explode( '.' , $image);
    $ext_image = substr($image, $ext_position); // recupere les carraater apres la possition
    //$ext_position = end($ext);
    $ext_image = strtolower($ext_image); // je met en minuscule
    $ext_valide = ['.jpg','.jpeg','.png','.bmp','.gif']; // je crée le tableau de verification

    return in_array($ext_image,$ext_valide); // in_array retourne true ou false si il trouve une correspondance dans le tableau
}


/** Cettre fonction retour une tableau/array avec 4 info sur un fichier donnée
 * [0] => nom , [1] => extention, [2] => nom+extention, [3] => true si c'est une image
 *
 *
 * @param string $image le nom du fichier à tester
 * @return array [0] => nom , [1] => extention, [2] => nom+extention, [3] => true si c'est une image
 *
**/

function infoImage($image){
    $ext_position = strrpos($image, '.');
    $ext_image = substr($image, $ext_position);
    $nom_image = substr($image, 0, $ext_position);
    $ext_image_m = strtolower($ext_image);
    $ext_valide = ['.jpg','.jpeg','.png','.bmp','.gif'];

    $image_v = in_array($ext_image_m,$ext_valide);

    $info_fichier = [$nom_image, $ext_image, $image, $image_v];

    return   $info_fichier;
}


/**
*
* Fonction pour definir si une image est claire ou sombre
*
* retourne 1 si image plus claire que le seuil d'obscurite défini
* retourne 0 si image plus sombre ou égal au le seuil d'obscurite défini
* retourne le pourcentage d'obscurite si le seuil défini est 0 (zero)
*
* @param $img       chemin vers l'image source
* @param $limit     seuil d'obscurite de 0 à 100 (%) ex: 50
*
* fonction inspiree de : https://www.php.net/manual/fr/function.imagecolorat.php#70783
* (fonction donnant le code hex de la couleur moyenne d'une image)
*
*/
function cclair($img, $limit) {
  $img = imagecreatefrompng($img);
  // on convertit img en gris :
  imagefilter($img, IMG_FILTER_GRAYSCALE);
  // on compte nombre pixels ds img :
  $w = imagesx($img);
  $h = imagesy($img);
  $pxls = $w * $h;
  // img en gris donc r=v=b, donc on n'utilisera qu'1 valeur (red) :
  $red = 0;
  // boucle pour avoir le total des valeurs "red" de chaque pixel de l'image :
  for($y = 0; $y < $h; $y++) {
     for($x = 0; $x < $w; $x++) {
       $rgb = imagecolorat($img, $x, $y);
       $red += $rgb >> 16;
     }
  }
  $red = $red / $pxls; // on fait la moyenne pour 1 seul pixel
  $percentDark = 100 - ($red * 100 / 255); // on transforme en pourcentage
  // on libere la memoire :
  imagedestroy($img);
  if($limit==0) {
    // si @param $seuil=0, renvoie pourcentage au lieu de 1 ou 0 :
    return round($percentDark);
  } else {
    return (($percentDark<$limit)?1:0); // si pourcentage < @param $limit : 1, sinon : 0
  }
}
