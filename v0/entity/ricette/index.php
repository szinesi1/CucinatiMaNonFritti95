<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$ricette = $db->ricette->find();
?>

<h1>Elenco Ricette</h1>

<ul>
<?php foreach ($ricette as $r): ?>
    <li>
        <a href="dettaglio.php?numero=<?= $r['numero'] ?>">
            <?= $r['titolo'] ?> (<?= $r['tipo'] ?>)
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
