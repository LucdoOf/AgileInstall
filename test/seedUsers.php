<?php

use AgileCore\Models\Address;
use AgileCore\Models\Administrator;
use AgileCore\Models\User;

if(!defined(SHARE_ROOT)) require_once "../src/boot.php";

$startStamp = (new DateTime())->getTimestamp();

echo "Started users job <br>";

//https://www.fantasynamegenerators.com/french_names.php
//https://www.coolgenerator.com/fr-address-generator
$names = [
    "Sylviane Rousselle" => ["16 rue La Boétie"],
    "Léone Montgomery" => ["35 rue Jean-Monnet"],
    "Adeline Jacquemoud" => ["90 rue Pierre Motte"],
    "Emmanuelle Chappelle" => ["4 rue de Raymond Poincaré"],
    "Géraldine Larousse" => ["12 rue Léon Dierx"],
    "Christiane Ardouin" => ["62 rue du Clair Bocage"],
    "Agathe Regnard" => ["84 rue Michel Ange"],
    "Thècle Mesny" => ["55 avenue de Provence"],
    "Solenne LeBeau" => ["3 rue du Château"],
    "Ingrid Brousseau" => ["95 boulevard Albin Durand"],
    "Régis Emmanuelli" => ["31 rue de la Mare aux Carats"],
    "Remi Corriveau" => ["64 rue du Gue Jacquet"],
    "Gabriel Geffroy" => ["7 avenue de l'Amandier"],
    "Dimitri Maitre" => ["78 rue Descartes"],
    "Florentin Beaulne" => ["40 Square de la Couronne"],
    "Isaac Jullien" => ["24 avenue Jean Portalis"],
    "Tobie Cazenave" => ["16 Rue Bonnet"],
    "Pierre-Antoine Nee" => ["67 rue des Chaligny"],
    "Armand Boulet" => ["64 Rue de la Pompe"],
    "Josué Fouché" => ["45 Avenue des Tuileries"],
    "Félix Droz" => ["94 place Maurice-Charretier"],
    "Francis Houdin" => ["50 rue Isambard"],
    "Franck Héroux" => ["10 Place du Jeu de Paume"],
    "Marian Loupe" => ["74 route de Lyon"],
    "Pascal Duret" => ["9 rue des Coudriers"],
    "Jacques Tourneur" => ["36 rue du Général Ailleret"],
    "Thaddée Leclair" => ["52 quai Saint-Nicolas"],
    "Marc-Antoine Mossé" => ["59 rue Léon Dierx"],
    "Jean-Michel Cazal" => ["9 rue des Coudriers"],
    "Maxence Laurent" => ["55 quai Saint-Nicolas"],
    "Lorraine Gérard" => ["46 rue Reine Elisabeth"],
    "Aurélia Aliker" => ["28 boulevard Amiral Courbet"],
    "Viviane Blanchet" => ["5 Place de la Gare"],
    "Audrey Duhamel" => ["31 rue du Président Roosevelt"],
    "Séverine Édouard" => ["99 rue Sadi Carnot"],
    "Aurélie Valluy" => ["30 Place Charles de Gaulle"],
    "Françoise Jauffret" => ["34 rue des Nations Unies"],
    "Pascale Grandjean" => ["99 rue Sadi Carnot"],
    "Nicoline Mesny" => ["3 Chemin des Bateliers"],
    "Marguerite Pleimelding" => ["83 rue Sébastopol"],
    "Paulin Fouché" => ["89 rue des lieutemants Thomazo"],
    "Gaby Quint" => ["34 Rue du Limas"],
    "Arthur Baillairgé" => ["44 rue de Raymond Poincaré"],
    "Florentin Gounelle" => ["34 Rue du Limas"],
    "Roméo Laurens" => ["52 Chemin Du Lavarin Sud"],
    "Henri Beaubois" => ["77 route de Lyon"],
    "Maurice Boudon" => ["42 avenue Ferdinand de Lesseps"],
    "William Brochard" => ["14 avenue Ferdinand de Lesseps"],
    "Martin Gounelle" => ["2 boulevard Amiral Courbet"],
    "Armel Bourcier" => ["38 rue de Groussay"],
    "Edmée Couvreur" => ["42 rue des Chaligny"],
    "Hélène Rapace" => ["69 rue de Penthièvre"],
    "Louise Vaganay" => ["89 avenue de l'Amandier"],
    "Ameline Barbier" => ["54 rue des six frères Ruellan"],
    "Éliane Neri" => ["98 avenue de l'Amandier"],
    "Hélène Vallotton" => ["55 cours Franklin Roosevelt"],
    "Joséphine Bombelles" => ["65 rue Marie de Médicis"],
    "Huguette Couvreur" => ["37 rue des Soeurs"],
    "Coline Colbert" => ["68 Chemin Des Bateliers"],
    "Stéphanie Gicquel" => ["89 rue des Nations Unies"],
    "Charlène Garreau" => ["18 boulevard Aristide Briand"],
    "Lucille Ardouin" => ["74 rue Reine Elisabeth"],
    "Marie-Hélène Thévenet" => ["52 place Maurice-Charretier"],
    "Agathe Clérisseau" => ["69 Rue Roussy"],
    "Berthe Leloup" => ["49 rue Isambard"],
    "Abélia Millet" => ["26 Rue de Verdun"],
    "Alberte Barrande" => ["4 boulevard d'Alsace"],
    "Josée Bonhomme" => ["36 rue du Général Ailleret"],
    "Océane Allard" => ["96 Cours Marechal-Joffre"],
    "Agnès Brochard" => ["8 boulevard d'Alsace"]
];

