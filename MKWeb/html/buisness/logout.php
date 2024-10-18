<?php
session_start();
unset($_SESSION["business_id"]);
unset($_SESSION["last_page"]);
header("Location: login.php"); 
exit();
?>
