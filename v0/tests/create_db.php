<?php
require __DIR__ . '/../vendor/autoload.php';

/* per creare il db usare: 
http://localhost/CucinatiMaNonFritti95/v0/tests/create_db.php 
*/
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->CucinatiMaNonFritti95;

/* RICETTE */
$ricetteData = require __DIR__ . '/data/ricette.php';
$db->ricette->insertMany($ricetteData);

/* REGIONI */
$regioniData = require __DIR__ . '/data/regioni.php';
$db->regioni->insertMany($regioniData);

/* INGREDIENTI */
$ingredientiData = require __DIR__ . '/data/ingredienti.php';
$db->ingredienti->insertMany($ingredientiData);

/* RICETTA REGIONALE */
$ricettaRegionaleData = require __DIR__ . '/data/ricettaRegionale.php';
$db->ricettaRegionale->insertMany($ricettaRegionaleData);

/* LIBRI */
$libriData = require __DIR__ . '/data/libri.php';
$db->libri->insertMany($libriData);

/* PAGINE */
$pagineData = require __DIR__ . '/data/pagine.php';
$db->pagine->insertMany($pagineData);

/* RICETTA PUBBLICATA */
$ricettaPubblicataData = require __DIR__ . '/data/ricettaPubblicata.php';
$db->ricettaPubblicata->insertMany($ricettaPubblicataData);

echo "Database popolato con successo!";
