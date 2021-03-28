<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";

//přihlášení
session_start();
$loggedIn = $_SESSION['loggedIn'] ?? false;
$admin = $_SESSION['admin'] ?? false;

//classy
$m = new MustacheRunner();
$errorList = new ErrorList(
    [
        new SingleError("wrongNum", false),
        new SingleError("nullNum", false),
        new SingleError("invalidNum", false),
        new SingleError("wrongName", false),
        new SingleError("nullName", false),
        new SingleError("wrongPhone", false),
        new SingleError("nullPhone", false),
        new SingleError("invalidPhone", false),
    ]
);

//databáze
$roomId = (int) ($_GET['roomId'] ?? 0);
$roomId > 0 ?  $badgateway = false : $badgateway = true;
$pdo = dbConnect();
$stmt = $pdo->prepare('SELECT * FROM room WHERE room_id= :roomId_');
$stmt->execute([':roomId_' => $roomId]);

//existence sama
if ($stmt->rowCount() == 0) {
    if ($badgateway) http_response_code(500);
    else http_response_code(404);
    $success = false;
} else {
    $success = true;
    $row = $stmt->fetch();
}

//generace stránky
echo ($m->render("head", ["title" => "Edit"]));
echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

//error výpis
if (!$success) {
    if ($badgateway) {
        Error500("mistnosti.php", "seznam místností");
    } else {
        Error404("mistnosti.php", "seznam místností");
    }
} 
else {
    if ($loggedIn && $admin) {

        //profilace
        $profile = new ProfileMistnost(
            $row['room_id'],
            (int)$row['no'],
            $row['name'],
            (int)$row['phone'] ?? 0,
        );
        
        //update
        $valid = false;
        if($profile->Validate($errorList)){
            $valid=true;
            $update = $pdo->prepare(
                "UPDATE room SET `no` = :no_, `name` = :name_, `phone` = :phone_ WHERE room_id= :room_id_"
            );
            $update->execute([
                ":room_id_" => $profile->room_id,
                ":no_" => $profile->no,
                ":name_" => $profile->name,
                ":phone_" => $profile->phone
            ]);
        }

        //výpis formuláře
        if($valid) echo $m->render("createOK", ["destination" => "mistnosti.php"]);
        echo ($m->render("editMistnost", [
            "title" => "Editace",
            "room" => $profile->getArrayStorage(),
            "errors" => $errorList->getArrayStorage(),
            "destination" => ""
        ]));
        if($valid) echo $m->render("createOK", ["destination" => "mistnosti.php"]);
    } else {
        //nepřihlášený/neautorizovaný člověk
        echo $m->render("noAuth");
    }
}
echo $m->render("foot");
