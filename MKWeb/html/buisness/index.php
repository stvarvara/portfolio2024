<?php 
    session_start();
    require "../../utils.php";

    header('Location: mes_reserv.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/connect.css">
    <title>Acceuil</title>
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
</head>
<body class="page">
    <div class="wrapper">
    <?php require_once "header.php"; ?>
        <div>
            Bienvenue sur AlheizBreizh
        </div>
    </div>
    <?php require_once "footer.php"; ?>
</body>
