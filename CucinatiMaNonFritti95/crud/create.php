<?php
include '../php/connessione.php';
include '../php/funzioni.php';
include '../php/header.php';
include '../php/navbar.php';
?>

<h2>Aggiungi Ingrediente</h2>

<form method="POST">
    <label>Nome ingrediente:</label>
    <input type="text" name="nome" required>
    <button type="submit">Salva</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];

    $sql = "INSERT INTO Ingrediente (nome) VALUES ('$nome')";
    eseguiQuery($conn, $sql);

    echo "<p>Ingrediente aggiunto con successo!</p>";
    echo "<a href='read.php'>Torna alla lista</a>";
}

include '../php/footer.php';
?>
