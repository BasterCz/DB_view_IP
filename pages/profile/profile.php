<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";
$m = new MustacheRunner();
$pdo = dbConnect();

	

session_start();
$loggedIn = $_SESSION['loggedIn'] ?? false;

$eLogin = new ErrorList(
    [
        new SingleError("mailEmpty", $_SESSION['mailEmpty'] ?? false),
        new SingleError("mailWrong", false),
        new SingleError("mailSomehowWrong", $_SESSION['mailWrong'] ?? false),
        new SingleError("passWrong", false),
        new SingleError("passNo", $_SESSION['passNo'] ?? false),
    ]
);
$eChange = new ErrorList(
    [
        new SingleError("samePass", $_SESSION['samePass']??false),
        new SingleError("newEmpty", $_SESSION['newEmpty']??false),
        new SingleError("oldPassWrong", $_SESSION['oldPassWrong']??false),
        new SingleError("newPassWrong", false),
    ]
);

if($loggedIn) {
    tryToChangePass($eChange);
}
else {
    tryToLogIn($eLogin);
}

echo ($m->render("head", ["title" => "Profil"]));
echo ($m->render("menuTop", ["name" => $_SESSION['name'] ?? ""]));

if (!$loggedIn) {
    echo ($m->render("login", [
        "errors"=>$eLogin->getArrayStorage(),
        "emailValue" => $_SESSION['emailValue'] ?? ""]));
} 
else {
    echo ($m->render("profile", [
    "name" => $_SESSION['name'] ?? "", 
    "errors"=> $eChange->getArrayStorage(),
    ]));
}
echo $m->render("foot");
