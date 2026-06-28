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
   QUERY BASE PDO
========================= */

$where = [];
$params = [];

/* ricerca titolo */
if ($search !== '') {
    $where[] = "titolo LIKE ?";
    $params[] = "%$search%";
}

/* filtro tipo multiplo */
if (!empty($selectedTipi)) {
    $in = implode(',', array_fill(0, count($selectedTipi), '?'));
    $where[] = "tipo IN ($in)";
    $params = array_merge($params, $selectedTipi);
}

/* filtro regione (JOIN su tabella relazione) */
$joinRegione = '';
if (!empty($selectedRegioni)) {
    $joinRegione = "
        INNER JOIN ricettaRegionale rr ON rr.numeroRicetta = ricette.numero
        INNER JOIN regioni r ON r.cod = rr.cod
    ";

    $in = implode(',', array_fill(0, count($selectedRegioni), '?'));
    $where[] = "r.nome IN ($in)";
    $params = array_merge($params, $selectedRegioni);
}

/* WHERE finale */
$whereSql = '';
if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

/* =========================
   OUTPUT PER TIPO
========================= */

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

            <?php foreach ($tipi as $key => $label): ?>
                <label>
                    <input type="checkbox"
                           name="tipo[]"
                           value="<?= $key ?>"
                           <?= in_array($key, $selectedTipi) ? 'checked' : '' ?>>
                    <?= $label ?>
                </label>
            <?php endforeach; ?>
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
        SELECT DISTINCT ricette.*
        FROM ricette
        $joinRegione
        $whereSql
        " . (!empty($selectedTipi) ? "" : " WHERE ricette.tipo = ? ") . "
        ORDER BY titolo ASC
    ";

    $stmt = $pdo->prepare($sql);

    $finalParams = $params;

    /* se non hai filtro tipo, aggiungo il tipo della sezione */
    if (empty($selectedTipi)) {
        $finalParams[] = $tipo;
    }

    $stmt->execute($finalParams);
    $ricette = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card-grid">

        <?php foreach ($ricette as $r): ?>

            <?php
            $img = "img/default.jpg";

            if (!empty($r['immagine'])) {
                $img = "img/ricette/" . $r['immagine'];
            }
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