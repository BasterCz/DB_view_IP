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
    ]
);

//databáze
$pdo = dbConnect();
$allKeys = $pdo->prepare('SELECT DISTINCT * FROM `room`');
$allKeys->execute([]);

foreach ($allKeys as $key) {
    $keychain->add(new Key($key['room_id'], $key['name'], $key['no'], false));
}

//generace stránky
echo ($m->render("head", ["title" => "Nový employee"]));
echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

if ($loggedIn && $admin) {

    //profilace
    $profile = new ProfileClovek(
        -1,
        "",
        "",
        "",
        0,
        -1,
        "",
        false
    );


    //UPLOAD
    $valid = false;
    if ($profile->Validate($errorList, $keychain)) {
        $valid = true;
        $update = $pdo->prepare(
            "INSERT INTO employee 
            (`name`, `surname`, `job`, `wage`, `room`, `login`, `admin`) VALUES 
            (:name_, :surname_, :job_, :wage_, :room_, :login_, :admin_)"
        );
        $update->execute([
            ":name_" => $profile->name,
            ":surname_" => $profile->surname,
            ":job_" => $profile->job,
            ":wage_" => $profile->wage,
            ":room_" => $profile->room,
            ":login_" => $profile->login,
            ":admin_" => $profile->admin ? 1 : 0,
        ]);

        //zjištění nového ID
        $getID = $pdo->prepare("SELECT `employee_id` FROM `employee` WHERE `login`=:login_");
        $getID->execute([":login_" => $profile->login]);
        $ID = $getID->fetch();

        foreach ($keychain->keys as $key) {
            if ($key->iHaveKey) {
                $update = $pdo->prepare("INSERT INTO `key` (employee, room) VALUES (:employee_, :room_)");
                $update->execute([
                    ":employee_" => $ID['employee_id'],
                    ":room_" => $key->roomId,
                ]);
            }
        }
    }

    //výběr moožnosti domovské stránky
    $keychain->selectActual($profile->room, true);
    if($valid) echo $m->render("createOK", ["destination" => "lide.php"]);
    //výpis formuláře
    echo ($m->render("editClovek", [
        "title" => "Vytvoření zaměstnance",
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

echo $m->render("foot");
