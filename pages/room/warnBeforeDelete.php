<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";



$m = new MustacheRunner();

session_start();

$roomId = (int) ($_GET['roomId'] ?? 0);
$roomId > 0 ?  $badgateway = false : $badgateway = true;
$loggedIn = $_SESSION['loggedIn'] ?? false;
$admin = $_SESSION['admin'] ?? false;
$pdo = dbConnect();
$stmt = $pdo->prepare('SELECT * FROM room WHERE room_id= :roomId_');
$stmt->execute([':roomId_' => $roomId]);
$lide = $pdo->prepare('SELECT employee_id, `name` as firstname, surname, job FROM employee WHERE room= :roomId_');
$lide->execute([':roomId_' => $roomId]);

//existence sama
if ($stmt->rowCount() == 0) {
    if ($badgateway) http_response_code(500);
    else http_response_code(404);
    $success = false;
} else {
    $success = true;
    $row = $stmt->fetch();
}

echo ($m->render("head", ["title" => "Mazání místnosti"]));
echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

//error výpis
if (!$success) {
    if ($badgateway) {
        Error500("mistnosti.php", "seznam místností");
    } else {
        Error404("mistnosti.php", "seznam místností");
    }
} else {
    if ($loggedIn && $admin) {
        //profilace
        $profile = new ProfileMistnost(
            $row['room_id'],
            $row['no'],
            $row['name'],
            $row['phone'] ?? 0
        );
        echo ($m->render("warning", [
            "title" => "Pozor!",
            "room" => $profile->getArrayStorage(),
            "person" => $lide
        ]));
    }
}
