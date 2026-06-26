<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$ingrediente = $_GET['ingrediente'] ?? null;
$numeroRicetta = (int) ($_GET['numero'] ?? 0);

if (!$ingrediente || !$numeroRicetta) {
    die("Ingrediente non specificato.");
}

$record = $db->ingredienti->findOne([
    'ingrediente' => $ingrediente,
    'numeroRicetta' => $numeroRicetta
]);

if (!$record) {
    die("Ingrediente non trovato.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantita = trim($_POST['quantita'] ?? '');

    $db->ingredienti->updateOne(
        [
            'ingrediente' => $ingrediente,
            'numeroRicetta' => $numeroRicetta
        ],
        [
            '$set' => [
                'quantità' => $quantita
            ]
        ]
    );

    header(
        "Location: dettaglio.php?ingrediente=" .
        urlencode($ingrediente) .
        "&numero=" . $numeroRicetta
    );
    exit;
}
?>

<h2>Modifica ingrediente</h2>

<form method="post">

    <p>
        Ingrediente<br>
        <strong><?= htmlspecialchars($ingrediente) ?></strong>
    </p>

    <p>
        Quantità<br>
        <input
            type="text"
            name="quantita"
            value="<?= htmlspecialchars($record['quantità']) ?>"
            required
        >
    </p>

    <button type="submit">Salva modifiche</button>

</form>

<?php include __DIR__ . '/../../interface/footer.php'; ?>