<?php
function eseguiQuery($conn, $sql) {
    $ris = $conn->query($sql);
    if (!$ris) {
        die("Errore nella query: " . $conn->error);
    }
    return $ris;
}
?>