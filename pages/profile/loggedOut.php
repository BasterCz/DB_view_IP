<?php
header("Location: profile.php");
session_start();
session_destroy();
$_SESSION = [];

die();
