<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

// Tipologie in ordine desiderato + titolo plurale
$tipi = [
    "antipasto" => "Antipasti",
    "primo"     => "Primi",
    "secondo"   => "Secondi",
    "contorno"  => "Contorni",
    "dolce"     => "Dolci"
];

// =========================
// FILTRI
// =========================

$search = trim($_GET['search'] ?? '');

$selectedTipi = $_GET['tipo'] ?? [];
$selectedRegioni = $_GET['regione'] ?? [];

if (!is_array($selectedTipi)) {
    $selectedTipi = [];
}

if (!is_array($selectedRegioni)) {
    $selectedRegioni = [];
}

$regioni = [
    "Abruzzo",
    "Basilicata",
    "Calabria",
    "Campania",
    "Emilia-Romagna",
    "Friuli-Venezia Giulia",
    "Lazio",
    "Liguria",
    "Lombardia",
    "Marche",
    "Molise",
    "Piemonte",
    "Puglia",
    "Sardegna",
    "Sicilia",
    "Toscana",
    "Trentino-Alto Adige",
    "Umbria",
    "Valle d'Aosta",
    "Veneto"
];

$baseQuery = [];

if (!empty($search)) {
    $baseQuery['titolo'] = [
        '$regex' => $search,
        '$options' => 'i'
    ];
}

if (!empty($selectedTipi)) {
    $baseQuery['tipo'] = [
        '$in' => $selectedTipi
    ];
}

if (!empty($selectedRegioni)) {
    $baseQuery['regione'] = [
        '$in' => $selectedRegioni
    ];
}

$showFilters =
    !empty($selectedTipi) ||
    !empty($selectedRegioni);

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
            placeholder="Cerca ricetta..."
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
        <fieldset class="tipologie-fieldset">
            <legend>Tipologia</legend>
            <div class="filter-group tipologie-group">
                <?php foreach ($tipi as $key => $label): ?>
                    <label>
                        <input
                            type="checkbox"
                            name="tipo[]"
                            value="<?= htmlspecialchars($key) ?>"
                            <?= in_array($key, $selectedTipi) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <fieldset>
            <legend>Regione</legend>
            <div class="filter-group regioni-group">
                <?php foreach ($regioni as $regione): ?>
                    <label>
                        <input
                            type="checkbox"
                            name="regione[]"
                            value="<?= htmlspecialchars($regione) ?>"
                            <?= in_array($regione, $selectedRegioni) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($regione) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>
    </div>
    <div class="filters-actions">
        <button type="submit" class="btn">
            Filtra
        </button>
        <a href="/CucinatiMaNonFritti95/v0/pages/ricette/index.php" class="reset-button">
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

<h2>Ricette per tipologia</h2>

<!-- =========================
     LISTA RICETTE
========================= -->

<?php foreach ($tipi as $tipo => $titolo): ?>

    <h3 class="mt-4"><?= htmlspecialchars($titolo) ?></h3>

    <?php
    $tipoQuery = $baseQuery;
    // Se NON hai selezionato un tipo dal filtro,
    // mantieni il raggruppamento per sezioni
    if (empty($selectedTipo)) {
        $tipoQuery['tipo'] = $tipo;
    }

    $ricette = $db->ricette->find(
        $tipoQuery,
        ["sort" => ["titolo" => 1]]
    );
    ?>

    <div class="card-grid">
        <?php foreach ($ricette as $r): ?>
            <?php
            if (
                isset($r['immagini']) &&
                is_array($r['immagini']) &&
                count($r['immagini']) > 0
            ) {
                $img = "img/ricette/" . $r['immagini'][0];
            } else {
                $img = "img/default.jpg";
            }
            ?>
            <div class="card">
                <img
                    src="<?= htmlspecialchars($img) ?>"
                    alt="<?= htmlspecialchars($r['titolo']) ?>">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= htmlspecialchars($r['titolo']) ?>
                    </h5>
                    <a
                        href="pages/ricette/dettaglio.php?numero=<?= urlencode($r['numero']) ?>"
                        class="card-button">
                        Vedi ricetta
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php include __DIR__ . '/../../interface/footer.php'; ?>