<?php
/* per testare il db usare: 
http://localhost/CucinatiMaNonFritti95/v0/tests/test_mongo.php 
*/
require __DIR__ . '/../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->CucinatiMaNonFritti95;

$collection = $db->test;

$result = $collection->insertOne([
    "messaggio" => "Ciao Sara!",
    "ora" => date("H:i:s")
]);

echo "Inserito ID: " . $result->getInsertedId();