<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

$self = strtok($_SERVER['REQUEST_URI'], '?');
/* =========================
   FILTRI
========================= */

$titolo = trim($_GET['titolo'] ?? '');
$anno   = $_GET['anno'] ?? '';
$isbn   = trim($_GET['isbn'] ?? '');

/* serve per UI toggle coerente */
$showFilters = !empty($titolo) || !empty($anno) || !empty($isbn);

/* =========================
   ORDINAMENTO (WHITELIST)
========================= */

$sortField = $_GET['sort'] ?? 'titolo';
$sortDir   = $_GET['dir'] ?? 'asc';

$allowedSortFields = ['titolo', 'anno'];
$allowedDir = ['asc', 'desc'];

if (!in_array($sortField, $allowedSortFields, true)) {
    $sortField = 'titolo';
}

if (!in_array($sortDir, $allowedDir, true)) {
    $sortDir = 'asc';
}

$sortMongo = [
    $sortField => ($sortDir === 'asc' ? 1 : -1)
];

/* =========================
   QUERY MONGODB
========================= */

$filter = [];

if ($titolo !== '') {
    $filter['titolo'] = [
        '$regex' => $titolo,
        '$options' => 'i'
    ];
}

if ($anno !== '') {
    $filter['anno'] = (int)$anno;
}

if ($isbn !== '') {
    $filter['isbn'] = [
        '$regex' => $isbn,
        '$options' => 'i'
    ];
}

$libri = $db->libri->find($filter, [
    'sort' => $sortMongo
]);

/* =========================
   SORT LINK
========================= */

function sortLink($field, $currentField, $currentDir) {
    $dir = 'asc';

    if ($currentField === $field && $currentDir === 'asc') {
        $dir = 'desc';
    }

    return "?sort={$field}&dir={$dir}";
}
?>

<!-- =========================
     FILTRI
========================= -->

<form method="GET" class="filters">
    <div class="filters-top">
        <input
            type="text"
            name="titolo"
            class="filters-input"
            placeholder="Cerca titolo..."
            value="<?= htmlspecialchars($titolo) ?>">
        <button type="button" id="toggleFilters" class="secondary-button">
            <?= $showFilters ? 'Meno filtri' : 'Più filtri' ?>
        </button>
    </div>
    <div id="advancedFilters" class="advanced-filters <?= $showFilters ? 'open' : '' ?>">
        <div class="filter-group">
            <input
                type="number"
                name="anno"
                class="filters-input2"
                placeholder="Anno"
                value="<?= htmlspecialchars($anno) ?>">
            <input
                type="text"
                name="isbn"
                class="filters-input2"
                placeholder="ISBN"
                value="<?= htmlspecialchars($isbn) ?>">
        </div>
    </div>
    <div class="filters-actions">
        <button type="submit" class="btn">
            Filtra
        </button>
        <a href="/CucinatiMaNonFritti95/v0/pages/libri/index.php" class="reset-button">
            Reset
        </a>
    </div>
</form>

<!-- =========================
     RISULTATI
========================= -->

<h2>Elenco Libri</h2>

<table class="table-libri">
    <tr>
        <th>
            Titolo
            <span class="sort-icons">
                <a href="<?= $self . '?' . http_build_query(array_merge($_GET, [
                    'sort' => 'titolo',
                    'dir' => 'asc'
                ])) ?>"
                class="sort-btn <?= ($sortField==='titolo' && $sortDir==='asc') ? 'active' : '' ?>">
                    ▲
                </a>

                <a href="<?= $self . '?' . http_build_query(array_merge($_GET, [
                    'sort' => 'titolo',
                    'dir' => 'desc'
                ])) ?>"
                class="sort-btn <?= ($sortField==='titolo' && $sortDir==='desc') ? 'active' : '' ?>">
                    ▼
                </a>
            </span>
        </th>
        <th>
            Anno
            <span class="sort-icons">
                <a href="<?= $self . '?' . http_build_query(array_merge($_GET, [
                    'sort' => 'anno',
                    'dir' => 'asc'
                ])) ?>"
                class="sort-btn <?= ($sortField==='anno' && $sortDir==='asc') ? 'active' : '' ?>">
                    ▲
                </a>
                <a href="<?= $self . '?' . http_build_query(array_merge($_GET, [
                    'sort' => 'anno',
                    'dir' => 'desc'
                ])) ?>"
                class="sort-btn <?= ($sortField==='anno' && $sortDir==='desc') ? 'active' : '' ?>">
                    ▼
                </a>
            </span>
        </th>
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

<!-- =========================
     JS TOGGLE FILTRI
========================= -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('toggleFilters');
    const panel = document.getElementById('advancedFilters');

    btn.addEventListener('click', function () {
        panel.classList.toggle('open');
    });

});
</script>

<?php include __DIR__ . '/../../interface/footer.php'; ?>