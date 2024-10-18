<?php
    $project_root = dirname(__DIR__);
    $env = parse_ini_file("$project_root/.env");
    $server = $env["DB_SERVER"];
    $driver = $env["DB_DRIVER"];
    $dbname = $env["DB_NAME"];
    $user   = $env["DB_USER"];
    $pass	= $env["DB_PASS"];
?>