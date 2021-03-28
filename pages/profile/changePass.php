<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";
require_once "../../_includes/functions.php";
require_once "../../_includes/db.php";
header("Location: profile.php");
session_start();



$pdo = dbConnect();
$preStmt = "SELECT * FROM `employee` WHERE `employee_id`= :id_";
$stmt = $pdo->prepare($preStmt);
$stmt->execute([":id_"=> $_SESSION['id']]);
$row = $stmt->fetch();
$heslo = $row['password'];
$oldPass = $_POST['oldPass'] ?? "";
$newPass = $_POST['newPass'] ?? "";
if ((empty($row['password']) && empty($oldPass)) || password_verify($oldPass, $heslo)) {
    $_SESSION['oldPassWrong']= false;
} else {
    $_SESSION['oldPassWrong']= true;
}
if (empty($newPass) || strlen($newPass) < 8) {
    $_SESSION['newEmpty']=  true;
}
else {
    $_SESSION['newEmpty']=  false;
}
if (($_POST['oldPass'] ?? "") === ($newPass)) {
    $_SESSION['samePass']= true;
}
else {
    $_SESSION['samePass']= false;
}
if (!$_SESSION['newEmpty'] && !$_SESSION['samePass']) {
    $preStmt = "UPDATE `employee` SET `password`=:pass_ WHERE `employee_id`= :id_";
    $stmt = $pdo->prepare($preStmt);
    $stmt->execute([
        ":pass_" => password_hash($newPass, PASSWORD_BCRYPT), 
        ":id_"=> $_SESSION['id']
        ]);
}

die();
