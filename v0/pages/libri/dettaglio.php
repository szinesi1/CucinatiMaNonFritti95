<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codISBN = $_GET['isbn'] ?? null;

if (!$codISBN) {
    echo "<p><em>Libro non specificato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

$libro = $db->libri->findOne([
    "codISBN" => $codISBN
]);

if (!$libro) {
    echo "<p><em>Libro non trovato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

$from = $_GET['from'] ?? null;

if ($from === 'ricetta' && isset($_GET['numero'])) {

    $backUrl =
        "pages/ricette/dettaglio.php?numero=" .
        urlencode($_GET['numero']);

} elseif ($from === 'regione' && isset($_GET['cod'])) {

    $backUrl =
        "pages/regioni/dettaglio.php?regione=" .
        urlencode($_GET['cod']);

} elseif ($from === 'ingrediente' && isset($_GET['ingrediente'])) {

    $backUrl =
        "pages/ingredienti/dettaglio.php?ingrediente=" .
        urlencode($_GET['ingrediente']);

} else {

    $backUrl =
        $_SERVER['HTTP_REFERER']
        ?? 'pages/libri/index.php';
}
?>

<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
    ← Torna indietro
</a>

<h1><?= htmlspecialchars($libro['titolo']) ?></h1>

<p>
    <strong>Anno:</strong>
    <?= htmlspecialchars($libro['anno']) ?>
</p>

<h2>Ricette presenti nel libro</h2>

<?php
$pubblicazioni = $db->ricettaPubblicata->find(
    ["libro" => $codISBN],
    ["sort" => ["numeroPagina" => 1]]
);
?>

<ul>

<?php foreach ($pubblicazioni as $pub): ?>

    <?php
    $ricetta = $db->ricette->findOne([
        "numero" => $pub['numeroRicetta']
    ]);
    ?>

    <li>

        Pagina <?= $pub['numeroPagina'] ?> —

        <?php if ($ricetta): ?>

            <a href="pages/ricette/dettaglio.php?numero=<?= urlencode($ricetta['numero']) ?>&from=libro&isbn=<?= urlencode($codISBN) ?>">
                <?= htmlspecialchars($ricetta['titolo']) ?>
            </a>

        <?php else: ?>

            <em>Ricetta non trovata</em>

        <?php endif; ?>

    </li>

<?php endforeach; ?>

</ul>

<a href="pages/libri/index.php" class="btn-category">
    ← Torna ai libri
</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>