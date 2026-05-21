<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$regioni = $db->regioni->find();
?>

<h1>Elenco Regioni</h1>

<table>
    <tr>
        <th>Regione</th>
        <th>Ricette tipiche</th>
    </tr>

<?php foreach ($regioni as $regione): ?>

    <?php
    // Conta ricette tipiche usando il COD della regione
    $numRicette = $db->ricettaRegionale->countDocuments([
        "cod" => $regione['cod']
    ]);
    ?>

    <tr>
        <td>
            <a href="dettaglio.php?regione=<?= $regione['cod'] ?>">
                <?= $regione['nome'] ?>
            </a>
        </td>
        <td><?= $numRicette ?></td>
    </tr>

<?php endforeach; ?>

</table>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
