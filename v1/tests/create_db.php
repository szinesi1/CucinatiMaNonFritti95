<?php
require __DIR__ . '/../vendor/autoload.php';

/* per creare il db usare: 
http://localhost/CucinatiMaNonFritti95/v0/tests/create_db.php 
 */
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->CucinatiMaNonFritti95;

/* RICETTE */
require __DIR__ . '/data/ricette.php';
$db->ricette->insertMany($ricetteData);

/* REGIONI */
require __DIR__ . '/data/regioni.php';
$db->regioni->insertMany($regioniData);

/* INGREDIENTI */
require __DIR__ . '/data/ingredienti.php';
$db->ingredienti->insertMany($ingredientiData);

/* RICETTA REGIONALE */
require __DIR__ . '/data/ricettaRegionale.php';
$db->ricettaRegionale->insertMany($ricettaRegionaleData);

/* LIBRI */
require __DIR__ . '/data/libri.php';
$db->libri->insertMany($libriData);

/* PAGINE */
require __DIR__ . '/data/pagine.php';
$db->pagine->insertMany($pagineData);

/* RICETTA PUBBLICATA */
require __DIR__ . '/data/ricettaPubblicata.php';
$db->ricettaPubblicata->insertMany($ricettaPubblicataData);

echo "Database popolato con successo!";
