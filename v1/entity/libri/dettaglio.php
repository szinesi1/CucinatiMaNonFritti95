<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codISBN = $_GET['isbn'] ?? null;

if (!$codISBN) {
    echo "<p><em>Libro non specificato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

// Recupero il libro
$libro = $db->libri->findOne(["codISBN" => $codISBN]);

if (!$libro) {
    echo "<p><em>Libro non trovato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

$from = $_GET['from'] ?? null;

if ($from === 'ricetta' && isset($_GET['numero'])) {
    // Torna alla ricetta da cui sei arrivata
    $backUrl = "../ricette/dettaglio.php?numero=" . $_GET['numero'];

} elseif ($from === 'regione' && isset($_GET['cod'])) {
    // Torna alla regione
    $backUrl = "../regioni/dettaglio.php?regione=" . $_GET['cod'];

} elseif ($from === 'ingrediente' && isset($_GET['ingrediente'])) {
    // Torna all'ingrediente
    $backUrl = "../ingredienti/dettaglio.php?ingrediente=" . urlencode($_GET['ingrediente']);

} else {
    // Default: referer (index libri o altro)
    $backUrl = $_SERVER['HTTP_REFERER'] ?? null;
}
?>

<?php if ($backUrl): ?>
    <a href="<?= $backUrl ?>" class="btn-back">← Torna indietro</a>
<?php endif; ?>

<h1><?= $libro['titolo'] ?></h1>

<p><strong>Anno:</strong> <?= $libro['anno'] ?></p>

<h2>Ricette presenti nel libro</h2>

<?php
// Trovo tutte le ricette pubblicate in questo libro
$pubblicazioni = $db->ricettaPubblicata->find(
    ["libro" => $codISBN],
    ["sort" => ["numeroPagina" => 1]]
);
?>

<ul>
<?php foreach ($pubblicazioni as $pub): ?>

    <?php
    // Recupero la ricetta collegata
    $ricetta = $db->ricette->findOne(["numero" => $pub['numeroRicetta']]);
    ?>

    <li>
        Pagina <?= $pub['numeroPagina'] ?> —
        <?php if ($ricetta): ?>
            <a href="../ricette/dettaglio.php?numero=<?= $ricetta['numero'] ?>&from=libro&isbn=<?= $codISBN ?>">
                <?= $ricetta['titolo'] ?>
            </a>
        <?php else: ?>
            <em>Ricetta non trovata</em>
        <?php endif; ?>
    </li>

<?php endforeach; ?>
</ul>

<a href="index.php" class="btn-category">← Torna ai libri</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
