<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* Recupero parametri */
$nomeIngrediente = $_GET['ingrediente'] ?? null;
$from = $_GET['from'] ?? null;
$numeroRicetta = $_GET['numero'] ?? null;

/* Controllo parametro */
if (!$nomeIngrediente) {
    echo "<p><em>Ingrediente non specificato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* Determino il link torna indietro */
if ($from === 'ricetta' && $numeroRicetta) {
    $backUrl = "pages/ricette/dettaglio.php?numero=" . urlencode($numeroRicetta);
} else {
    $backUrl = $_SERVER['HTTP_REFERER'] ?? 'pages/ingredienti/index.php';
}
?>

<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
    ← Torna indietro
</a>

<h1><?= htmlspecialchars($nomeIngrediente) ?></h1>

<h2>Ricette che usano questo ingrediente</h2>

<?php
$utilizzi = $db->ingredienti->find([
    "ingrediente" => $nomeIngrediente
]);
?>

<ul>
<?php foreach ($utilizzi as $u): ?>

    <?php
    $ricetta = $db->ricette->findOne([
        "numero" => $u['numeroRicetta']
    ]);

    if (!$ricetta) {
        continue;
    }
    ?>

    <li>
        <?= htmlspecialchars($u['quantità']) ?> —
        <a href="pages/ricette/dettaglio.php?numero=<?= urlencode($ricetta['numero']) ?>">
            <?= htmlspecialchars($ricetta['titolo']) ?>
        </a>
    </li>

<?php endforeach; ?>
</ul>

<a href="pages/ingredienti/index.php" class="btn-category">
    ← Torna agli ingredienti
</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>