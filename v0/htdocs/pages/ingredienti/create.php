<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* =========================
   RECUPERO RICETTA
========================= */

$numeroRicetta = isset($_GET['numero'])
    ? (int) $_GET['numero']
    : (int) ($_POST['numeroRicetta'] ?? 0);

/* MYSQL: SELECT invece di findOne */
$stmt = $pdo->prepare("
    SELECT *
    FROM Ricette
    WHERE numero = ?
    LIMIT 1
");

$stmt->execute([$numeroRicetta]);
$ricetta = $stmt->fetch(PDO::FETCH_ASSOC);

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

        /* MYSQL: INSERT invece di insertOne */
        $stmt = $pdo->prepare("
            INSERT INTO Ingredienti (numeroRicetta, numero, ingrediente, quantita)
            VALUES (?, ?, ?, ?)
        ");

        /* numero progressivo ingrediente (semplice gestione) */
        $stmtNum = $pdo->prepare("
            SELECT COALESCE(MAX(numero), 0) + 1 AS nextNum
            FROM Ingredienti
            WHERE numeroRicetta = ?
        ");

        $stmtNum->execute([$numeroRicetta]);
        $next = $stmtNum->fetch(PDO::FETCH_ASSOC)['nextNum'];

        $stmt->execute([
            $numeroRicetta,
            $next,
            $ingrediente,
            $quantita
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
        <a href="/pages/ricette/dettaglio.php?numero=<?= $numeroRicetta ?>" class="btn btn-undo">
            Annulla
        </a>
        <button type="submit" class="btn btn-save">
            Salva
        </button>
    </div>
</form>

<?php include __DIR__ . '/../../interface/footer.php'; ?>