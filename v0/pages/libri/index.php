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

$showFilters = !empty($titolo) || !empty($anno) || !empty($isbn);

/* =========================
   ORDINAMENTO
========================= */

$sortField = $_GET['sort'] ?? 'titolo';
$sortDir   = $_GET['dir'] ?? 'asc';

$allowedSortFields = ['titolo', 'anno'];
$allowedDir = ['asc', 'desc'];

if (!in_array($sortField, $allowedSortFields, true)) $sortField = 'titolo';
if (!in_array($sortDir, $allowedDir, true)) $sortDir = 'asc';

/* =========================
   QUERY LIBRI
========================= */

$sql = "SELECT * FROM Libri WHERE 1=1";
$params = [];

if ($titolo !== '') {
    $sql .= " AND titolo LIKE ?";
    $params[] = "%$titolo%";
}

if ($anno !== '') {
    $sql .= " AND anno = ?";
    $params[] = $anno;
}

if ($isbn !== '') {
    $sql .= " AND codISBN LIKE ?";
    $params[] = "%$isbn%";
}

$sql .= " ORDER BY $sortField $sortDir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$libri = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   SORT LINK HELPER
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
     FILTRI UI (UGUALE)
========================= -->

<form method="GET" class="filters">

    <div class="filters-top">

        <input type="text"
               name="titolo"
               class="filters-input"
               placeholder="Cerca titolo..."
               value="<?= htmlspecialchars($titolo) ?>">

        <button type="button" id="toggleFilters" class="secondary-button">
            <?= $showFilters ? 'Meno filtri' : 'Più filtri' ?>
        </button>

    </div>

    <div id="advancedFilters"
         class="advanced-filters <?= $showFilters ? 'open' : '' ?>">

        <div class="filter-group">

            <input type="number"
                   name="anno"
                   class="filters-input2"
                   placeholder="Anno"
                   value="<?= htmlspecialchars($anno) ?>">

            <input type="text"
                   name="isbn"
                   class="filters-input2"
                   placeholder="ISBN"
                   value="<?= htmlspecialchars($isbn) ?>">

        </div>

    </div>

    <div class="filters-actions">

        <button type="submit" class="btn">Filtra</button>

        <a href="index.php" class="reset-button">Reset</a>

    </div>

</form>

<h2>Elenco Libri</h2>

<table class="table-libri">

<tr>
    <th>Titolo</th>
    <th>Anno</th>
    <th>Pagine</th>
    <th>Ricette pubblicate</th>
</tr>

<?php foreach ($libri as $libro): ?>

    <?php
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Pagine WHERE libro = ?");
    $stmt->execute([$libro['codISBN']]);
    $numPagine = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM RicettaPubblicata WHERE libro = ?");
    $stmt->execute([$libro['codISBN']]);
    $numRicette = $stmt->fetchColumn();
    ?>

    <tr>
        <td>
            <a href="dettaglio.php?isbn=<?= urlencode($libro['codISBN']) ?>">
                <?= htmlspecialchars($libro['titolo']) ?>
            </a>
        </td>

        <td><?= htmlspecialchars($libro['anno']) ?></td>
        <td><?= $numPagine ?></td>
        <td><?= $numRicette ?></td>
    </tr>

<?php endforeach; ?>

</table>

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