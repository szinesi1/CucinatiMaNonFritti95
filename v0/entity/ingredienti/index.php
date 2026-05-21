<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$ingredienti = $db->ingredienti->find();
?>

<h1>Elenco Ingredienti</h1>

<ul>
<?php foreach ($ingredienti as $ing): ?>
    <li>
        <a href="dettaglio.php?id=<?= $ing['_id'] ?>">
            <?= $ing['ingrediente'] ?> (<?= $ing['quantità'] ?>)
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
