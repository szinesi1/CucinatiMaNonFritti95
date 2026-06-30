<?php
// navbar.php
$current = $_SERVER['REQUEST_URI'];

if (!function_exists('isActive')) {

    function isActive($section)
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Se siamo in /pages/...
        if (preg_match('#/pages/([^/]+)/#', $path, $matches)) {
            $currentSection = $matches[1];
        }
        // Altrimenti è la home
        else {
            $currentSection = 'home';
        }

        return ($currentSection === $section) ? 'active' : '';
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
