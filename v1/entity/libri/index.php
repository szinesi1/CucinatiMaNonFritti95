<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

// Recupero tutti i libri
$libri = $db->libri->find();
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
    // Conta pagine del libro
    $numPagine = $db->pagine->countDocuments([
        "libro" => $libro['codISBN']
    ]);

    // Conta ricette pubblicate nel libro
    $numRicette = $db->ricettaPubblicata->countDocuments([
        "libro" => $libro['codISBN']
    ]);
    ?>

    <tr>
        <td>
            <a href="dettaglio.php?isbn=<?= $libro['codISBN'] ?>">
                <?= $libro['titolo'] ?>
            </a>
        </td>
        <td><?= $libro['anno'] ?></td>
        <td><?= $numPagine ?></td>
        <td><?= $numRicette ?></td>
    </tr>

<?php endforeach; ?>

</table>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
