<?php
require "../../utils.php";
session_start();
unset($_SESSION['form_data']);
unset($_SESSION['form_images']);
$id_proprio = buisness_connected_or_redirect();
$dirname = "../img/tmp/$id_proprio";
array_map('unlink', glob("$dirname/*.*"));
rmdir($dirname);

echo json_encode(['status' => 'success']);
?>
