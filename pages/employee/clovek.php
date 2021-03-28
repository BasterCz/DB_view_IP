<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";

$m = new MustacheRunner();

session_start();
$loggedIn = $_SESSION['loggedIn'] ?? false;
$clovekId = (int) ($_GET['clovekId'] ?? 0);
$clovekId > 0 ?  $badgateway = false : $badgateway = true;

$pdo = dbConnect();
$stmt = $pdo->prepare('SELECT *, employee.name as "firstname" FROM employee JOIN room WHERE room=room_id AND `employee`.`employee_id`=:clovekId');
$stmt->execute(['clovekId' => $clovekId]);
$keys = $pdo->prepare('SELECT DISTINCT *, room.name as "roomname",`key`.`room` as "roomid" FROM `key` JOIN `room`, `employee` WHERE `key`.`room`=`room`.`room_id` AND `employee`.`employee_id`=`key`.`employee` AND `employee`.`employee_id`=:clovekId');
$keys->execute(['clovekId' => $clovekId]);

if ($stmt->rowCount() == 0) {
    if ($badgateway) http_response_code(500);
    else http_response_code(404);
    $success = false;
} else {
    $success = true;
    $row = $stmt->fetch();
}

if (!$success) {
    echo ($m->render("head", ["title" => "Error"]));
    if ($badgateway) {
        Error500("lide.php", "seznam zaměstnanců");
    } else {
        Error404("lide.php", "seznam zaměstnanců");
    }
} else {

    echo ($m->render("head", ["title" => Title($row['firstname'] . " " . $row['surname'], $success, $badgateway)]));
    echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

    if ($loggedIn) {
        echo ($m->render("clovek", ["person" => $row, "keys" => $keys]));
    } else {
        echo $m->render("noAuth");
    }
}
echo $m->render("foot");
