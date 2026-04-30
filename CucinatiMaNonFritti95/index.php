<?php
include 'php/header.php';
include 'php/navbar.php';
?>

<h2>Benvenuto nel Ricettario</h2>

<p>Usa il menu in alto per navigare tra le ricerche e il CRUD degli ingredienti.</p>

<ul>
    <li><a href="ricerca/ricette.php">Ricerca Ricette</a></li>
    <li><a href="ricerca/regioni.php">Ricerca Regioni</a></li>
    <li><a href="ricerca/libri.php">Ricerca Libri</a></li>
    <li><a href="ricerca/pagine.php">Ricerca Pagine</a></li>
    <li><a href="ricerca/ingredienti.php">Ricerca Ingredienti</a></li>
    <li><a href="crud/read.php">CRUD Ingredienti</a></li>
</ul>

<?php
include 'php/footer.php';
?>