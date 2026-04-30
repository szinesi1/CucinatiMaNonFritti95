<?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';
?>

<h2>Ricerca Libri</h2>

<form method="GET">
    <label>Titolo libro:</label>
    <input type="text" name="titolo" value="<?php echo $_GET['titolo'] ?? ''; ?>">
    <button type="submit">Cerca</button>
</form>

<?php
$titolo = $_GET['titolo'] ?? '';

$sql = "SELECT * FROM Libro WHERE 1";

if ($titolo != '') {
    $sql .= " AND titolo LIKE '%$titolo%'";
}

$ris = eseguiQuery($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Titolo</th>
            <th>Anno</th>
        </tr>";

while ($r = $ris->fetch_assoc()) {
    echo "<tr>
            <td>{$r['idLibro']}</td>
            <td>{$r['titolo']}</td>
            <td>{$r['annoPubblicazione']}</td>
          </tr>";
}

echo "</table>";

include '../php/footer.php';
?>
