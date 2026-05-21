<?php
require __DIR__ . '/../../includes/db_connect.php';
include __DIR__ . '/../../interface/header.php';

// Tipologie in ordine desiderato + titolo plurale
$tipi = [
    "antipasto" => "Antipasti",
    "primo"     => "Primi",
    "secondo"   => "Secondi",
    "contorno"  => "Contorni",
    "dolce"     => "Dolci"
];
?>

<h1>Ricette per tipologia</h1>

<?php foreach ($tipi as $tipo => $titolo): ?>

    <h2 class="mt-4"><?= $titolo ?></h2>

    <?php
    // Recupero ricette di questo tipo
    $ricette = $db->ricette->find(
        ["tipo" => $tipo],
        ["sort" => ["titolo" => 1]]
    );
    ?>

    <div class="card-grid">

        <?php foreach ($ricette as $r): ?>

            <?php
            // Se esiste almeno una immagine, usa la prima come copertina
            if (isset($r['immagini']) && is_array($r['immagini']) && count($r['immagini']) > 0) {
                $img = "/img/ricette/" . $r['immagini'][0];
            } else {
                // fallback
                $img = "/img/default.jpg";
            }
            ?>

            <div class="card">

                <img 
                    src="<?= $img ?>" 
                    alt="<?= $img === '/img/default.jpg' ? 'immagine non disponibile' : $r['titolo'] ?>"
                >

                <div class="card-body">
                    <h5 class="card-title"><?= $r['titolo'] ?></h5>

                    <a href="dettaglio.php?numero=<?= $r['numero'] ?>" class="card-button">
                        Vedi ricetta
                    </a>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endforeach; ?>

<?php include __DIR__ . '/../../interface/footer.php'; ?>
