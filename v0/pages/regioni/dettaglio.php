<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codRegione = $_GET['regione'] ?? null;

if (!$codRegione) {
    echo "<p><em>Regione non specificata.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* =========================
   REGIONE
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM Regioni
    WHERE cod = ?
");

$stmt->execute([$codRegione]);
$regione = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$regione) {
    echo "<p><em>Regione non trovata.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* =========================
   BACK URL
========================= */

$backUrl = $_SERVER['HTTP_REFERER'] ?? 'pages/regioni/index.php';

/* =========================
   RICETTE REGIONALI (JOIN)
========================= */

$stmt = $pdo->prepare("
    SELECT 
        RR.numeroRicetta,
        R.titolo,
        R.numero
    FROM RicettaRegionale RR
    LEFT JOIN Ricette R ON R.numero = RR.numeroRicetta
    WHERE RR.cod = ?
");

$stmt->execute([$codRegione]);
$ricetteTipiche = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
    ← Torna indietro
</a>

<h2><?= htmlspecialchars($regione['nome']) ?></h2>

<h3>Ricette della regione</h3>

<ul>

<?php foreach ($ricetteTipiche as $rt): ?>

    <li>

        <?php if (!empty($rt['numero'])): ?>

            <a href="../ricette/dettaglio.php?numero=<?= urlencode($rt['numero']) ?>&from=regione&cod=<?= urlencode($regione['cod']) ?>">
                <?= htmlspecialchars($rt['titolo']) ?>
            </a>

        <?php else: ?>

            <em>Ricetta non trovata</em>

        <?php endif; ?>

    </li>

<?php endforeach; ?>

</ul>

<a href="../regioni/index.php" class="btn-category">
    ← Torna alle regioni
</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>