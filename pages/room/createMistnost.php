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


//generace stránky
echo ($m->render("head", ["title" => "Nová mistnost"]));
echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));


if ($loggedIn && $admin) {

    //profilace
    $profile = new ProfileMistnost(
        -1,
        0,
        "",
        0
    );

    //uppload
    $valid = false;
    if ($profile->Validate($errorList)) {
        $valid = true;
        $pdo = dbConnect();
        $update = $pdo->prepare(
            "INSERT INTO room 
            (`no`, `name`, `phone`)  VALUES 
            (:no_, :name_, :phone_)"
        );
        $update->execute([
            ":no_" => $profile->no,
            ":name_" => $profile->name,
            ":phone_" => $profile->phone
        ]);
    }

    if($valid) echo $m->render("createOK", ["destination" => "mistnosti.php"]);
    //výpis formuláře
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

echo $m->render("foot");
