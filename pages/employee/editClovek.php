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
$keychain = new Keys();
$errorList = new ErrorList(
    [
        new SingleError("wrongName", false),
        new SingleError("nullName", false),
        new SingleError("wrongSurname", false),
        new SingleError("nullSurname", false),
        new SingleError("wrongJob", false),
        new SingleError("nullJob", false),
        new SingleError("wrongWage", false),
        new SingleError("nullWage", false),
        new SingleError("wrongRoom", false),
        new SingleError("nullRoom", false),
        new SingleError("invalidRoom", false),
        new SingleError("wrongLogin", false),
        new SingleError("nullLogin", false),
        new SingleError("sameLogin", false),
        new SingleError("wrongPass", false),
        new SingleError("shortPass", false),
    ]
);

//databáze
$clovekId = (int) ($_GET['clovekId'] ?? 0);
$clovekId > 0 ?  $badgateway = false : $badgateway = true;
$pdo = dbConnect();
$stmt = $pdo->prepare('SELECT *, employee.name as "firstname" FROM employee JOIN room WHERE room=room_id AND `employee`.`employee_id`=:clovekId');
$stmt->execute(['clovekId' => $clovekId]);
$keys = $pdo->prepare('SELECT DISTINCT *, room.name as "roomname",`key`.`room` as "roomid" FROM `key` JOIN `room`, `employee` WHERE `key`.`room`=`room`.`room_id` AND `employee`.`employee_id`=`key`.`employee` AND `employee`.`employee_id`=:clovekId');
$keys->execute(['clovekId' => $clovekId]);
$allKeys = $pdo->prepare('SELECT DISTINCT * FROM `room`');
$allKeys->execute([]);

//existence sama
if ($stmt->rowCount() == 0) {
    if ($badgateway) http_response_code(500);
    else http_response_code(404);
    $success = false;
} else {
    $success = true;
    $row = $stmt->fetch();
}

//přidání všech exstujících klíčů
foreach ($allKeys as $key) {
    $keychain->add(new Key($key['room_id'], $key['name'], $key['no'], false));
}

//generace stránky
echo ($m->render("head", ["title" => "Edit"]));
echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

//error výpis
if (!$success) {
    if ($badgateway) {
        Error500("lide.php", "seznam zaměstnanců");
    } else {
        Error404("lide.php", "seznam zaměstnanců");
    }
} 
else {
    if ($loggedIn && $admin) {

        //profilace
        $profile = new ProfileClovek(
            $row['employee_id'],
            $row['firstname'],
            $row['surname'],
            $row['job'],
            $row['wage'],
            $row['room'],
            $row['login'],
            $row['admin'] === 1 ? true : false
        );

        //přidání existujících klíčů
        foreach ($keys as $key) {
            $keychain->iHaveAKeyChange($key['room_id'], true);
        }
        
        $valid = false;
        //update
        if($profile->Validate($errorList, $keychain)){
            $valid = true;
            $update = $pdo->prepare(
                "UPDATE employee SET name = :name_, surname = :surname_, job = :job_, wage=:wage_, room = :room_, `login` = :login_,  `admin` = :admin_ WHERE employee_id= :id_"
            );
            $update->execute([
                ":name_" => $profile->name,
                ":surname_" => $profile->surname,
                ":job_" => $profile->job,
                ":wage_" => $profile->wage,
                ":room_" => $profile->room,
                ":login_" => $profile->login,
                ":admin_" => $profile->admin ? 1:0,
                ":id_" => $profile->employee_id
            ]);

            $update = $pdo->prepare("DELETE FROM `key` WHERE employee = :id_");
            $update->execute([":id_" => $profile->employee_id]);
            foreach ($keychain->keys as $key) {
                if ($key->iHaveKey) {
                    $update = $pdo->prepare("INSERT INTO `key` (employee, room) VALUES (:employee_, :room_)");
                    $update->execute([
                        ":employee_" => $profile->employee_id,
                        ":room_" => $key->roomId,
                    ]);
                }
            }
        }

        //výběr moožnosti domovské stránky
        $keychain->selectActual($profile->room, true);

        //výpis formuláře
        if($valid) echo $m->render("createOK", ["destination" => "lide.php"]);
        echo ($m->render("editClovek", [
            "title" => "Editace",
            "profile" => $profile->getArrayStorage(),
            "errors" => $errorList->getArrayStorage(),
            "keys" => $keychain->getArrayStorage(),
            "destination" => ""
        ]));
        if($valid) echo $m->render("createOK", ["destination" => "lide.php"]);
    } else {
        //nepřihlášený/neautorizovaný člověk
        echo $m->render("noAuth");
    }
}
unset($stmt);
echo $m->render("foot");
