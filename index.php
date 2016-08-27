<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 13:49
 */

ini_set("display_errors", 1);
ini_set("erro_reporting", -1);

require_once __DIR__ . '/vendor/autoload.php';

$Core = new \Brevis\Core();

$req = !empty($_REQUEST['q'])
        ? trim($_REQUEST['q'])
        : '';

$Core->handleRequest($req);