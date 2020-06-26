<?php

use AgileCore\Models\Command;

require "../src/boot.php";

$command = new Command(1);
$command->generateInvoicePDF();
