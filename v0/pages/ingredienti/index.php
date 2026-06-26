<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

// Raggruppo ingredienti per nome e conto quante ricette li usano
$ingredienti = $db->ingredienti->aggregate([
    [
        '$group' => [
            '_id' => '$ingrediente',
            'conteggio' => ['$sum' => 1]
        ]
    ],
    [
        '$sort' => ['_id' => 1]
    ]
]);

// =========================
// FILTRI
// =========================

$search = trim($_GET['search'] ?? '');

$selectedTipi = $_GET['tipo'] ?? [];
$selectedOrigini = $_GET['origine'] ?? [];

if (!is_array($selectedTipi)) {
    $selectedTipi = [];
}

if (!is_array($selectedOrigini)) {
    $selectedOrigini = [];
}

/* Tipologie ingredienti */

$tipi = [
    "Verdura",
    "Frutta",
    "Carne",
    "Pesce",
    "Latticini",
    "Cereali",
    "Legumi",
    "Spezie",
    "Erbe aromatiche",
    "Condimenti"
];

/* Origini */

$origini = [
    "Vegetale",
    "Animale",
    "Minerale"
];

$match = [];

/* Ricerca ingrediente */

if (!empty($search)) {

    $match['ingrediente'] = [
        '$regex' => $search,
        '$options' => 'i'
    ];
}

/* Tipologie multiple */

if (!empty($selectedTipi)) {

    $match['tipologia'] = [
        '$in' => $selectedTipi
    ];
}

/* Origini multiple */

if (!empty($selectedOrigini)) {

    $match['origine'] = [
        '$in' => $selectedOrigini
    ];
}

$showFilters =
    !empty($selectedTipi) ||
    !empty($selectedOrigini);

/* Query Mongo */

$pipeline = [];

if (!empty($match)) {

    $pipeline[] = [
        '$match' => $match
    ];
}

$pipeline[] = [
    '$group' => [
        '_id' => '$ingrediente',
        'tipologia' => [
            '$first' => '$tipologia'
        ],
        'origine' => [
            '$first' => '$origine'
        ],
        'conteggio' => [
            '$sum' => 1
        ]
    ]
];

$pipeline[] = [
    '$sort' => [
        '_id' => 1
    ]
];

$ingredienti = $db->ingredienti->aggregate($pipeline);
?>

<!-- =========================
     FILTRI UI
========================= -->

<form method="GET" class="filters">

    <div class="filters-top">

        <input
            type="text"
            name="search"
            placeholder="Cerca ingrediente..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button
            type="button"
            id="toggleFilters"
            class="secondary-button"
        >
            <?= $showFilters ? 'Meno filtri' : 'Più filtri' ?>
        </button>

    </div>

    <div
        id="advancedFilters"
        class="advanced-filters <?= $showFilters ? 'open' : '' ?>"
    >

        <fieldset class="tipologie-fieldset">

            <legend>Origine</legend>

            <div class="filter-group origine-group">

                <?php foreach ($origini as $origine): ?>

                    <label>

                        <input
                            type="checkbox"
                            name="origine[]"
                            value="<?= htmlspecialchars($origine) ?>"
                            <?= in_array($origine, $selectedOrigini) ? 'checked' : '' ?>
                        >

                        <?= htmlspecialchars($origine) ?>

                    </label>

                <?php endforeach; ?>

            </div>

        </fieldset>
        
        <fieldset>

            <legend>Tipologia</legend>

            <div class="filter-group tipologia-ing-group">

                <?php foreach ($tipi as $tipo): ?>

                    <label>

                        <input
                            type="checkbox"
                            name="tipo[]"
                            value="<?= htmlspecialchars($tipo) ?>"
                            <?= in_array($tipo, $selectedTipi) ? 'checked' : '' ?>
                        >

                        <?= htmlspecialchars($tipo) ?>

                    </label>

                <?php endforeach; ?>

            </div>

        </fieldset>

    </div>

    <div class="filters-actions">

        <button type="submit">
            Filtra
        </button>

        <a href="/CucinatiMaNonFritti95/v0/pages/ingredienti/index.php" class="reset-button">
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

        toggleBtn.textContent =
            filters.classList.contains('open')
                ? 'Meno filtri'
                : 'Più filtri';

    });

});

</script>

<h2>Elenco Ingredienti</h2>

<?php
$letteraCorrente = '';
?>

<div class="ingredienti-container">

<?php foreach ($ingredienti as $ing): ?>

    <?php
    $lettera = strtoupper(substr($ing['_id'], 0, 1));

    if ($lettera !== $letteraCorrente):

        if ($letteraCorrente !== '') {
            echo '</div>';
        }

        $letteraCorrente = $lettera;
    ?>

        <h3 class="lettera-titolo">
            <?= htmlspecialchars($lettera) ?>
        </h3>

        <div class="card-grid">

    <?php endif; ?>

        <div class="card">

            <div class="card-body">

                <h5 class="card-title">
                    <?= htmlspecialchars($ing['_id']) ?>
                </h5>

                <?php if (!empty($ing['tipologia'])): ?>
                    <p class="ingrediente-info">
                        <?= htmlspecialchars($ing['tipologia']) ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($ing['origine'])): ?>
                    <p class="ingrediente-info">
                        <?= htmlspecialchars($ing['origine']) ?>
                    </p>
                <?php endif; ?>

                <p class="ingrediente-conteggio">
                    Usato in <strong><?= $ing['conteggio'] ?></strong> ricette
                </p>

                <a
                    href="pages/ingredienti/dettaglio.php?ingrediente=<?= urlencode($ing['_id']) ?>"
                    class="card-button"
                >
                    Vedi ingrediente
                </a>

            </div>

        </div>

<?php endforeach; ?>

<?php if ($letteraCorrente !== ''): ?>
    </div>
<?php endif; ?>

</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>