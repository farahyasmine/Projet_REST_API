<?php

include_once 'db/Database.php';
$db_table = "products";
$database = new Database();
$conn = $database->getConnection();
$sqlQuery = "Select * from ". $db_table ;
$stmt = $conn->query($sqlQuery);
//à recupérer depuis une base de données
//Chaque Token a une duré de vie
$token  = "Bmn0c8rQDJoGTibk";
$headers  = getallheaders();
if (array_key_exists("x-auth-token", $headers)) {
    if($headers["x-auth-token"] === $token );
    echo json_encode($stmt->fetchAll());
    }