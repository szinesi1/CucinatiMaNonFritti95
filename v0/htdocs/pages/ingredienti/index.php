<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* =========================
   FILTRI
========================= */

$search = trim($_GET['search'] ?? '');
$selectedLetters = $_GET['lettera'] ?? [];

if (!is_array($selectedLetters)) {
    $selectedLetters = [];
}

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

/* LETTERA FILTRO */
if (!empty($selectedLetters)) {
    $in = implode(',', array_fill(0, count($selectedLetters), '?'));
    $sql .= " AND UPPER(LEFT(ingrediente,1)) IN ($in)";
    $params = array_merge($params, $selectedLetters);
}

/* GROUP + ORDER */
$sql .= " GROUP BY ingrediente ORDER BY ingrediente ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ingredienti = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   GRUPPO PER INIZIALE
========================= */

$grouped = [];

foreach ($ingredienti as $ing) {
    $lettera = strtoupper(substr($ing['ingrediente'], 0, 1));
    $grouped[$lettera][] = $ing;
}

ksort($grouped);

/* alfabeto */
$alfabeto = range('A', 'Z');
?>

<!-- =========================
     FILTRI
========================= -->

<form method="GET" class="filters">

    <!-- TOP BAR -->
    <div class="filters-top">

        <input type="text"
               name="search"
               class="filters-input"
               placeholder="Cerca ingrediente..."
               value="<?= htmlspecialchars($search) ?>">

        <button type="button" id="toggleFilters" class="secondary-button">
            Filtri avanzati
        </button>

    </div>

    <!-- FILTRI AVANZATI -->
    <div id="advancedFilters" class="alphabet-filter">
        
         <fieldset>
            <legend>Lettera iniziale</legend>

				<div class="alphabet-bar">
                <?php foreach ($alfabeto as $lettera): ?>
                    <label class="lettera-item">
                        <input type="checkbox"
                               name="lettera[]"
                               value="<?= $lettera ?>"
                               <?= in_array($lettera, $selectedLetters) ? 'checked' : '' ?>>

                        <span><?= $lettera ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

    </div>

    <!-- AZIONI FUORI DAL TOGGLE -->
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

<!-- =========================
     OUTPUT
========================= -->

<h2>Elenco Ingredienti</h2>

<div class="ingredienti-container">

<?php foreach ($grouped as $lettera => $lista): ?>

    <div class="letter-header">
        <?= $lettera ?>
    </div>

    <div class="card-grid">

        <?php foreach ($lista as $ing): ?>

            <div class="card">

                <div class="card-body">

                    <h5 class="card-title">
                        <?= htmlspecialchars($ing['ingrediente']) ?>
                    </h5>

                    <p>
                        Usato in <strong><?= $ing['conteggio'] ?></strong> ricette
                    </p>

                    <a href="pages/ingredienti/dettaglio.php?ingrediente=<?= urlencode($ing['ingrediente']) ?>"
                       class="card-button">
                        Vedi ingrediente
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endforeach; ?>

</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>