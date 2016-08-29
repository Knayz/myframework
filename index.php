<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 13:49
 */


require_once __DIR__ . '/vendor/autoload.php';

$Core = new \Brevis\Core();

$req = !empty($_REQUEST['q'])
        ? trim($_REQUEST['q'])
        : '';
if (!defined('PROJECT_API_MODE') || !PROJECT_API_MODE) {
    $Core->handleRequest($req);
}