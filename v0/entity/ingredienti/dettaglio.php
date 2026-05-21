<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$numeroRicetta = intval($_GET['ricetta']);
$numeroIng = intval($_GET['numero']);

$ingrediente = $db->ingredienti->findOne([
    "numeroRicetta" => $numeroRicetta,
    "numero" => $numeroIng
]);
?>

<h1>Ingrediente</h1>

<p><strong>Nome:</strong> <?= $ingrediente['ingrediente'] ?></p>
<p><strong>Quantità:</strong> <?= $ingrediente['quantità'] ?></p>

<h2>Ricetta collegata</h2>

<?php
$ricetta = $db->ricette->findOne(["numero" => $numeroRicetta]);
?>

<p>
    <a href="../ricette/dettaglio.php?numero=<?= $ricetta['numero'] ?>">
        <?= $ricetta['titolo'] ?>
    </a>
</p>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
