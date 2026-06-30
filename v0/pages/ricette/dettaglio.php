<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$numero = intval($_GET['numero'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM Ricette WHERE numero = ?");
$stmt->execute([$numero]);
$ricetta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ricetta) {
    echo "Ricetta non trovata";
    exit;
}
?>

<h2><?= htmlspecialchars($ricetta['titolo']) ?></h2>

/* =========================
   LIBRI
========================= */

<h3>Presente nei libri</h3>

<?php
$stmt = $pdo->prepare("
    SELECT P.numeroPagina, L.codISBN, L.titolo
    FROM Pagine P
    JOIN Libri L ON L.codISBN = P.libro
    WHERE P.numeroRicetta = ?
");

$stmt->execute([$numero]);
$libri = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<ul>
<?php foreach ($libri as $l): ?>
    <li>
        <a href="../libri/dettaglio.php?isbn=<?= $l['codISBN'] ?>">
            <?= htmlspecialchars($l['titolo']) ?>
            — Pag. <?= $l['numeroPagina'] ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>