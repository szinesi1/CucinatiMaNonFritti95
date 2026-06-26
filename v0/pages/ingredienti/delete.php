<?php
require __DIR__ . '/../../includes/db_connect.php';

$ingrediente = $_GET['ingrediente'] ?? null;
$numeroRicetta = (int) ($_GET['numero'] ?? 0);

if (!$ingrediente || !$numeroRicetta) {
    die("Ingrediente non specificato.");
}

$db->ingredienti->deleteOne([
    'ingrediente' => $ingrediente,
    'numeroRicetta' => $numeroRicetta
]);

header("Location: index.php");
exit;