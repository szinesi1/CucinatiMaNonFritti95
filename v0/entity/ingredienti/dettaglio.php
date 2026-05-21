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

/* Determino il link “torna indietro” */
if ($from === 'ricetta' && $numeroRicetta) {
    // Torna alla ricetta da cui sei arrivata
    $backUrl = "../ricette/dettaglio.php?numero=" . $numeroRicetta;
} else {
    // Default: usa il referer (index ingredienti o altro)
    $backUrl = $_SERVER['HTTP_REFERER'] ?? null;
}

/* Mostro il pulsante */
if ($backUrl):
?>
    <a href="<?= $backUrl ?>" class="btn-back">← Torna indietro</a>
<?php endif; ?>

<h1><?= $nomeIngrediente ?></h1>

<h2>Ricette che usano questo ingrediente</h2>

<?php
// Trovo tutte le occorrenze dell'ingrediente
$utilizzi = $db->ingredienti->find(["ingrediente" => $nomeIngrediente]);
?>

<ul>
<?php foreach ($utilizzi as $u): ?>
    
    <?php
    // Recupero la ricetta collegata
    $ricetta = $db->ricette->findOne(["numero" => $u['numeroRicetta']]);
    ?>

    <li>
        <?= $u['quantità'] ?> —
        <a href="../ricette/dettaglio.php?numero=<?= $ricetta['numero'] ?>">
            <?= $ricetta['titolo'] ?>
        </a>
    </li>

<?php endforeach; ?>
</ul>

<a href="index.php" class="btn-category">← Torna agli ingredienti</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
