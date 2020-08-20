<?php

use AgileCore\Core\Communication\Mailer;
use AgileCore\Models\Basket;
use AgileCore\Models\BasketEntry;
use AgileCore\Models\Command;
use AgileCore\Models\Product;
use AgileCore\Models\Transaction;
use AgileCore\Models\User;
use AgileInstall\Config;

if(!defined(SHARE_ROOT)) require_once "../src/boot.php";

$startStamp = (new DateTime())->getTimestamp();

echo "Started commands job <br>";

$products = Product::getAll();
$users = User::getAll();

for($i = 0; $i < 100; $i++){
    $user = $users[mt_rand(0,count($users)-1)];
    $command = new Command();
    $basket = new Basket();
    $basket->user_id = $user->id;
    $basket->ip = getIpAddress();
    $basket->save();
    for($j = 0; $j < mt_rand(1, 6); $j++){
        $basketEntry = new BasketEntry();
        $basketEntry->basket_id = $basket->id;
        $product = $products[mt_rand(0,count($products)-1)];
        $basketEntry->product_id = $product->id;
        $basketEntry->quantity = mt_rand(1, 20);
        $basketEntry->entry_price = $basketEntry->quantity * $product->price;
        $basketEntry->save();
    }
    $command->basket_id = $basket->id;
    $command->order_date = new DateTime();
    $command->order_date->setTimestamp(mt_rand((new DateTime())->getTimestamp()-(3600*24*14), (new DateTime())->getTimestamp()));
    $command->status = Command::STATUS[mt_rand(0,count(Command::STATUS)-1)];
    $command->billing_address_id = $user->getLinkedAddresses()[0]->id;
    $command->shipping_address_id = $user->getLinkedAddresses()[0]->id;
    $command->transporter_id = (Config::AVAILABLE_TRANSPORTERS[mt_rand(0, count(Config::AVAILABLE_TRANSPORTERS)-1)])::getIdentifier();
    $command->save();
    Mailer::orderSuccessMail($command);
    Transaction::createFromCommand($command, null);
}

$endStamp = (new DateTime())->getTimestamp();

echo "Job done in " . ($endStamp-$startStamp) . " seconds <br>";
