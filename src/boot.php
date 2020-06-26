<?php

use AgileCore\Utils\Dbg;

define("ROOT", dirname(dirname(__FILE__)));

require ROOT . "/vendor/autoload.php";

ini_set("log_errors", 1);
ini_set("error_log", Dbg::getFileName());
ini_set("memory_limit","128M");

require ROOT . "/src/helpers.php";

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();
