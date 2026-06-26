<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codRegione = $_GET['regione'] ?? null;

if (!$codRegione) {
    echo "<p><em>Regione non specificata.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

// Recupero la regione tramite COD
$regione = $db->regioni->findOne([
    "cod" => $codRegione
]);

if (!$regione) {
    echo "<p><em>Regione non trovata.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

// Link di ritorno
$backUrl = $_SERVER['HTTP_REFERER'] ?? 'pages/regioni/index.php';
?>

<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
    ← Torna indietro
</a>

<h2><?= htmlspecialchars($regione['nome']) ?></h2>

<h3>Ricette della regione</h3>

<?php
$ricetteTipiche = $db->ricettaRegionale->find([
    "cod" => $codRegione
]);
?>

<ul>

<?php foreach ($ricetteTipiche as $rt): ?>

    <?php
    $ricetta = $db->ricette->findOne([
        "numero" => $rt['numeroRicetta']
    ]);
    ?>

    <li>

        <?php if ($ricetta): ?>

            <a href="pages/ricette/dettaglio.php?numero=<?= urlencode($ricetta['numero']) ?>&from=regione&cod=<?= urlencode($regione['cod']) ?>">
                <?= htmlspecialchars($ricetta['titolo']) ?>
            </a>

        <?php else: ?>

            <em>Ricetta non trovata</em>

        <?php endif; ?>

    </li>

<?php endforeach; ?>

</ul>

<a href="pages/regioni/index.php" class="btn-category">
    ← Torna alle regioni
</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>