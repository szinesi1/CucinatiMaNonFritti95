<?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';
?>

<h2>Ricerca Ingredienti</h2>

<form method="GET">
    <label>Nome ingrediente:</label>
    <input type="text" name="nome" value="<?php echo $_GET['nome'] ?? ''; ?>">
    <button type="submit">Cerca</button>
</form>

<?php
$nome = $_GET['nome'] ?? '';

$sql = "SELECT * FROM Ingrediente WHERE 1";

if ($nome != '') {
    $sql .= " AND nome LIKE '%$nome%'";
}

$ris = eseguiQuery($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Nome</th>
        </tr>";

while ($r = $ris->fetch_assoc()) {
    echo "<tr>
            <td>{$r['idIngrediente']}</td>
            <td>{$r['nome']}</td>
          </tr>";
}

echo "</table>";

include '../php/footer.php';
?>
