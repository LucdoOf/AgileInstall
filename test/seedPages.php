<?php

use AgileCore\Models\Page;
use AgileCore\Models\User;
use AgileCore\Models\Visit;

if(!defined(SHARE_ROOT)) require_once "../src/boot.php";

$startStamp = (new DateTime())->getTimestamp();

echo "Started pages job <br>";

$data = [
    ["slug" => "home", "name" => "Accueil", "static_title" => "Bienvenue sur Agile-Web.net", "static_description" => "Agile-Web.net est une agence de développement basée en Touraine"],
    ["slug" => "product", "name" => "Page produit", "static" => false],
    ["slug" => "contact", "name" => "Contact", "static_title" => "Contactez-nous", "static_description" => "Contactez notre équipe"]
];

foreach ($data as $datum) {
    $page = new Page($datum);
    $page->save();
}

$users = User::getAll();
$pages = Page::getAll();

for($i = 0; $i < 200; $i++) {
    $visit = new Visit();
    $visit->user_id = mt_rand(0, 2) == 1 ? null : ($users[mt_rand(0, count($users)-1)]->id);
    $visit->ip = getIpAddress();
    $visit->page_id = $pages[mt_rand(0, count($pages)-1)]->id;
    $visit->visit_date = new DateTime();
    $visit->visit_date->setTimestamp(mt_rand((new DateTime())->getTimestamp()-(3600*24*14), (new DateTime())->getTimestamp()));
    $visit->visit_duration = mt_rand(10, 10000);
    $visit->save();
}

$endStamp = (new DateTime())->getTimestamp();

echo "Job done in " . ($endStamp-$startStamp) . " seconds <br>";
