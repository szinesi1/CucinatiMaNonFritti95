<?php
require_once "config/db.php";
include "includes/header.php";
include "includes/nav.php";
?>

<div class="layout">

    <aside class="sidebar">
        <h3>Navigazione</h3>
        <p>Usa il menu superiore per consultare ricette, ingredienti, libri e regioni.</p>
    </aside>

    <main class="content">
        <h2>Benvenuto nel Ricettario Verde</h2>

        <p>
            Questa applicazione permette di consultare un archivio digitale di ricette,
            ingredienti, libri di cucina e regioni di provenienza.
        </p>

        <h3>Funzionalità previste</h3>

        <ul>
            <li>Ricerca delle ricette per titolo e categoria</li>
            <li>Visualizzazione degli ingredienti collegati a ogni ricetta</li>
            <li>Visualizzazione dei libri con numero di pagine e ricette pubblicate</li>
            <li>CRUD completo sulla tabella Ingrediente</li>
        </ul>
    </main>

    <aside class="filter">
        <h3>Dettagli progetto</h3>

        <p><strong>Codice progetto:</strong> 95</p>
        <p><strong>Database:</strong> Ex 4 - Ricettario</p>
        <p><strong>CRUD:</strong> Ingrediente</p>
        <p><strong>Interfaccia:</strong> Interfaccia 3</p>
        <p><strong>Palette:</strong> Verde</p>
    </aside>

</div>

<?php include "includes/footer.php"; ?>