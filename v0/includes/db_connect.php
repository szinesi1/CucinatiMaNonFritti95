<?php

$host = "sql211.infinityfree.com";
$dbname = "if0_42294395_cucinati";
$user = "if0_42294395";
$password = "D2oVuZ9l9f";

$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $user,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5
    ]
);