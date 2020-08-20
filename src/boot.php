<?php

use AgileCore\Utils\Dbg;

define("SHARE_ROOT", dirname(dirname(__FILE__)));
define("INSTALL_ROOT", dirname(dirname(dirname(__FILE__))));
define("CONFIG_ROOT", INSTALL_ROOT . '/config');

require SHARE_ROOT . "/vendor/autoload.php";

ini_set("log_errors", 1);
ini_set("error_log", Dbg::getFileName());
ini_set("memory_limit","128M");

require SHARE_ROOT . "/src/helpers.php";

$dotenv = Dotenv\Dotenv::createImmutable(INSTALL_ROOT);
$dotenv->load();
