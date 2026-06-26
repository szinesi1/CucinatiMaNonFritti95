<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$regioni = $db->regioni->find([], [
    'sort' => ['nome' => 1]
]);
?>

<h2>Elenco Regioni</h2>

<table>
    <tr>
        <th>Regione</th>
        <th>Ricette tipiche</th>
    </tr>

    <?php foreach ($regioni as $regione): ?>

        <?php
        $numRicette = $db->ricettaRegionale->countDocuments([
            "cod" => $regione['cod']
        ]);
        ?>

        <tr>
            <td>
                <a href="pages/regioni/dettaglio.php?regione=<?= urlencode($regione['cod']) ?>">
                    <?= htmlspecialchars($regione['nome']) ?>
                </a>
            </td>

            <td><?= $numRicette ?></td>
        </tr>

    <?php endforeach; ?>

</table>

<?php include __DIR__ . '/../../interface/footer.php'; ?>