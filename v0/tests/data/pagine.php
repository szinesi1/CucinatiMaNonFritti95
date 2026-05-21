<?php
require __DIR__ . '/libri.php';

$pagineData = [];
foreach ($libriData as $libro) {
    $n = rand(30, 60);
    for ($i = 1; $i <= $n; $i++) {
        $pagineData[] = [
            "libro" => $libro["codISBN"],
            "numeroPagina" => $i
        ];
    }
}
