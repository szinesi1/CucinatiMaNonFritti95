<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ricettario95";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Errore di connessione al database: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>