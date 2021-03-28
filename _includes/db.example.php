<?php

define('CHARSET', '');
define("DB_HOST", '');
define('DB', '');
define('DB_USER', '');
define('DB_PASS', '');

function dbConnect() {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB.";charset=".CHARSET;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $options);
}