<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ingrediente = trim($_POST['ingrediente'] ?? '');
    $numeroRicetta = (int) ($_POST['numeroRicetta'] ?? 0);
    $quantita = trim($_POST['quantita'] ?? '');

    if ($ingrediente && $numeroRicetta && $quantita) {

        $db->ingredienti->insertOne([
            'ingrediente' => $ingrediente,
            'numeroRicetta' => $numeroRicetta,
            'quantità' => $quantita
        ]);

        header("Location: dettaglio.php?ingrediente=" . urlencode($ingrediente));
        exit;
    }
}
?>

<h1>Nuovo ingrediente</h1>

<form method="post">

    <p>
        Nome ingrediente<br>
        <input type="text" name="ingrediente" required>
    </p>

    <p>
        Numero ricetta<br>
        <input type="number" name="numeroRicetta" required>
    </p>

    <p>
        Quantità<br>
        <input type="text" name="quantita" required>
    </p>

    <button type="submit">Salva</button>

</form>

<?php include __DIR__ . '/../../interface/footer.php'; ?>