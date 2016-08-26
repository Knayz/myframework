<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 13:49
 */

ini_set("display_errors", 1);
ini_set("erro_reporting", -1);

//var_dump($_REQUEST);



if(!class_exists('Core')){
    require_once 'core/core.php';
}

$Core = new Core();

$req = !empty($_REQUEST['q'])
        ? trim($_REQUEST['q'])
        : '';

$Core->handleRequest($req);