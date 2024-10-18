<?php
require "../../utils.php";
session_start();

$id_proprio = buisness_connected_or_redirect();

$_SESSION['form_data'] = $_POST;

$uploads_dir = "../img/tmp/$id_proprio";
if (!is_dir($uploads_dir)){
    mkdir($uploads_dir, 0777, true);
}

if (isset($_FILES["images"])){
  if (!isset($_SESSION["form_images"])){
    $_SESSION["form_images"] = [];
  }
  foreach($_SESSION["form_images"] as $key=>$img){
    if (!in_array($img, $_POST["img-loaded"])){
        unset($_SESSION["form_images"][$key]);
    }
  }
  unset($_SESSION["img-loaded"]);

    foreach ($_FILES["images"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES["images"]["tmp_name"][$key];
            $name = basename($_FILES["images"]["name"][$key]);
            if (!in_array("/tmp/$id_proprio/$name", $_SESSION["form_images"])){
                $test = move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $_SESSION["form_images"][] = "/tmp/$id_proprio/$name";
            }
        }
    }
}



echo json_encode(['status' => 'success']);
?>
