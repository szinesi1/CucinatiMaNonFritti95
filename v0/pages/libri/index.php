<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

// Recupero tutti i libri ordinati per titolo
$libri = $db->libri->find([], [
    'sort' => ['titolo' => 1]
]);
?>

<h1>Elenco Libri</h1>

<table>
    <tr>
        <th>Titolo</th>
        <th>Anno</th>
        <th>Pagine</th>
        <th>Ricette pubblicate</th>
    </tr>

    <?php foreach ($libri as $libro): ?>

        <?php
        $numPagine = $db->pagine->countDocuments([
            "libro" => $libro['codISBN']
        ]);

        $numRicette = $db->ricettaPubblicata->countDocuments([
            "libro" => $libro['codISBN']
        ]);
        ?>

        <tr>
            <td>
                <a href="pages/libri/dettaglio.php?isbn=<?= urlencode($libro['codISBN']) ?>">
                    <?= htmlspecialchars($libro['titolo']) ?>
                </a>
            </td>

            <td><?= htmlspecialchars($libro['anno']) ?></td>
            <td><?= $numPagine ?></td>
            <td><?= $numRicette ?></td>
        </tr>

    <?php endforeach; ?>

</table>

<?php include __DIR__ . '/../../interface/footer.php'; ?>