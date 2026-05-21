<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$numero = intval($_GET['numero']);

// Recupero ricetta
$ricetta = $db->ricette->findOne(["numero" => $numero]);
?>

<h1><?= $ricetta['titolo'] ?></h1>

<p><strong>Tipo:</strong> <?= $ricetta['tipo'] ?></p>

<h2>Ingredienti</h2>

<?php
$ingredienti = $db->ingredienti->find(["numeroRicetta" => $numero]);
?>

<ul>
<?php foreach ($ingredienti as $ing): ?>
    <li><?= $ing['ingrediente'] ?> (<?= $ing['quantità'] ?>)</li>
<?php endforeach; ?>
</ul>

<h2>Regione</h2>

<?php
$regRel = $db->ricettaRegionale->findOne(["ricetta" => $numero]);
?>

<?php if ($regRel): ?>
    <?php $regione = $db->regioni->findOne(["cod" => $regRel['regione']]); ?>
    <p>
        <a href="../regioni/dettaglio.php?cod=<?= $regione['cod'] ?>">
            <?= $regione['nome'] ?>
        </a>
    </p>
<?php else: ?>
    <p><em>Nessuna regione associata</em></p>
<?php endif; ?>

<h2>Pubblicata in</h2>

<?php
$pubblicazioni = $db->ricettaPubblicata->find(["numeroRicetta" => $numero]);
?>

<ul>
<?php foreach ($pubblicazioni as $pub): ?>
    <?php $libro = $db->libri->findOne(["codISBN" => $pub['libro']]); ?>
    <li>
        <a href="../libri/dettaglio.php?isbn=<?= $libro['codISBN'] ?>">
            <?= $libro['titolo'] ?> — Pagina <?= $pub['numeroPagina'] ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
