<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codISBN = $_GET['isbn'] ?? null;

if (!$codISBN) {
    echo "<p><em>Libro non specificato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* =========================
   LIBRO
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM Libri
    WHERE codISBN = ?
");

$stmt->execute([$codISBN]);
$libro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$libro) {
    echo "<p><em>Libro non trovato.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* =========================
   BACK URL
========================= */

$from = $_GET['from'] ?? null;

if ($from === 'ricetta' && isset($_GET['numero'])) {

    $backUrl = "pages/ricette/dettaglio.php?numero=" . urlencode($_GET['numero']);

} elseif ($from === 'regione' && isset($_GET['cod'])) {

    $backUrl = "pages/regioni/dettaglio.php?regione=" . urlencode($_GET['cod']);

} elseif ($from === 'ingrediente' && isset($_GET['ingrediente'])) {

    $backUrl = "pages/ingredienti/dettaglio.php?ingrediente=" . urlencode($_GET['ingrediente']);

} else {

    $backUrl = $_SERVER['HTTP_REFERER'] ?? 'pages/libri/index.php';
}

/* =========================
   RICETTE NEL LIBRO (JOIN LOGICO)
========================= */

$stmt = $pdo->prepare("
    SELECT 
        P.numeroPagina,
        R.numero,
        R.titolo
    FROM Pagine P
    LEFT JOIN Ricette R ON R.numero = P.numeroRicetta
    WHERE P.libro = ?
    ORDER BY P.numeroPagina ASC
");

$stmt->execute([$codISBN]);
$pubblicazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
    ← Torna indietro
</a>

<h2><?= htmlspecialchars($libro['titolo']) ?></h2>

<p>
    <strong>Anno:</strong>
    <?= htmlspecialchars($libro['anno']) ?>
</p>

<h3>Ricette presenti nel libro</h3>

<ul>

<?php foreach ($pubblicazioni as $pub): ?>

    <li>
        Pagina <?= htmlspecialchars($pub['numeroPagina']) ?> —

        <?php if (!empty($pub['numero'])): ?>

            <a href="pages/ricette/dettaglio.php?numero=<?= urlencode($pub['numero']) ?>&from=libro&isbn=<?= urlencode($codISBN) ?>">
                <?= htmlspecialchars($pub['titolo']) ?>
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