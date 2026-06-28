<?php

$libriData = require __DIR__ . '/libri.php';
$ricetteData = require __DIR__ . '/ricette.php';

$ricettaPubblicataData = [];

foreach ($ricetteData as $r) {

    $libriRandom = array_rand($libriData, rand(1, 3));

    if (!is_array($libriRandom)) {
        $libriRandom = [$libriRandom];
    }

    foreach ($libriRandom as $idx) {
        $ricettaPubblicataData[] = [
            "numeroRicetta" => $r["numero"],
            "libro" => $libriData[$idx]["codISBN"],
            "numeroPagina" => rand(3, 55)
        ];
    }
}

return $ricettaPubblicataData;