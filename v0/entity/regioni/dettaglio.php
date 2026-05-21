<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codRegione = $_GET['regione'];

// Recupero la regione tramite COD
$regione = $db->regioni->findOne(["cod" => $codRegione]);
?>

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
            <a href="../ricette/dettaglio.php?numero=<?= $ricetta['numero'] ?>">
                <?= $ricetta['titolo'] ?>
            </a>
        <?php else: ?>
            <em>Ricetta non trovata</em>
        <?php endif; ?>
    </li>

<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