$mailDomains = [
    "yahoo.fr",
    "gmail.com",
    "hotmail.fr",
    "outlook.com",
    "orange.fr"
];

$cities = [
    "Paris" => 75000,
    "Tours" => 37000,
    "Toulouse" => 31000,
    "Bourges" => 18000,
    "Marseille" => 13000,
    "Brest" => 29200,
    "Nantes" => 44000,
    "Bordeaux" => 33000,
    "Augy-sur-aubois" => 18600,
    "Grenoble" => 38000,
    "Plaimpied" => 18340,
    "La ville aux dames" => 37700,
    "Châteauroux" => 36000
];

for ($i = 0; $i < count($names); $i++){
    $fullName = array_keys($names)[$i];
    $firstname = explode(" ", $fullName)[0];
    $lastname = explode(" ", $fullName)[1];
    $domain = $mailDomains[mt_rand(0, count($mailDomains)-1)];
    $user = new User();
    $user->mail = strtolower($firstname) . "." . strtolower($lastname) . "@" . $domain;
    $user->firstname = $firstname;
    $user->lastname = $lastname;
    $user->inscription_date = new DateTime();
    $user->inscription_date->setTimestamp(mt_rand((new DateTime())->getTimestamp()-(3600*24*14), (new DateTime())->getTimestamp()));
    $user->last_seen = new DateTime();
    $user->last_seen->setTimestamp(mt_rand($user->inscription_date->getTimestamp(), (new DateTime())->getTimestamp()));
    $user->save();

    $addressLine = array_values($names)[$i][0];
    $city = array_keys($cities)[mt_rand(0,count($cities)-1)];
    $zipcode = $cities[$city];

    $address = new Address();
    $address->firstname = $user->firstname;
    $address->lastname = $user->lastname;
    $address->user_id = $user->id;
    $address->address = $addressLine;
    $address->city = $city;
    $address->zipcode = $zipcode;
    $address->country = "FR";
    $address->save();
}

$administrator = new Administrator();
$administrator->name = "root";
$administrator->password = "Brisbane";
$administrator->save();

$endStamp = (new DateTime())->getTimestamp();

echo "Job done in " . ($endStamp-$startStamp) . " seconds <br>";
