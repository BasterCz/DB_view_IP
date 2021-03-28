<?php

require_once "../../_includes/bootstrap.php";
require_once "../../_includes/db.php";
require_once "../../_includes/functions.php";
header("Location: profile.php");
session_start();
var_dump($_POST);


$pdo = dbConnect();
$_SESSION['emailValue'] = $_POST['email']??"";

if (!empty($_POST['email'])) {
    $_SESSION['mailEmpty'] = false;
    $preStmt = "SELECT * FROM `employee` WHERE `login` = :login_";
    $stmt = $pdo->prepare($preStmt);
    $stmt->execute([":login_" => $_POST['email']]);
    $row = $stmt->fetch();
    if ($stmt->rowCount() !== 0) {
        $_SESSION['mailWrong'] = false;
        if (password_verify($_POST['pass'] ?? "", $row['password']) || (empty($row['password']) && empty($_POST['pass']))) {
            session_destroy();
            session_start();
            $_SESSION = [];
            $_SESSION['loggedIn'] = true;
            $_SESSION['name'] = $row['name'] . " " . $row['surname'];
            $_SESSION['id'] = $row['employee_id'];
            $_SESSION['admin'] = $row['admin'];
        } 
        else {
            $_SESSION['passNo'] = true;
        }
    }
    else {
        $_SESSION['mailWrong'] = true;
    }
} else {
    $_SESSION['mailEmpty'] = true;
}

die();
