<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";



$roomId = (int) ($_GET['roomId'] ?? 0);
$roomId > 0 ?  $badgateway = false : $badgateway = true;

$m = new MustacheRunner();

//přihlášení
header("Location: mistnosti.php");
session_start();

$loggedIn = $_SESSION['loggedIn'] ?? false;
$admin = $_SESSION['admin'] ?? false;
if ($loggedIn && $admin) {
    $pdo = dbConnect();
    $stmt = $pdo->prepare('SELECT * FROM room WHERE`room_id`=:roomId');
    $stmt->execute(['roomId' => $roomId]);
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
            Error500("mistnosti.php", "seznam místností");
        } else {
            Error404("mistnosti.php", "seznam místností");
        }
    } else {
        $stmt = $pdo->prepare('DELETE FROM room WHERE `room_id`=:roomId');
        $stmt->execute(['roomId' => $roomId]);
    }
} else {
    //nepřihlášený/neautorizovaný člověk
    echo $m->render("noAuth");
}

echo $m->render("foot");
