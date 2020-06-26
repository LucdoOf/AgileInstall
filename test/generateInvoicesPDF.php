<?php

use AgileCore\Models\Command;

require "../src/boot.php";

$startStamp = (new DateTime())->getTimestamp();

echo "Started job <br>";

foreach (Command::getAll() as $command) {
    $command->generateInvoicePDF();
    $command->save();
}

$endStamp = (new DateTime())->getTimestamp();

echo "Job done in " . ($endStamp-$startStamp) . " seconds";
