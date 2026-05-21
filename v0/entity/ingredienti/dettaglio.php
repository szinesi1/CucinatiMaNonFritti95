<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

if (!isset($_GET['ingrediente'])) {
    echo "<p>Ingrediente non specificato.</p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

$nomeIngrediente = $_GET['ingrediente'];
?>

<h1><?= $nomeIngrediente ?></h1>

<h2>Ricette che usano questo ingrediente</h2>

<?php
// Trovo tutte le occorrenze dell'ingrediente
$utilizzi = $db->ingredienti->find(["ingrediente" => $nomeIngrediente]);
?>

<ul>
<?php foreach ($utilizzi as $u): ?>

    <?php
    // Recupero la ricetta collegata
    $ricetta = $db->ricette->findOne(["numero" => $u['numeroRicetta']]);
    ?>

    <li>
        <?= $u['quantità'] ?> —
        <a href="../ricette/dettaglio.php?numero=<?= $ricetta['numero'] ?>">
            <?= $ricetta['titolo'] ?>
        </a>
    </li>

<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
