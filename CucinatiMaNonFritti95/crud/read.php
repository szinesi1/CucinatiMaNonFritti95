<?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';
?>

<h2>Lista Ingredienti</h2>

<a href="create.php">➕ Aggiungi nuovo ingrediente</a>

<?php
$sql = "SELECT * FROM Ingrediente ORDER BY idIngrediente";
$ris = eseguiQuery($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Azioni</th>
        </tr>";

while ($r = $ris->fetch_assoc()) {
    echo "<tr>
            <td>{$r['idIngrediente']}</td>
            <td>{$r['nome']}</td>
            <td>
                <a href='update.php?id={$r['idIngrediente']}'>Modifica</a> |
                <a href='delete.php?id={$r['idIngrediente']}'>Elimina</a>
            </td>
          </tr>";
}

echo "</table>";

include '../php/footer.php';
?>
