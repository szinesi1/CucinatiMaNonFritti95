<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* =========================
   RECUPERO RICETTA
========================= */

$numeroRicetta = isset($_GET['numero'])
    ? (int) $_GET['numero']
    : (int) ($_POST['numeroRicetta'] ?? 0);

$ricetta = $db->ricette->findOne([
    'numero' => $numeroRicetta
]);

if (!$ricetta) {
    echo "<p>Ricetta non trovata.</p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* =========================
   SALVATAGGIO
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ingrediente = trim($_POST['ingrediente'] ?? '');
    $quantita = trim($_POST['quantita'] ?? '');

    if ($ingrediente !== '' && $quantita !== '') {

        $db->ingredienti->insertOne([
            'ingrediente'    => $ingrediente,
            'numeroRicetta'  => $numeroRicetta,
            'quantità'       => $quantita
        ]);

        header("Location: ../ricette/dettaglio.php?numero=" . $numeroRicetta);
        exit;
    }
}
?>

<h2>
    Nuovo ingrediente per la ricetta:
    <?= htmlspecialchars($ricetta['titolo']) ?>
</h2>

<form method="post">

    <input
        type="hidden"
        name="numeroRicetta"
        value="<?= $numeroRicetta ?>">

    <p>
        <label for="ingrediente">Nome ingrediente</label><br>

        <input
            type="text"
            id="ingrediente"
            name="ingrediente"
            class="text-input"
            required>
    </p>

    <p>
        <label for="quantita">Quantità</label><br>

        <input
            type="text"
            id="quantita"
            class="text-input"
            name="quantita"
            required>
    </p>

    <div class="form-actions">

        <a
            href="/CucinatiMaNonFritti95/v0/pages/ricette/dettaglio.php?numero=<?= $numeroRicetta ?>"
            class="btn btn-undo">
            Annulla
        </a>

        <button type="submit" class="btn btn-save">
            Salva
        </button>

    </div>

</form>

<?php include __DIR__ . '/../../interface/footer.php'; ?>