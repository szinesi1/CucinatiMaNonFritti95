<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$ingrediente = $_GET['ingrediente'] ?? null;
$numeroRicetta = (int) ($_GET['numero'] ?? 0);

if (!$ingrediente || !$numeroRicetta) {
    die("Ingrediente non specificato.");
}

/* =========================
   RECORD PRINCIPALE
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM Ingredienti
    WHERE ingrediente = ?
    AND numeroRicetta = ?
");

$stmt->execute([$ingrediente, $numeroRicetta]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die("Ingrediente non trovato.");
}

/* =========================
   RICETTA
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM Ricette
    WHERE numero = ?
    LIMIT 1
");

$stmt->execute([$numeroRicetta]);
$ricetta = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   ALTRE RICETTE (stesso ingrediente)
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM Ingredienti
    WHERE ingrediente = ?
    AND numeroRicetta != ?
");

$stmt->execute([$ingrediente, $numeroRicetta]);
$altreRicette = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   SALVATAGGIO
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nuovoIngrediente = trim($_POST['ingrediente'] ?? '');
    $quantita = trim($_POST['quantita'] ?? '');
    $propaga = isset($_POST['propaga']);

    /* UPDATE principale */
    $stmt = $pdo->prepare("
        UPDATE Ingredienti
        SET ingrediente = ?, quantita = ?
        WHERE ingrediente = ?
        AND numeroRicetta = ?
    ");

    $stmt->execute([
        $nuovoIngrediente !== '' ? $nuovoIngrediente : $ingrediente,
        $quantita,
        $ingrediente,
        $numeroRicetta
    ]);

    /* propagazione nome ingrediente */
    if ($propaga && $nuovoIngrediente !== '') {

        $stmt = $pdo->prepare("
            UPDATE Ingredienti
            SET ingrediente = ?
            WHERE ingrediente = ?
        ");

        $stmt->execute([
            $nuovoIngrediente,
            $ingrediente
        ]);
    }

    header("Location: ../ricette/dettaglio.php?numero=" . $numeroRicetta);
    exit;
}
?>

<h2>Modifica ingrediente</h2>

<p class="recipe-title-edit">
    <strong>Ricetta:</strong>
    <?= htmlspecialchars($ricetta['titolo'] ?? '') ?>
</p>

<div class="edit-grid">

    <!-- SINISTRA -->
    <div class="edit-main">

        <form method="post" id="editForm">

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
                       value="<?= htmlspecialchars($record['quantita']) ?>"
                       required>
            </p>

            <!-- bottone dentro form (FIX IMPORTANTE) -->
            <div class="form-actions">

                <a href="/pages/ricette/dettaglio.php?numero=<?= $numeroRicetta ?>" class="btn btn-undo">
                    Annulla
                </a>

                <button type="submit" class="btn btn-save">
                    Salva
                </button>

            </div>

        </form>

    </div>

    <!-- DESTRA -->
    <div class="edit-side">

        <label class="check-card">
            <input type="checkbox" name="propaga" form="editForm">

            <div class="check-ui">
                <div class="check-box"></div>

                <div class="check-text">
                    <strong>Modifica globale</strong>
                    <span>Applica il cambiamento a tutte le ricette</span>
                </div>
            </div>
        </label>

        <div class="side-box">
            <h4>Presente anche in:</h4>

            <ul>
                <?php foreach ($altreRicette as $r): ?>
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT titolo
                        FROM Ricette
                        WHERE numero = ?
                        LIMIT 1
                    ");
                    $stmt->execute([$r['numeroRicetta']]);
                    $info = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <li><?= htmlspecialchars($info['titolo'] ?? 'Ricetta') ?></li>
                <?php endforeach; ?>
            </ul>

        </div>

    </div>
</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>