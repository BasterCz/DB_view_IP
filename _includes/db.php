<?php

define('CHARSET', 'utf8mb4');
define("DB_HOST", '127.0.0.1');
define('DB', 'c149a2018bartbo');
define('DB_USER', 'c149a2018bartbo');
define('DB_PASS', 'Usi8inF#W');

function dbConnect() {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB.";charset=".CHARSET;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $options);
}