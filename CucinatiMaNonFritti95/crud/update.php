<?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';

$id = $_GET['id'];
?>

<h2>Elimina Ingrediente</h2>

<p>Sei sicura di voler eliminare questo ingrediente?</p>

<form method="POST">
    <button type="submit" name="conferma" value="1">Sì, elimina</button>
    <a href="read.php">Annulla</a>
</form>

<?php
if (isset($_POST['conferma'])) {
    $sql = "DELETE FROM Ingrediente WHERE idIngrediente = $id";
    eseguiQuery($conn, $sql);

    echo "<p>Ingrediente eliminato con successo!</p>";
    echo "<a href='read.php'>Torna alla lista</a>";
}

include '../php/footer.php';
?>