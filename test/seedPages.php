<?php

use AgileCore\Models\Page;

require "../src/boot.php";

$startStamp = (new DateTime())->getTimestamp();

echo "Started job <br>";

$data = [
    ["slug" => "home", "name" => "Accueil", "static_title" => "Bienvenue sur Agile-Web.net", "static_description" => "Agile-Web.net est une agence de développement basée en Touraine"],
    ["slug" => "product", "name" => "Page produit", "static" => false],
    ["slug" => "contact", "name" => "Contact", "static_title" => "Contactez-nous", "static_description" => "Contactez notre équipe"]
];

foreach ($data as $datum) {
    $page = new Page($datum);
    $page->save();
}

$endStamp = (new DateTime())->getTimestamp();

echo "Job done in " . ($endStamp-$startStamp) . " seconds";
