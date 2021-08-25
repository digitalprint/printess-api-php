<?php

use Printess\Api\PrintessApiClient;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/functions.php";

$printess = new  PrintessApiClient();
$printess->setApiKey("TheVerySafeApiKey");

return $printess;
