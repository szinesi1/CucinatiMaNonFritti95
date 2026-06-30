<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$numero = isset($_GET['numero']) ? intval($_GET['numero']) : 0;

/* =========================
   RICETTA
========================= */

$stmt = $pdo->prepare("SELECT * FROM Ricette WHERE numero = ?");
$stmt->execute([$numero]);
$ricetta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ricetta) {
    echo "<p><em>Ricetta non trovata.</em></p>";
    include __DIR__ . '/../../interface/footer.php';
    exit;
}

/* =========================
   BACK URL
========================= */

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

<!-- =========================
     HEADER
========================= -->

<header class="recipe-header">
    <h2 class="recipe-title">
        <?= htmlspecialchars($ricetta['titolo']) ?>
    </h2>

    <p class="recipe-type">
        <strong>Tipo:</strong>
        <?= htmlspecialchars($ricetta['tipo']) ?>
    </p>
</header>

<!-- =========================
     IMMAGINI
========================= -->

<?php
$immagini = !empty($ricetta['immagini'])
    ? json_decode($ricetta['immagini'], true)
    : [];
?>

<?php if (!empty($immagini)): ?>
<section class="recipe-media">
    <div class="carousel" id="carousel">
        <div class="carousel-inner">

            <?php foreach ($immagini as $index => $img): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <img src="/img/ricette/<?= htmlspecialchars($img) ?>">
                </div>
            <?php endforeach; ?>

        </div>

        <button class="carousel-control prev" onclick="prevSlide()">&#10094;</button>
        <button class="carousel-control next" onclick="nextSlide()">&#10095;</button>

    </div>
</section>
<?php endif; ?>

<!-- =========================
     INGREDIENTI
========================= -->

<section class="recipe-section">

<h3>Ingredienti</h3>

<?php
$stmt = $pdo->prepare("
    SELECT ingrediente, quantita
    FROM Ingredienti
    WHERE numeroRicetta = ?
");
$stmt->execute([$numero]);
$ingredienti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table-ingredients">
<tbody>

<?php foreach ($ingredienti as $ing): ?>

<tr class="ingredient-row">

    <td class="ingredient-qty">
        <?= htmlspecialchars($ing['quantita']) ?>
    </td>

    <td class="ingredient-name">
        <a href="pages/ingredienti/dettaglio.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&from=ricetta&numero=<?= $numero ?>">
            <?= htmlspecialchars($ing['ingrediente']) ?>
        </a>
    </td>

    <td class="ingredient-actions">

        <a class="btn btn-edit"
           href="pages/ingredienti/edit.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>&numero=<?= $numero ?>">
            ✏️ Modifica
        </a>

        <a href="#"
           class="btn btn-delete js-delete-btn"
           data-id="<?= $ing['idIngrediente'] ?>"
           data-ingrediente="<?= htmlspecialchars($ing['ingrediente']) ?>"
           data-numero="<?= $numero ?>">
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

<!-- =========================
     REGIONE
========================= -->

<section class="recipe-section">

<h3>Regione</h3>

<?php
$stmt = $pdo->prepare("
    SELECT r.*
    FROM RicettaRegionale rr
    JOIN Regioni r ON rr.cod = r.cod
    WHERE rr.numeroRicetta = ?
");
$stmt->execute([$numero]);
$regione = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php if ($regione): ?>
    <p class="region-box">
        <a href="pages/regioni/dettaglio.php?regione=<?= $regione['cod'] ?>&from=ricetta&numero=<?= $numero ?>">
            <?= htmlspecialchars($regione['nome']) ?>
        </a>
    </p>
<?php else: ?>
    <p><em>Nessuna regione associata</em></p>
<?php endif; ?>

</section>

<!-- =========================
     LIBRI
========================= -->

<section class="recipe-section">

<h3>Pubblicata in</h3>

<?php
$stmt = $pdo->prepare("
    SELECT p.numeroPagina, l.codISBN, l.titolo
    FROM Pagine p
    JOIN Libri l ON p.libro = l.codISBN
    WHERE p.numeroRicetta = ?
");
$stmt->execute([$numero]);
$pubblicazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<ul class="publication-list">

<?php foreach ($pubblicazioni as $pub): ?>

    <li>
        <a href="pages/libri/dettaglio.php?isbn=<?= urlencode($pub['codISBN']) ?>&from=ricetta&numero=<?= $numero ?>">
            <?= htmlspecialchars($pub['titolo']) ?>
            — Pag. <?= htmlspecialchars($pub['numeroPagina']) ?>
        </a>
    </li>

<?php endforeach; ?>

</ul>

</section>

<!-- =========================
     FOOTER
========================= -->

<div class="page-footer">
    <a href="pages/ricette/index.php" class="btn-category">
        ← Torna alle ricette
    </a>
</div>

</div>

<!-- =========================
     MODAL DELETE INGREDIENTE
========================= -->

<div id="deleteModal" class="modal hidden">

    <div class="modal-box">

        <p>
            Vuoi eliminare <strong id="modalIngrediente"></strong>?
        </p>

        <form method="POST" action="/pages/ingredienti/delete.php">

            <input type="hidden" name="idIngrediente" id="inputIdIngrediente">
            <input type="hidden" name="numero" id="inputNumero">

            <button type="submit" class="btn btn-delete">
                Elimina
            </button>

            <button type="button" id="closeModal" class="btn btn-save">
                Annulla
            </button>

        </form>

    </div>

</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>

<script src="/js/carousel.js"></script>
<script src="/js/modal-delete.js"></script>