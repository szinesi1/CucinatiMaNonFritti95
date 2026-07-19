<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

// Raggruppo ingredienti per nome e conto quante ricette li usano
$ingredienti = $db->ingredienti->aggregate([
    [
        '$group' => [
            '_id' => '$ingrediente',
            'conteggio' => ['$sum' => 1]
        ]
    ],
    [
        '$sort' => ['_id' => 1]
    ]
]);
?>

<h1>Elenco Ingredienti</h1>

<ul>
<?php foreach ($ingredienti as $ing): ?>
    <li>
        <a href="dettaglio.php?ingrediente=<?= urlencode($ing['_id']) ?>">
            <?= $ing['_id'] ?>
        </a>
        — usato in <?= $ing['conteggio'] ?> ricette
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
