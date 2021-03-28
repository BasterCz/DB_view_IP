<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";

session_start();
$m = new MustacheRunner();
echo $m->render("head", ["title" => "Rozcestník"]);
echo($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));
echo $m->render("rozcestnik");
echo $m->render("foot");