<?php

use AgileCore\Models\Category;
use AgileCore\Models\Product;
use AgileCore\Utils\Str;

if(!defined(SHARE_ROOT)) require_once "../src/boot.php";

$startStamp = (new DateTime())->getTimestamp();

echo "Started products job <br>";

$categories = [
    ["name" => "Disque durs"], // 1
    ["name" => "Écrans", "children" => [
        "Écran 27 pouces", "Écran 24 pouces", "Écran 17 pouces"
    ]], // 2
    ["name" => "Périphériques"], // 3
    ["name" => "Souris", "parent_id" => 3, "children" => [
        "Souris sans fil", "Souris filaire", "Souris verticale"
    ]], // 4
    ["name" => "Claviers", "parent_id" => 4, "children" => [
        "Clavier sans fil", "Clavier filaire", "Clavier mécanique"
    ]], // 5
    ["name" => "SSD", "parent_id" => 1, "children" => [
        "SSD 200 GO", "SSD 500 GO", "SSD 1 TO"
    ]], // 6
    ["name" => "HDD", "parent_id" => 1, "children" => [
        "HDD 200 GO", "HDD 500 GO", "HDD 1 TO"
    ]], // 7
    ["name" => "Ordinateurs"], // 8
    ["name" => "Ordinateurs portables", "parent_id" => 8, "children" => [
        "Ordinateur portable bureautique", "Ordinateur portable jeux"
    ]], // 9
    ["name" => "Ordinateurs fixes", "parent_id" => 8, "children" => [
        "Ordinateur fixe bureautique", "Ordinateur fixe jeux"
    ]]
];

foreach ($categories as $categoryData) {
    $category = new Category();
    $category->name = $categoryData["name"];
    $category->slug = Str::slugify($category->name);
    if(isset($categoryData["parent_id"])) $category->parent_id = $categoryData["parent_id"];
    $category->save();
    if(isset($categoryData["children"])) {
        foreach ($categoryData["children"] as $productName) {
            $product = new Product();
            $product->name = $productName;
            $product->category_id = $category->id;
            $product->stock = mt_rand(1, 100);
            $product->price = rand(1, 100) / 10;
            $product->save();
        }
    }
}

$endStamp = (new DateTime())->getTimestamp();

echo "Job done in " . ($endStamp-$startStamp) . " seconds <br>";
