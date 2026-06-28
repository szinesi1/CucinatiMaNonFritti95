<?php
require __DIR__ . '/../db_connect.php';

try {

    /* =========================
       TRANSAZIONE
    ========================= */
    $pdo->beginTransaction();

    /* =========================
       DISABILITA FK TEMPORANEA
    ========================= */
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    /* =========================
       PULIZIA TABELLE
    ========================= */
    $pdo->exec("DELETE FROM RicettaPubblicata");
    $pdo->exec("DELETE FROM RicettaRegionale");
    $pdo->exec("DELETE FROM Ingredienti");
    $pdo->exec("DELETE FROM Pagine");
    $pdo->exec("DELETE FROM Ricette");
    $pdo->exec("DELETE FROM Libri");
    $pdo->exec("DELETE FROM Regioni");

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    /* =========================
       CARICAMENTO DATI
    ========================= */
    $ricette = require __DIR__ . '/data/ricette.php';
    $ingredienti = require __DIR__ . '/data/ingredienti.php';
    $libri = require __DIR__ . '/data/libri.php';
    $pagine = require __DIR__ . '/data/pagine.php';
    $regioni = require __DIR__ . '/data/regioni.php';
    $ricettaRegionale = require __DIR__ . '/data/ricettaRegionale.php';
    $ricettaPubblicata = require __DIR__ . '/data/ricettaPubblicata.php';

    /* =========================
       RICETTE
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO Ricette (numero, titolo, tipo, immagine)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($ricette as $r) {
        $img = $r['immagini'][0] ?? null;
        $stmt->execute([$r['numero'], $r['titolo'], $r['tipo'], $img]);
    }

    /* =========================
       INGREDIENTI
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO Ingredienti (numeroRicetta, numero, ingrediente, quantita)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($ingredienti as $i) {
        $stmt->execute([
            $i['numeroRicetta'],
            $i['numero'],
            $i['ingrediente'],
            $i['quantita']   // ✔ FIX IMPORTANTE
        ]);
    }

    /* =========================
       LIBRI
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO Libri (codISBN, titolo, anno)
        VALUES (?, ?, ?)
    ");

    foreach ($libri as $l) {
        $stmt->execute([$l['codISBN'], $l['titolo'], $l['anno']]);
    }

    /* =========================
       PAGINE
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO Pagine (libro, numeroPagina, numeroRicetta)
        VALUES (?, ?, ?)
    ");

    foreach ($pagine as $p) {
        $stmt->execute([$p['libro'], $p['numeroPagina'], $p['numeroRicetta']]);
    }

    /* =========================
       REGIONI
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO Regioni (cod, nome, zona)
        VALUES (?, ?, ?)
    ");

    foreach ($regioni as $r) {
        $stmt->execute([$r['cod'], $r['nome'], $r['zona']]);
    }

    /* =========================
       RICETTA REGIONALE
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO RicettaRegionale (cod, numeroRicetta)
        VALUES (?, ?)
    ");

    foreach ($ricettaRegionale as $r) {
        $stmt->execute([$r['cod'], $r['numeroRicetta']]);
    }

    /* =========================
       RICETTA PUBBLICATA
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO RicettaPubblicata (numeroRicetta, libro, numeroPagina)
        VALUES (?, ?, ?)
    ");

    foreach ($ricettaPubblicata as $r) {
        $stmt->execute([
            $r['numeroRicetta'],
            $r['libro'],
            $r['numeroPagina']
        ]);
    }

    /* =========================
       COMMIT
    ========================= */
    $pdo->commit();

    echo "Database inizializzato correttamente!";

} catch (Exception $e) {

    $pdo->rollBack();

    echo "Errore inizializzazione: " . $e->getMessage();
}