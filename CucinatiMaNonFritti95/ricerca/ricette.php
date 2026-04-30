<?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';
?>

<h2>Ricerca Ricette</h2>

<form method="GET">
    <label>Nome ricetta:</label>
    <input type="text" name="nome" value="<?php echo $_GET['nome'] ?? ''; ?>">
    <button type="submit">Cerca</button>
</form>

<?php
$nome = $_GET['nome'] ?? '';

$sql = "SELECT * FROM Ricetta WHERE 1";

if ($nome != '') {
    $sql .= " AND nome LIKE '%$nome%'";
}

$ris = eseguiQuery($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Tempo</th>
        </tr>";

while ($r = $ris->fetch_assoc()) {
    echo "<tr>
            <td>{$r['idRicetta']}</td>
            <td>{$r['nome']}</td>
            <td>{$r['tempoPreparazione']}</td>
          </tr>";
}

echo "</table>";

include '../php/footer.php';
?>
