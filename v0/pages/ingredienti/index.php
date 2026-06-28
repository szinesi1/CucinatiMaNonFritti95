<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* =========================
   FILTRI
========================= */

$search = trim($_GET['search'] ?? '');

$selectedTipi = $_GET['tipo'] ?? [];
$selectedOrigini = $_GET['origine'] ?? [];

if (!is_array($selectedTipi)) $selectedTipi = [];
if (!is_array($selectedOrigini)) $selectedOrigini = [];

/* dati statici UI (non DB) */
$tipi = [
    "Verdura","Frutta","Carne","Pesce","Latticini",
    "Cereali","Legumi","Spezie","Erbe aromatiche","Condimenti"
];

$origini = ["Vegetale","Animale","Minerale"];

/* =========================
   QUERY BASE
========================= */

$sql = "
    SELECT 
        ingrediente,
        COUNT(*) AS conteggio
    FROM Ingredienti
    WHERE 1=1
";

$params = [];

/* SEARCH */
if ($search !== '') {
    $sql .= " AND ingrediente LIKE ?";
    $params[] = "%$search%";
}

/* GROUP */
$sql .= " GROUP BY ingrediente";

/* ORDER */
$sql .= " ORDER BY ingrediente ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ingredienti = $stmt->fetchAll(PDO::FETCH_ASSOC);

$showFilters =
    !empty($selectedTipi) ||
    !empty($selectedOrigini);
?>

<!-- =========================
     FILTRI UI (INVARIATI)
========================= -->

<form method="GET" class="filters">

    <div class="filters-top">

        <input
            type="text"
            name="search"
            class="filters-input"
            placeholder="Cerca ingrediente..."
            value="<?= htmlspecialchars($search) ?>">

        <button type="button" id="toggleFilters" class="secondary-button">
            <?= $showFilters ? 'Meno filtri' : 'Più filtri' ?>
        </button>

    </div>

    <div id="advancedFilters"
         class="advanced-filters <?= $showFilters ? 'open' : '' ?>">

        <fieldset>
            <legend>Origine</legend>

            <div class="filter-group origine-group">
                <?php foreach ($origini as $origine): ?>
                    <label>
                        <input type="checkbox"
                               name="origine[]"
                               value="<?= htmlspecialchars($origine) ?>"
                               <?= in_array($origine, $selectedOrigini) ? 'checked' : '' ?>>
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
                        <input type="checkbox"
                               name="tipo[]"
                               value="<?= htmlspecialchars($tipo) ?>"
                               <?= in_array($tipo, $selectedTipi) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($tipo) ?>
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
<div class="ingredienti-container">

<?php foreach ($ingredienti as $ing): ?>
    <?php $lettera = strtoupper(substr($ing['ingrediente'], 0, 1)); ?>
    <div class="card">
        <div class="card-body">

            <h5 class="card-title">
                <?= htmlspecialchars($ing['ingrediente']) ?>
            </h5>

            <p class="ingrediente-conteggio">
                Usato in <strong><?= $ing['conteggio'] ?></strong> ricette
            </p>

            <a href="dettaglio.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>"
               class="card-button">
                Vedi ingrediente
            </a>
        </div>
    </div>
<?php endforeach; ?>

</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>