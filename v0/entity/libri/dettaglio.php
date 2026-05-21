<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$codISBN = $_GET['isbn'];

// Recupero il libro
$libro = $db->libri->findOne(["codISBN" => $codISBN]);
?>

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
