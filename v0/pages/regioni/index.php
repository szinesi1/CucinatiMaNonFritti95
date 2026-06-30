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
   QUERY REGIONI (MYSQL)
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
   FUNZIONE COUNT RICETTE
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

<h2>Regioni italiane</h2>

<form method="GET" class="filters">

    <input type="text"
           name="search"
           class="filters-input"
           placeholder="Cerca regione..."
           value="<?= htmlspecialchars($search) ?>">

    <div id="advancedFilters" class="advanced-filters">
        <?php foreach ($zone as $z): ?>
            <label>
                <input type="checkbox"
                       name="zona[]"
                       value="<?= $z ?>"
                       <?= in_array($z, $selectedZone) ? 'checked' : '' ?>>
                <?= $z ?>
            </label>
        <?php endforeach; ?>
    </div>

    <<div class="filters-actions">
        <button type="submit" class="btn">Filtra</button>
        <a href="index.php" class="reset-button">Reset</a>
    </div>

</form>

<div class="card-grid">

<?php foreach ($regioni as $regione): ?>

    <?php
    $numRicette = countRicetteRegione($pdo, $regione['cod']);
    ?>

    <div class="card card-body">
        <h3 class="card-title"><?= htmlspecialchars($regione['nome']) ?></h3>

        <p>Zona: <?= htmlspecialchars($regione['zona']) ?></p>

        <p> <?= $numRicette ?> ricette</p>

        <a href="dettaglio.php?regione=<?= urlencode($regione['cod']) ?>" class="card-button">
            Visualizza
        </a>
    </div>

<?php endforeach; ?>

</div>

<?php include __DIR__ . '/../../interface/footer.php'; ?>