<?php
require __DIR__ . '/../../includes/db_connect.php';

$ingrediente = $_GET['ingrediente'] ?? null;
$numeroRicetta = (int) ($_GET['numero'] ?? 0);
if (!$ingrediente || !$numeroRicetta) {
    die("Ingrediente non specificato.");
}
/* =========================
   SE CONFERMATO (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        DELETE FROM Ingredienti
        WHERE ingrediente = ?
        AND numeroRicetta = ?
    ");
    $stmt->execute([$ingrediente, $numeroRicetta]);
    header("Location: ../ricette/dettaglio.php?numero=" . $numeroRicetta);
    exit;
}
/* =========================
   PAGINA DI CONFERMA
========================= */
?>
<h2>Conferma eliminazione</h2>
<p>Sei sicuro di voler eliminare:</p>
<strong><?= htmlspecialchars($ingrediente) ?></strong>
<form method="post" style="margin-top:20px;">
    <button type="submit" class="btn btn-reset">
        Sì, elimina
    </button>
    <a href="../ricette/dettaglio.php?numero=<?= $numeroRicetta ?>"
       class="btn btn-undo">
        Annulla
    </a>

</form>