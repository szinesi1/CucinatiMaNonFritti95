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
$regione = $db->regioni->findOne(["cod" => $codRegione]);

if (!$regione) {
    echo "<p><em>Regione non trovata.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}
?>

<!-- Pulsante torna indietro -->
<?php
// Se arrivo da una ricetta o da un ingrediente, posso usare il referer
$backUrl = $_SERVER['HTTP_REFERER'] ?? null;

if ($backUrl):
?>
    <a href="<?= $backUrl ?>" class="btn-back">← Torna indietro</a>
<?php endif; ?>

<h1><?= $regione['nome'] ?></h1>

<h2>Ricette della regione</h2>

<?php
// Trovo tutte le ricette tipiche usando il COD
$ricetteTipiche = $db->ricettaRegionale->find(["cod" => $codRegione]);
?>

<ul>
<?php foreach ($ricetteTipiche as $rt): ?>

    <?php
    // Recupero la ricetta collegata
    $ricetta = $db->ricette->findOne(["numero" => $rt['numeroRicetta']]);
    ?>

    <li>
        <?php if ($ricetta): ?>
            <a href="../ricette/dettaglio.php?numero=<?= $ricetta['numero'] ?>&from=regione&cod=<?= $regione['cod'] ?>">
                <?= $ricetta['titolo'] ?>
            </a>
        <?php else: ?>
            <em>Ricetta non trovata</em>
        <?php endif; ?>
    </li>

<?php endforeach; ?>
</ul>

<a href="index.php" class="btn-category">← Torna alle regioni</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
