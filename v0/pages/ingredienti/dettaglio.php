<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* =========================
   PARAMETRI
========================= */

$nomeIngrediente = $_GET['ingrediente'] ?? null;
$from = $_GET['from'] ?? null;
$numeroRicetta = $_GET['numero'] ?? null;

if (!$nomeIngrediente) {
    echo "<p><em>Ingrediente non specificato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* =========================
   BACK URL
========================= */

if ($from === 'ricetta' && $numeroRicetta) {
    $backUrl = "pages/ricette/dettaglio.php?numero=" . urlencode($numeroRicetta);
} else {
    $backUrl = $_SERVER['HTTP_REFERER'] ?? 'pages/ingredienti/index.php';
}
?>

<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
    ← Torna indietro
</a>

<h2><?= htmlspecialchars($nomeIngrediente) ?></h2>

<h3>Ricette che usano questo ingrediente</h3>

<?php
/* =========================
   QUERY INGREDIENTI
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM Ingredienti
    WHERE ingrediente = ?
");

$stmt->execute([$nomeIngrediente]);
$utilizzi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<ul>

<?php foreach ($utilizzi as $u): ?>

    <?php
    /* =========================
       RICETTA COLLEGATA
    ========================= */

    $stmt2 = $pdo->prepare("
        SELECT *
        FROM Ricette
        WHERE numero = ?
        LIMIT 1
    ");

    $stmt2->execute([$u['numeroRicetta']]);
    $ricetta = $stmt2->fetch(PDO::FETCH_ASSOC);

    if (!$ricetta) {
        continue;
    }
    ?>

    <li>
        <?= htmlspecialchars($u['quantita']) ?> —
        <a href="../ricette/dettaglio.php?numero=<?= urlencode($ricetta['numero']) ?>">
            <?= htmlspecialchars($ricetta['titolo']) ?>
        </a>
    </li>

<?php endforeach; ?>

</ul>

<a href="../ingredienti/index.php" class="btn-category">
    ← Torna agli ingredienti
</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>