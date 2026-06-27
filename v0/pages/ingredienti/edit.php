<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$ingrediente = $_GET['ingrediente'] ?? null;
$numeroRicetta = (int) ($_GET['numero'] ?? 0);

if (!$ingrediente || !$numeroRicetta) {
    die("Ingrediente non specificato.");
}

/* record principale */
$record = $db->ingredienti->findOne([
    'ingrediente' => $ingrediente,
    'numeroRicetta' => $numeroRicetta
]);

if (!$record) {
    die("Ingrediente non trovato.");
}

/* ricetta */
$ricetta = $db->ricette->findOne([
    'numero' => $numeroRicetta
]);

/* altre ricette */
$altreRicette = $db->ingredienti->find([
    'ingrediente' => $ingrediente,
    'numeroRicetta' => ['$ne' => $numeroRicetta]
]);

/* SALVATAGGIO */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nuovoIngrediente = trim($_POST['ingrediente'] ?? '');
    $quantita = trim($_POST['quantita'] ?? '');
    $propaga = isset($_POST['propaga']);

    $update = [
        'quantità' => $quantita
    ];

    if ($nuovoIngrediente !== '') {
        $update['ingrediente'] = $nuovoIngrediente;
    }

    $db->ingredienti->updateOne(
        [
            'ingrediente' => $ingrediente,
            'numeroRicetta' => $numeroRicetta
        ],
        ['$set' => $update]
    );

    if ($propaga && $nuovoIngrediente !== '') {
        $db->ingredienti->updateMany(
            ['ingrediente' => $ingrediente],
            ['$set' => ['ingrediente' => $nuovoIngrediente]]
        );
    }

    header("Location: ../ricette/dettaglio.php?numero=" . $numeroRicetta);
    exit;
}
?>

<h2>Modifica ingrediente</h2>

<p class="recipe-title-edit">
    <strong>Ricetta:</strong>
    <?= htmlspecialchars($ricetta['titolo']) ?>
</p>

<div class="edit-grid">

    <!-- SINISTRA -->
    <div class="edit-main">

        <form method="post">

            <p>
                Nome ingrediente<br>
                <input type="text"
                       name="ingrediente"
                       class="text-input"
                       value="<?= htmlspecialchars($record['ingrediente']) ?>">
            </p>

            <p>
                Quantità<br>
                <input type="text"
                       name="quantita"
                       class="text-input"
                       value="<?= htmlspecialchars($record['quantità']) ?>"
                       required>
            </p>

        </form>

    </div>

    <!-- DESTRA -->
    <div class="edit-side">

        <!-- checkbox -->
        <label class="check-card">
            <input type="checkbox" name="propaga">

            <div class="check-ui">
                <div class="check-box"></div>

                <div class="check-text">
                    <strong>Modifica globale</strong>
                    <span>Applica il cambiamento a tutte le ricette</span>
                </div>
            </div>
        </label>

        <!-- ricette collegate -->
        <div class="side-box">
            <h4>Presente anche in:</h4>

            <ul>
                <?php foreach ($altreRicette as $r): ?>
                    <?php
                    $info = $db->ricette->findOne([
                        'numero' => $r['numeroRicetta']
                    ]);
                    ?>
                    <li><?= htmlspecialchars($info['titolo'] ?? 'Ricetta') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</div>

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
<form id="editForm" method="post"></form>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
