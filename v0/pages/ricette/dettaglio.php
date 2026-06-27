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

<div class="page-container recipe-detail">

    <?php if ($backUrl): ?>
        <a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
            ← Torna indietro
        </a>
    <?php endif; ?>

    <!-- Header -->
    <header class="recipe-header">
        <h2 class="recipe-title">
            <?= htmlspecialchars($ricetta['titolo']) ?>
        </h2>

        <p class="recipe-type">
            <strong>Tipo:</strong>
            <?= htmlspecialchars($ricetta['tipo']) ?>
        </p>
    </header>

    <!-- Carousel immagini -->
    <?php
    $immagini = isset($ricetta['immagini']) && is_array($ricetta['immagini'])
        ? $ricetta['immagini']
        : [];
    ?>

    <?php if (count($immagini) > 0): ?>
        <section class="recipe-media">
            <div class="carousel" id="carousel">
                <div class="carousel-inner">
                    <?php foreach ($immagini as $index => $img): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="/CucinatiMaNonFritti95/v0/img/ricette/<?= htmlspecialchars($img) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control prev" onclick="prevSlide()">
                    &#10094;
                </button>

                <button class="carousel-control next" onclick="nextSlide()">
                    &#10095;
                </button>
            </div>
        </section>
    <?php endif; ?>

    <!-- Ingredienti -->
    <section class="recipe-section">

        <h3>Ingredienti</h3>

        <?php
        $ingredienti = $db->ingredienti->find([
            "numeroRicetta" => $numero
        ]);
        ?>

        <table class="table-ingredients">
            <tbody>
            <?php foreach ($ingredienti as $ing): ?>
                <tr class="ingredient-row">
                    <td class="ingredient-qty">
                        <?= htmlspecialchars($ing['quantità']) ?>
                    </td>
                    <td class="ingredient-name">
                        <a href="pages/ingredienti/dettaglio.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&from=ricetta&numero=<?= $ricetta['numero'] ?>">
                            <?= htmlspecialchars($ing['ingrediente']) ?>
                        </a>
                    </td>
                    <td class="ingredient-actions">
                        <a class="btn btn-edit"
                           href="pages/ingredienti/edit.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&numero=<?= $numero ?>">
                            ✏️ Modifica
                        </a>
                        <a class="btn btn-delete"
                           href="pages/ingredienti/delete.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&numero=<?= $numero ?>"
                           onclick="return confirm('Eliminare questo ingrediente dalla ricetta?')">
                            🗑️ Elimina
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p class="add-action">
            <a class="btn btn-add"
               href="pages/ingredienti/create.php?numero=<?= $numero ?>">
                ➕ Aggiungi ingrediente
            </a>
        </p>
    </section>

    <!-- Regione -->
    <section class="recipe-section">

        <h3>Regione</h3>

        <?php
        $regioneRicetta = $db->ricettaRegionale->findOne([
            "numeroRicetta" => $numero
        ]);
        
        $regione = null;
        if ($regioneRicetta) {
            $regione = $db->regioni->findOne([
                "cod" => $regioneRicetta['cod']
            ]);
        }
        ?>

        <?php if ($regione): ?>
            <p class="region-box">
                <a href="pages/regioni/dettaglio.php?regione=<?= $regione['cod'] ?>&from=ricetta&numero=<?= $ricetta['numero'] ?>">
                    <?= htmlspecialchars($regione['nome']) ?>
                </a>
            </p>

        <?php else: ?>
            <p class="empty-state">
                <em>Nessuna regione associata</em>
            </p>
        <?php endif; ?>
    </section>

    <!-- Pubblicazioni -->
    <section class="recipe-section">

        <h3>Pubblicata in</h3>

        <?php
        $pubblicazioni = $db->ricettaPubblicata->find([
            "numeroRicetta" => $numero
        ]);
        ?>

        <ul class="publication-list">
            <?php foreach ($pubblicazioni as $pub):
                $libro = $db->libri->findOne([
                    "codISBN" => $pub['libro']
                ]);

                if (!$libro) {
                    continue;
                }
                ?>
                <li>
                    <a href="pages/libri/dettaglio.php?isbn=<?= $libro['codISBN'] ?>&from=ricetta&numero=<?= $ricetta['numero'] ?>">
                        <?= htmlspecialchars($libro['titolo']) ?>
                        — Pag. <?= htmlspecialchars($pub['numeroPagina']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <div class="page-footer">
        <a href="index.php" class="btn-category">
            ← Torna alle ricette
        </a>
    </div>
</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>

<script src="/js/carousel.js"></script>