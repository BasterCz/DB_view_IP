<?php
mb_internal_encoding("UTF-8");
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __Dir__ .  "/../vendor/autoload.php";

spl_autoload_register(function ($className){
    include __Dir__ . '/' . $className .'.class.php';
});