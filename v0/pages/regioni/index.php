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

/* zone */
$zone = ["Nord", "Centro", "Sud", "Isole"];

/* =========================
   QUERY REGIONI
========================= */

$sql = "SELECT * FROM Regioni WHERE 1=1";
$params = [];

/* filtro nome */
if ($search !== '') {
    $sql .= " AND nome LIKE ?";
    $params[] = "%$search%";
}

/* filtro zona */
if (!empty($selectedZone)) {
    $in = implode(',', array_fill(0, count($selectedZone), '?'));
    $sql .= " AND zona IN ($in)";
    $params = array_merge($params, $selectedZone);
}

$sql .= " ORDER BY nome ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$regioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   COUNT RICETTE
========================= */

function countRicetteRegione($pdo, $codRegione) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM RicettaRegionale
        WHERE cod = ?
    ");
    $stmt->execute([$codRegione]);
    return (int)$stmt->fetchColumn();
}
?>

<!-- =========================
     FILTRI (STILE UGUALE RICETTE)
========================= -->

<form method="GET" class="filters">

    <div class="filters-top">

        <input type="text"
               name="search"
               class="filters-input"
               placeholder="Cerca regione..."
               value="<?= htmlspecialchars($search) ?>">

        <button type="button" id="toggleFilters" class="secondary-button">
            Filtri
        </button>

    </div>

    <div id="advancedFilters" class="advanced-filters">

        <fieldset>
            <legend>Zona geografica</legend>
            <div class="alphabet-bar">
            <?php foreach ($zone as $z): ?>
                <label class="check-ui">
                    <input type="checkbox"
                           name="zona[]"
                           class="check-box"
                           value="<?= htmlspecialchars($z) ?>"
                           <?= in_array($z, $selectedZone) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($z) ?>
                </label>
            <?php endforeach; ?>
            </div>
        </fieldset>

    </div>

    <div class="filters-actions">
        <button type="submit" class="btn">Filtra</button>
        <a href="index.php" class="reset-button">Reset</a>
    </div>

</form>

<script>
document.getElementById('toggleFilters').addEventListener('click', function () {
    document.getElementById('advancedFilters').classList.toggle('open');
});
</script>

<h2>Regioni italiane</h2>

<!-- =========================
     GRID CARD (STILE RICETTE)
========================= -->

<div class="card-grid">

	<?php foreach ($regioni as $regione): ?>
    <?php $numRicette = countRicetteRegione($pdo, $regione['cod']); ?>

    <div class="card">

        <div class="card-body">

            <h5 class="card-title">
                <?= htmlspecialchars($regione['nome']) ?>
            </h5>

            <p class="card-text">
                Zona: <?= htmlspecialchars($regione['zona']) ?>
            </p>

            <p class="card-text">
                <?= $numRicette ?> ricette
            </p>

            <a href="pages/regioni/dettaglio.php?regione=<?= urlencode($regione['cod']) ?>"
               class="card-button">
                Visualizza
            </a>

        </div>

    </div>

<?php endforeach; ?>

</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>