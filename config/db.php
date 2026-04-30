<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "ricettario95";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Errore connessione database: " . mysqli_connect_error());
}
?>