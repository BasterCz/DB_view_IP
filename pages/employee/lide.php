<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";



$m = new MustacheRunner();

session_start();
$loggedIn = $_SESSION['loggedIn'] ?? false;
$admin = $_SESSION['admin'] ?? false;
$roomId = (int) ($_GET['roomId'] ?? 0);
$orders  = array("", "surname", "roomname", "phone", "job");
$key = array_search($_GET['filter'] ?? "", $orders);
$filter = $orders[$key];
$order = filter_var($_GET['order'] ?? true, FILTER_VALIDATE_BOOLEAN);

echo ($m->render("head", ["title" => "Lidé"]));
echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

$pdo = dbConnect();
$order ? $orderSQL = "ASC" : $orderSQL =  "DESC";
$preStmt = 'SELECT *, employee.name as "firstname", room.name as "roomname" FROM employee JOIN room WHERE employee.room = room.room_id' . ($filter !== "" ? " ORDER BY " . $filter . " " . $orderSQL : "");
$stmt = $pdo->prepare($preStmt);
$stmt->execute([]);

if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    if ($loggedIn) {
        echo ($m->render("peopleList", ["people" => $stmt, "admin_S" => $admin]));
    } else {
        echo $m->render("noAuth");
    }
}
echo $m->render("foot");
