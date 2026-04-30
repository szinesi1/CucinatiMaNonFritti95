<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "db4_ricettario";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}
?>