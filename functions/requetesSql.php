<?php

/**
* Cette fonction récupere des elements dans la BDD
*
* @param data          array    un tableau dont les cle sont les index de la BDD ex ['id', 'titre', 'date']
* @param tableName     string   le nom de la table
* @param where         array    un tableau des arguments WHERE ex ['id' => 1]
* @param order         array    ['id DESC', nom]
* @param limit         string   '0, 3'
* @param typeResult    str      Tupe de fetch (fetch, fetchAll, fetchColumn)
*
* exemple : getInDb(['id', name], DB_USERS, ['id' => 1], false, '', 'fetchColumn');
*
* @return bool|arrya soit le tableau des donnée ou false
*/
function getInDb($data, $tableName, $where = false, $order = false, $limit = '', $typeResult = 'fetchAll'){
  global $db;

  // SELECT
  if($data == 'all'){
    $sqlIndex = '*';
  }else{
    $dataIndex    = [];
    foreach ($data as $key => $value) {
      $dataIndex[]  = htmlspecialchars($value);
    }
    $sqlIndex   = implode(", ", $dataIndex);
  }
  // END SELECT

  // WHERE
  if($where){
    $dataWhere  = $bindValue = [];
    foreach ($where as $key => $value) {
      $dataWhere[] = htmlspecialchars($key).' = :'.htmlspecialchars($key);
      $valueWhere[htmlspecialchars($key)] = htmlspecialchars($value);
    }
    $dataWhere = ' WHERE '.implode(" AND ", $dataWhere);
  }
  else{ $dataWhere = '';}
  // END WHERE

  // ORDERBY
  if($order){
    $orderBy = [];
    foreach ($order as $value) {
      $orderBy[] = htmlspecialchars($value);
    }
    $orderBy = ' ORDER BY '.implode(",", $orderBy);
  }
  else{ $orderBy = '';}
  // END ORDERBY

  // LIMIT
  if($limit){
    $limit = ' LIMIT '.htmlspecialchars($limit);
  }
  // END LIMIT

  $req = $db->prepare('SELECT '.$sqlIndex.' FROM '.$tableName.' '.$dataWhere.' '.$orderBy.' '.$limit);
  if($where){
    foreach ($valueWhere as $key => $value) {
      $req->bindValue(':'.$key, $value, PDO::PARAM_STR);
    }
  }
  $req->execute();

  // Affichage d'information pour la gestion des erreurs ⤵
  // print_r($req->errorInfo()); print_r($db->errorInfo());

  $info = false;
  if($typeResult === 'fetch')             $info = $req->fetch();
  elseif($typeResult === 'fetchAll')      $info = $req->fetchAll();
  elseif($typeResult === 'fetchColumn')   $info = $req->fetchColumn();

  if($info){
    return $info;
  }

  return false;
}


/**
* Cette fonction insert un element en BDD
*
* @param data       array   un tableau associatif dont les cle sont les index de la BDD
* @param tableName  string  le nom de la table
* @return int|bool l’id inserer ou fase
*/
function addInDb($data, $tableName){
  global $db;

  $dataIndex    = [];
  $dataValeur   = [];

  foreach ($data as $key => $value) {
    $dataIndex[]  = htmlspecialchars($key);
    $dataValeur[htmlspecialchars($key)] = htmlspecialchars($value);
  }

  $sqlIndex = implode(", ", $dataIndex);
  $sqlValue = ':'.implode(", :", $dataIndex);

  $req = $db->prepare('INSERT INTO '.$tableName.' ('.$sqlIndex.') VALUES ('.$sqlValue.')');
  $req->execute($dataValeur);

  // Affichage d'information pour la gestion des erreurs ⤵
  // print_r($req->errorInfo()); print_r($db->errorInfo());

  // lastInsertId retourne 0 si table inexistante
  if($db->lastInsertId()) return $db->lastInsertId();
  return false;
}


/**
* Cette fonction modifie des infos en BDD
*
* @param data          array    un tableau dont les cle sont les index de la BDD ex ['name' => 'BOB'] name est l'index de la table et bob la nouvelle valeur
* @param tableName     string   le nom de la table
* @param where         array    un tableau des arguments WHERE ex ['id' => 1]
*
* @return bool|array soit le tableau des donnée ou false
*/
function updtInDb($data, $tableName, $where){
  global $db;

  $dataSet = $dataWhere = $dataValue = [];

  // listes des données
  foreach ($data as $key => $value) {
    $dataSet[] = htmlspecialchars($key).' = :'.htmlspecialchars($key);
    $dataValue[htmlspecialchars($key)] = htmlspecialchars($value);
  }
  $dataSet = implode(", ", $dataSet);

  // list des WHERE
  foreach ($where as $key => $value) {
    $dataWhere[] = htmlspecialchars($key).' = :'.htmlspecialchars($key);
    $valueWhere[htmlspecialchars($key)] = htmlspecialchars($value);
  }
  $dataWhere = implode(" AND ", $dataWhere);

  $req = $db->prepare('UPDATE '.$tableName.' SET '.$dataSet.' WHERE '.$dataWhere);
  foreach ($dataValue as $key => $value) {
    $req->bindValue(':'.$key, $value, PDO::PARAM_STR);
  }
  foreach ($valueWhere as $key => $value) {
    $req->bindValue(':'.$key, $value, PDO::PARAM_STR);
  }
  $req->execute();
  $nbr = $req->rowCount();

  return  $nbr;
}


/**
* Cette fonction supprime des infos en BDD
*
* @param $tableName     string   le nom de la table
* @param $where         array    un tableau des arguments WHERE ex ['id' => 1]
*
* @return bool|arrya soit le tableau des donnée ou false
*/
function rmvInDb($tableName, $where){
  global $db;

  $dataWhere = [];

  // list des WHERE
  foreach ($where as $key => $value) {
    $dataWhere[] = htmlspecialchars($key).' = :'.htmlspecialchars($key);
    $valueWhere[htmlspecialchars($key)] = htmlspecialchars($value);
  }
  $dataWhere = implode(" AND ", $dataWhere);

  $req = $db->prepare('DELETE FROM '.$tableName.' WHERE '.$dataWhere);
  foreach ($valueWhere as $key => $value) {
    $req->bindValue(':'.$key, $value, PDO::PARAM_STR);
  }
  $req->execute();
  $nbr = $req->rowCount();

  return  $nbr;
}
