<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";



$m = new MustacheRunner();

session_start();
$loggedIn = $_SESSION['loggedIn'] ?? false;
$roomId = (int) ($_GET['roomId'] ?? 0);
$roomId > 0 ?  $badgateway = false : $badgateway = true;;

$pdo = dbConnect();
$stmt = $pdo->prepare('SELECT *, room.name as "roomname" FROM room  WHERE `room`.`room_id`=:roomId');
$stmt->execute(['roomId' => $roomId]);
$people = $pdo->prepare('SELECT *, room.name as "roomname" FROM room JOIN `employee` WHERE `room`.`room_id`=`employee`.`room` AND `room`.`room_id`=:roomId ');
$people->execute(['roomId' => $roomId]);
$keys = $pdo->prepare('SELECT *, room.name as "roomname",`key`.`room` as "roomid" FROM `key` JOIN `room`, `employee` WHERE `room`.`room_id`=`key`.`room` AND `employee`.`employee_id`=`key`.`employee` AND `room`.`room_id`=:roomId');
$keys->execute(['roomId' => $roomId]);

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
        Error500("mistnosti.php", "seznam místností");
    } else {
        Error404("mistnosti.php", "seznam místností");
    }
} else {

    echo ($m->render("head", ["title" => Title($row['roomname'], $success, $badgateway)]));
    echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

    if ($loggedIn) {
        $prumer = 0;
        $soucet = 0;
        $pocet = 0;
        foreach ($people as $person) {
            $soucet += $person['wage'];
            $pocet++;
        }
        $people->execute(['roomId' => $roomId]);
        if ($pocet > 0) $prumer = $soucet / $pocet;
        echo ($m->render("mistnost", ["mistnost" => $row, "people" => $people, "keys" => $keys, "prumer" => $prumer]));
    } else {
        echo $m->render("noAuth");
    }
}
echo $m->render("foot");
