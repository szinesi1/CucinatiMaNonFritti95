<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$numero = isset($_GET['numero']) ? intval($_GET['numero']) : 0;

// Recupero ricetta
$ricetta = $db->ricette->findOne(["numero" => $numero]);

if (!$ricetta) {
    echo "<p><em>Ricetta non trovata.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

$from = $_GET['from'] ?? null;

if ($from === 'regione' && isset($_GET['cod'])) {
    $backUrl = "pages/regioni/dettaglio.php?regione=" . urlencode($_GET['cod']);
} elseif ($from === 'ingrediente' && isset($_GET['ingrediente'])) {
    $backUrl = "../ingredienti/dettaglio.php?ingrediente=" . urlencode($_GET['ingrediente']);
} elseif ($from === 'libro' && isset($_GET['isbn'])) {
    $backUrl = "pages/libri/dettaglio.php?isbn=" . urlencode($_GET['isbn']);
} else {
    $backUrl = $_SERVER['HTTP_REFERER'] ?? null;
}
?>

<?php if ($backUrl): ?>
    <a href="<?= $backUrl ?>" class="btn-back">← Torna indietro</a>
<?php endif; ?>

<h1><?= $ricetta['titolo'] ?></h1>

<?php
$immagini = isset($ricetta['immagini']) && is_array($ricetta['immagini']) ? $ricetta['immagini'] : [];
?>

<?php if (count($immagini) > 0): ?>
<div class="carousel" id="carousel">
    <div class="carousel-inner">

        <?php foreach ($immagini as $index => $img): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <img src="/CucinatiMaNonFritti95/v0/img/ricette/<?= $img ?>">            
            </div>
        <?php endforeach; ?>

    </div>

    <button class="carousel-control prev" onclick="prevSlide()">&#10094;</button>
    <button class="carousel-control next" onclick="nextSlide()">&#10095;</button>
</div>
<?php endif; ?>

<p><strong>Tipo:</strong> <?= $ricetta['tipo'] ?></p>

<h2>Ingredienti</h2>

<?php
$ingredienti = $db->ingredienti->find(["numeroRicetta" => $numero]);
?>

<table>
    <tbody>
    <?php foreach ($ingredienti as $ing): ?>
        <tr>

            <td><?= $ing['quantità'] ?></td>

            <td>
                <a href="pages/ingredienti/dettaglio.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&from=ricetta&numero=<?= $ricetta['numero'] ?>">
                    <?= $ing['ingrediente'] ?>
                </a>
            </td>

            <td>
                <a href="pages/ingredienti/edit.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&numero=<?= $numero ?>">
                    Modifica
                </a>

                |

                <a href="pages/ingredienti/delete.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&numero=<?= $numero ?>"
                   onclick="return confirm('Eliminare questo ingrediente dalla ricetta?')">
                    Elimina
                </a>
            </td>

        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p>
    <a href="pages/ingredienti/create.php?numero=<?= $numero ?>">
        ➕ Aggiungi ingrediente
    </a>
</p>

<h2>Regione</h2>

<?php
$regioneRicetta = $db->ricettaRegionale->findOne(["numeroRicetta" => $numero]);

if ($regioneRicetta) {
    $regione = $db->regioni->findOne(["cod" => $regioneRicetta['cod']]);
}
?>

<?php if (isset($regione)): ?>
    <p>
        <a href="pages/regioni/dettaglio.php?regione=<?= $regione['cod'] ?>&from=ricetta&numero=<?= $ricetta['numero'] ?>">
            <?= $regione['nome'] ?>
        </a>
    </p>
<?php else: ?>
    <p><em>Nessuna regione associata</em></p>
<?php endif; ?>


<h2>Pubblicata in</h2>

<?php
$pubblicazioni = $db->ricettaPubblicata->find(["numeroRicetta" => $numero]);
?>

<ul>
<?php foreach ($pubblicazioni as $pub): ?>
    <?php $libro = $db->libri->findOne(["codISBN" => $pub['libro']]); ?>
    <li>
        <a href="pages/libri/dettaglio.php?isbn=<?= $libro['codISBN'] ?>&from=ricetta&numero=<?= $ricetta['numero'] ?>">
            <?= $libro['titolo'] ?> — Pagina <?= $pub['numeroPagina'] ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<a href="index.php" class="btn-category">← Torna alle ricette</a>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
<script src="/js/carousel.js"></script>
