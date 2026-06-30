<?php
require __DIR__ . '/../../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idIngrediente = isset($_POST['idIngrediente']) ? (int) $_POST['idIngrediente'] : 0;
    $numero = isset($_POST['numero']) ? (int) $_POST['numero'] : 0;

    if ($idIngrediente > 0 && $numero > 0) {

        $stmt = $pdo->prepare("
            DELETE FROM Ingredienti
            WHERE idIngrediente = ?
            AND numeroRicetta = ?
        ");

        $stmt->execute([$idIngrediente, $numero]);
    }

    header("Location: /pages/ricette/dettaglio.php?numero=" . $numero);
    exit;
}

header("Location: /pages/ricette/index.php");
exit;