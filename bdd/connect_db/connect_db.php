<?php
    include('connect_param.php');

    try {
        $db = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
       
    } 
    catch (PDOException $e) {
        print "Erreur ! " . $e->getMessage() . "<br/>";
        die();
    }
?>