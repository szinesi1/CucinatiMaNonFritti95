<?php
// navbar.php

// Si attiva il link della pagina aperta così si capisce in che parte del sito ci si trova:

$current = $_SERVER['REQUEST_URI'];
/**
 * @param string $section
 */
if (!function_exists('isActive')) {
    function isActive($section) {
        $path = $_SERVER['REQUEST_URI'];

        // Caso HOME: sei in /v0/ o /v0/index.php
        if (preg_match('#/v0(/index\.php)?$#', $path)) {
            $currentSection = 'home';
        } 
        // Caso entità: /v0/entity/QUALCOSA/...
        elseif (preg_match('#/v0/pages/([^/]+)/#', $path, $matches)) {
            $currentSection = $matches[1]; // ricette, ingredienti, regioni, libri, ...
        } else {
            $currentSection = null;
        }

        return $currentSection === $section ? 'active' : '';
    }
}
?>

<nav class="nav-vertical">
    <ul>
        <li><a class="<?= isActive('home') ?>" href="index.php">Home</a></li>
        <li><a class="<?= isActive('ricette') ?>" href="pages/ricette/index.php">Ricette</a></li>
        <li><a class="<?= isActive('ingredienti') ?>" href="pages/ingredienti/index.php">Ingredienti</a></li>
        <li><a class="<?= isActive('regioni') ?>" href="pages/regioni/index.php">Regioni</a></li>
        <li><a class="<?= isActive('libri') ?>" href="pages/libri/index.php">Libri</a></li>
    </ul>
</nav>
