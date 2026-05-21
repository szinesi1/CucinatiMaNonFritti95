<?php
// navbar.php

// Si attiva il link della pagina aperta così si capisce in che parte del sito ci si trova:

$current = $_SERVER['REQUEST_URI'];

if (!function_exists('isActive')) {
    function isActive($path) {
        global $current;
        return strpos($current, $path) !== false ? 'active' : '';
    }
}
?>

<nav class="nav-vertical">
    <ul>
        <li><a class="<?= isActive('home') ?>" href="/CucinatiMaNonFritti95/v0/index.php">Home</a></li>
        <li><a class="<?= isActive('/ricette/') ?>" href="/CucinatiMaNonFritti95/v0/entity/ricette/index.php">Ricette</a></li>
        <li><a class="<?= isActive('/ingredienti/') ?>" href="/CucinatiMaNonFritti95/v0/entity/ingredienti/index.php">Ingredienti</a></li>
        <li><a class="<?= isActive('/regioni/') ?>" href="/CucinatiMaNonFritti95/v0/entity/regioni/index.php">Regioni</a></li>
        <li><a class="<?= isActive('/libri/') ?>" href="/CucinatiMaNonFritti95/v0/entity/libri/index.php">Libri</a></li>
    </ul>
</nav>
