<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";

$clovekId = (int) ($_GET['clovekId'] ?? 0);
$clovekId > 0 ?  $badgateway = false : $badgateway = true;

$m = new MustacheRunner();
//přihlášení
header("Location: lide.php");
session_start();

$loggedIn = $_SESSION['loggedIn'] ?? false;
$admin = $_SESSION['admin'] ?? false;
if ($loggedIn && $admin) {
    $pdo = dbConnect();
    $stmt = $pdo->prepare('SELECT * FROM employee WHERE`employee_id`=:clovekId');
    $stmt->execute(['clovekId' => $clovekId]);
    //existence sama
    if ($stmt->rowCount() == 0) {
        if ($badgateway) http_response_code(500);
        else http_response_code(404);
        $success = false;
    } else {
        $success = true;
        $row = $stmt->fetch();
    }
    echo ($m->render("head", ["title" => "Delete"]));
    echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));
    if (!$success) {
        if ($badgateway) {
            Error500("lide.php", "seznam zaměstnanců");
        } else {
            Error404("lide.php", "seznam zaměstnanců");
        }
    }
    else {
        $stmt = $pdo->prepare('DELETE FROM employee WHERE `employee_id`=:clovekId');
        $stmt->execute(['clovekId' => $clovekId]);
    }
} else {
    //nepřihlášený/neautorizovaný člověk
    echo $m->render("noAuth");
}

unset($stmt);
echo $m->render("foot");
