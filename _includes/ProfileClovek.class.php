<?php
class ProfileClovek
{
    private $storage = [
        "employee_id" => -1,
        "name" => "",
        "surname" => "",
        "job" => "",
        "wage" => 0,
        "room" => -1,
        "login" => "",
        "admin" => false,
    ];
    public function getArrayStorage() 
    {
        return $this->storage;
    }
    public function __construct(int $id, string $name, string $surname, string $job, int $wage, int $room, string $login, bool $admin)
    {
        $this->storage['employee_id'] = $id;
        $this->storage['name'] = $name;
        $this->storage['surname'] = $surname;
        $this->storage['job'] = $job;
        $this->storage['wage'] = $wage;
        $this->storage['room'] = $room;
        $this->storage['login'] = $login;
        $this->storage['admin'] = $admin;
    }
    public function __get($propName)
    {
        if (array_key_exists($propName,  $this->storage)) {
            return $this->storage[$propName];
        }
    }
    public  function __set($propName, $value)
    {
        if (array_key_exists($propName, $this->storage)) {
            $this->storage[$propName] = $value;
        }
    }

    public function Validate(&$errorList, &$keychain){
        if (!empty($_POST)) {

            $pdo = dbConnect();



            $name_ = $_POST['name'];
            if (empty($name_)) {
                $errorList->isActiveChange("wrongName", true);
                $errorList->isActiveChange("nullName", true);
            } else {
                $this->name = filter_var($name_, FILTER_SANITIZE_STRING);
            }
            $surname_ = $_POST['surname'];
            if (empty($surname_)) {
                $errorList->isActiveChange("wrongSurname", true);
                $errorList->isActiveChange("nullSurname", true);
            } else {
                $this->surname = filter_var($surname_, FILTER_SANITIZE_STRING);
            }
            $job_ = $_POST['job'];
            if (empty($job_)) {
                $errorList->isActiveChange("wrongJob", true);
                $errorList->isActiveChange("nullJob", true);
            } else {
                $this->job = filter_var($job_, FILTER_SANITIZE_STRING);
            }
            $wage_ = (int)$_POST['wage'];
            if (empty($wage_)) {
                $errorList->isActiveChange("wrongWage", true);
                $errorList->isActiveChange("nullWage", true);
            } else {
                $this->wage = filter_var($wage_, FILTER_VALIDATE_INT);
            }
            $room_ = (int)$_POST['room'];
            if (empty($room_)) {
                $errorList->isActiveChange("wrongRoom", true);
                $errorList->isActiveChange("nullRoom", true);
            } else {
                $validPDOcheck = $pdo->prepare("SELECT * FROM `room` WHERE `room_id`=:room");
                $validPDOcheck->execute([":room" => $room_]);
                if (empty($validPDOcheck->fetch())) {
                    $errorList->isActiveChange("wrongRoom", true);
                    $errorList->isActiveChange("invalidRoom", true);
                } else {
                    $this->room = filter_var($room_, FILTER_VALIDATE_INT);
                }
            }
            $login_ = $_POST['login'];
            if (empty($login_)) {
                $errorList->isActiveChange("wrongLogin", true);
                $errorList->isActiveChange("nullLogin", true);
            } else {
                $validPDOcheck = $pdo->prepare("SELECT * FROM `employee` WHERE `login`=:login_");
                $validPDOcheck->execute([":login_" => $login_]);
                $row = $validPDOcheck->fetch();
                $validPDOcheck->execute([":login_" => $login_]);
                if(empty($validPDOcheck->fetch()) || $_POST['id'] == $row['employee_id']) {
                    $this->login = filter_var($login_, FILTER_VALIDATE_EMAIL);
                }
                else {
                    $errorList->isActiveChange("wrongLogin", true);
                    $errorList->isActiveChange("sameLogin", true);
                }
            }
            $admin_ = filter_input(INPUT_POST, "admin", FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $this->admin = $admin_;
            $keyList_ = $_POST['keyList'] ?? [];
            $keychain->clearIHaveKey();
            $keychain->clearSelected();
            foreach ($keyList_ as $key) {
                $keychain->iHaveAKeyChange($key, true);
            }


            //update a zápis do databáze
            if ($errorList->noError()) {
                return true;
            }else{
                return false;
            }
        }
        return false;
    }
}
