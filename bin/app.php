#!/usr/local/bin/php
<?php
declare(strict_types=1);

require_once(__DIR__ . '/../vendor/autoload.php');

use Vending\Ui\AppCli;

$app = new AppCli(STDIN, STDOUT, $argv);
$app->run();
