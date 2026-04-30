<?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';
?>

<h2>Ricerca Pagine</h2>

<form method="GET">
    <label>Numero pagina:</label>
    <input type="number" name="numero" value="<?php echo $_GET['numero'] ?? ''; ?>">
    <button type="submit">Cerca</button>
</form>

<?php
$numero = $_GET['numero'] ?? '';

$sql = "SELECT * FROM Pagina WHERE 1";

if ($numero != '') {
    $sql .= " AND numero = $numero";
}

$ris = eseguiQuery($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Numero</th>
            <th>ID Libro</th>
        </tr>";

while ($r = $ris->fetch_assoc()) {
    echo "<tr>
            <td>{$r['idPagina']}</td>
            <td>{$r['numero']}</td>
            <td>{$r['idLibro']}</td>
          </tr>";
}

echo "</table>";

include '../php/footer.php';
?><?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';
?>

<h2>Ricerca Pagine</h2>

<form method="GET">
    <label>Numero pagina:</label>
    <input type="number" name="numero" value="<?php echo $_GET['numero'] ?? ''; ?>">
    <button type="submit">Cerca</button>
</form>

<?php
$numero = $_GET['numero'] ?? '';

$sql = "SELECT * FROM Pagina WHERE 1";

if ($numero != '') {
    $sql .= " AND numero = $numero";
}

$ris = eseguiQuery($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Numero</th>
            <th>ID Libro</th>
        </tr>";

while ($r = $ris->fetch_assoc()) {
    echo "<tr>
            <td>{$r['idPagina']}</td>
            <td>{$r['numero']}</td>
            <td>{$r['idLibro']}</td>
          </tr>";
}

echo "</table>";

include '../php/footer.php';
?>