<?php 
function request($sql, $uniq = false){
require "connect_db/connect_param.php";

try {
  $connexion = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
  $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


  $requete = $connexion->prepare($sql);

  $requete->execute();

  if ($uniq){
    $results = $requete->fetch(PDO::FETCH_ASSOC);
  } else {
    $results = $requete->fetchAll(PDO::FETCH_ASSOC);
  }

  $connexion = null;
  return $results;

} catch(PDOException $e) {
  $connexion = null;
  echo "Error : ".$e;
  return false;
}
}

function insert($table, $columns, $values, $get_id = true) {
  require "connect_db/connect_param.php";
  
  try {
      $connexion = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
      $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
      $columnsList = implode(", ", $columns);
      
      $placeholders = implode(", ", array_fill(0, count($values), '?'));
      
      $sql = "INSERT INTO $table ($columnsList) VALUES ($placeholders)";
      
      $requete = $connexion->prepare($sql);
      
      $requete->execute($values);
      
      $id = $get_id ? $connexion->lastInsertId() : true;
      
      $connexion = null;
      
      return $id;
  } catch(PDOException $e) {
      $connexion = null;
      echo "Error : " . $e->getMessage();
      return false;
  }
}

function client_connected(){
  if (isset($_SESSION) && isset($_SESSION["client_id"])){
    return $_SESSION["client_id"];
  } else {
    return false;
  }
}

function client_connected_or_redirect(){
  if (isset($_SESSION) && isset($_SESSION["client_id"])){
    return $_SESSION["client_id"];
  } else {
    $_SESSION["last_page"] = $_SERVER["REQUEST_URI"];
    header("Location: login.php");
    exit();
  }
}

function buisness_connected(){
  if (isset($_SESSION) && isset($_SESSION["business_id"])){
    return $_SESSION["business_id"];
  } else {
    return false;
  }
}

function buisness_connected_or_redirect(){
  if (isset($_SESSION) && isset($_SESSION["business_id"])){
    return $_SESSION["business_id"];
  } else {
    $_SESSION["last_page"] = $_SERVER["REQUEST_URI"];
    header("Location: login.php");
    exit();
  }
}

function client_disconnect(){
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
  if (isset($_SESSION["client_id"])) {
      unset($_SESSION["client_id"]);
  }
  session_unset();
  session_destroy();
}

function update($table, $columns, $values, $condition) {
  require "connect_db/connect_param.php";
  try {
      $connexion = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
      $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $setClause = '';
      foreach ($columns as $key => $column) {
          $setClause .= ($key > 0 ? ', ' : '') . "$column = ?";
      }
      $sql = "UPDATE $table SET $setClause WHERE $condition";
      $requete = $connexion->prepare($sql);
      $requete->execute($values);
      $connexion = null;
      return true;
  } catch(PDOException $e) {
      $connexion = null;
      echo "Error : " . $e->getMessage();
      return false;
  }
}
function business_disconnect(){
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
  if (isset($_SESSION["business_id"])) {
      unset($_SESSION["business_id"]);
  }
  session_unset();
  session_destroy();
}

function redirect(){
  /*if (isset($_SESSION["last_page"])){
    header('Location: '.$_SESSION["last_page"]);
  } else {*/
    header('Location: index.php');
  //}
  exit();
}

function redirect_business(){
  /*if (isset($_SESSION["last_page"])){
    header('Location: '.$_SESSION["last_page"]);
  } else {*/
    header('Location: index.php');
  //}
  exit();
}

?>