<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

/* =========================
   TIPI
========================= */

$tipi = [
    "antipasto" => "Antipasti",
    "primo"     => "Primi",
    "secondo"   => "Secondi",
    "contorno"  => "Contorni",
    "dolce"     => "Dolci"
];

/* =========================
   FILTRI
========================= */

$search = trim($_GET['search'] ?? '');

$selectedTipi = $_GET['tipo'] ?? [];
$selectedRegioni = $_GET['regione'] ?? [];

if (!is_array($selectedTipi)) $selectedTipi = [];
if (!is_array($selectedRegioni)) $selectedRegioni = [];

/* =========================
   BASE CONDITIONS (GLOBALI)
========================= */

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "Ricette.titolo LIKE ?";
    $params[] = "%$search%";
}

/* JOIN regione SOLO se serve */
$joinRegione = '';
if (!empty($selectedRegioni)) {
    $joinRegione = "
        INNER JOIN RicettaRegionale rr ON rr.numeroRicetta = Ricette.numero
        INNER JOIN Regioni r ON r.cod = rr.cod
    ";

    $in = implode(',', array_fill(0, count($selectedRegioni), '?'));
    $where[] = "r.nome IN ($in)";
    $params = array_merge($params, $selectedRegioni);
}

?>

<!-- FILTRI UI -->
<form method="GET" class="filters">

    <div class="filters-top">
        <input type="text"
               name="search"
               class="filters-input"
               placeholder="Cerca ricetta..."
               value="<?= htmlspecialchars($search) ?>">

        <button type="button" id="toggleFilters" class="secondary-button">
            Filtri
        </button>
    </div>

    <div id="advancedFilters" class="advanced-filters">
        <fieldset>
            <legend>Tipologia</legend>
			<div class="alphabet-bar">
                <?php foreach ($tipi as $key => $label): ?>
                    <label class="check-ui">
                        <input type="checkbox"
                               name="tipo[]"
                               class="check-box"
                               value="<?= $key ?>"
                            <?= in_array($key, $selectedTipi) ? 'checked' : '' ?>>
                        <?= $label ?>
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

<h2>Ricette per tipologia</h2>

<?php foreach ($tipi as $tipo => $titolo): ?>

    <h3><?= htmlspecialchars($titolo) ?></h3>

    <?php
    $sql = "
        SELECT DISTINCT Ricette.*
        FROM Ricette
        $joinRegione
    ";

    $conditions = $where;
    $localParams = $params;

    /* 👉 SEMPRE filtriamo per la categoria del loop */
    $conditions[] = "Ricette.tipo = ?";
    $localParams[] = $tipo;

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY Ricette.titolo ASC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($localParams);
        $ricette = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<pre>ERRORE SQL: " . $e->getMessage() . "</pre>";
        continue;
    }
    ?>

    <div class="card-grid">

        <?php if (empty($ricette)): ?>
            <p>Nessuna ricetta.</p>
        <?php endif; ?>

        <?php foreach ($ricette as $r): ?>

            <?php
            $img = !empty($r['immagine'])
                ? "img/ricette/" . $r['immagine']
                : "img/default.jpg";
            ?>

            <div class="card">
                <img src="<?= htmlspecialchars($img) ?>"
                     alt="<?= htmlspecialchars($r['titolo']) ?>">

                <div class="card-body">
                    <h5 class="card-title">
                        <?= htmlspecialchars($r['titolo']) ?>
                    </h5>

                    <a href="pages/ricette/dettaglio.php?numero=<?= $r['numero'] ?>"
                       class="card-button">
                        Vedi ricetta
                    </a>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

<?php endforeach; ?>

<?php include __DIR__ . '/../../interface/footer.php'; ?>