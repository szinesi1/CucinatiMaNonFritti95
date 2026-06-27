<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* =========================
   FILTRI
========================= */

$search = trim($_GET['search'] ?? '');
$selectedZone = $_GET['zona'] ?? [];

if (!is_array($selectedZone)) {
    $selectedZone = [];
}

/* zone disponibili (coerenti e riusabili) */
$zone = [
    "Nord",
    "Centro",
    "Sud",
    "Isole"
];

/* query base */
$baseQuery = [];

/* filtro ricerca (senza regex: match parziale “manuale” lato PHP) */
if (!empty($search)) {
    $baseQuery['nome'] = [
        '$regex' => $search,
        '$options' => 'i'
    ];
}

/* filtro zona */
if (!empty($selectedZone)) {
    $baseQuery['zona'] = [
        '$in' => $selectedZone
    ];
}

$showFilters = !empty($selectedZone) || !empty($search);

/* query regioni */
$regioni = $db->regioni->find($baseQuery, [
    'sort' => ['nome' => 1]
]);
?>

<!-- =========================
     FILTRI UI
========================= -->

<form method="GET" class="filters">
    <div class="filters-top">
        <input
            type="text"
            name="search"
            class="filters-input"
            placeholder="Cerca regione..."
            value="<?= htmlspecialchars($search) ?>">
        <button
            type="button"
            id="toggleFilters"
            class="secondary-button">
            <?= $showFilters ? 'Meno filtri' : 'Più filtri' ?>
        </button>
    </div>

    <div
        id="advancedFilters"
        class="advanced-filters <?= $showFilters ? 'open' : '' ?>">
        <fieldset>
            <legend>Zona geografica</legend>
            <div class="filter-group zone-group">
                <?php foreach ($zone as $z): ?>
                    <label>
                        <input
                            type="checkbox"
                            name="zona[]"
                            value="<?= htmlspecialchars($z) ?>"
                            <?= in_array($z, $selectedZone) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($z) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>
    </div>

    <div class="filters-actions">
        <button type="submit" class="btn">
            Filtra
        </button>
        <a href="/CucinatiMaNonFritti95/v0/pages/regioni/index.php" class="reset-button">
            Reset
        </a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleFilters');
    const filters = document.getElementById('advancedFilters');
    toggleBtn.addEventListener('click', function () {
        filters.classList.toggle('open');

        if (filters.classList.contains('open')) {
            toggleBtn.textContent = 'Meno filtri';
        } else {
            toggleBtn.textContent = 'Più filtri';
        }
    });
});
</script>

<h2>Regioni italiane</h2>

<!-- =========================
     LISTA REGIONI
========================= -->

<div class="card-grid">
<?php foreach ($regioni as $regione): ?>
<?php
$numRicette = $db->ricettaRegionale->countDocuments([
    "cod" => $regione["cod"]
]);
?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            🗺️ <?= htmlspecialchars($regione['nome']) ?>
        </h5>
        <p class="card-text">
            Zona: <?= htmlspecialchars($regione['zona']) ?>
        </p>
        <p class="card-text">
            🍽️ <?= $numRicette ?> ricette
        </p>
        <a
            href="pages/regioni/dettaglio.php?regione=<?= urlencode($regione['cod']) ?>"
            class="card-button">
            Visualizza
        </a>
    </div>
</div>

<?php endforeach; ?>

</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>