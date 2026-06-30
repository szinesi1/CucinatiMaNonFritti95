<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codISBN = $_GET['isbn'] ?? null;

if (!$codISBN) {
    echo "<p>Libro non specificato</p>";
    exit;
}

/* =========================
   LIBRO
========================= */

$stmt = $pdo->prepare("SELECT * FROM Libri WHERE codISBN = ?");
$stmt->execute([$codISBN]);
$libro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$libro) {
    echo "<p>Libro non trovato</p>";
    exit;
}

/* =========================
   RICETTE NEL LIBRO (PAGINE)
========================= */

$stmt = $pdo->prepare("
    SELECT 
        P.numeroPagina,
        R.numero,
        R.titolo
    FROM Pagine P
    JOIN Ricette R ON R.numero = P.numeroRicetta
    WHERE P.libro = ?
    ORDER BY P.numeroPagina ASC
");

$stmt->execute([$codISBN]);
$pubblicazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?= htmlspecialchars($libro['titolo']) ?></h2>

<p><strong>Anno:</strong> <?= htmlspecialchars($libro['anno']) ?></p>

<h3>Ricette nel libro</h3>

<ul>
<?php foreach ($pubblicazioni as $pub): ?>
    <li>
        Pagina <?= $pub['numeroPagina'] ?> —
        <a href="../ricette/dettaglio.php?numero=<?= $pub['numero'] ?>">
            <?= htmlspecialchars($pub['titolo']) ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>