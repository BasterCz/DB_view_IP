<?php
class ProfileMistnost
{
    private $storage = [
        "room_id" => -1,
        "no" => 0,
        "name" => "",
        "phone" => 0,
    ];
    public function getArrayStorage()
    {
        return $this->storage;
    }
    public function __construct(int $id, int $no, string $name, int $phone)
    {
        $this->storage['room_id'] = $id;
        $this->storage['no'] = $no;
        $this->storage['name'] = $name;
        $this->storage['phone'] = $phone;
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

    public function Validate(&$errorList)
    {
        if (!empty($_POST)) {

            $pdo = dbConnect();
            
            $no_ = $_POST['no'];
            if (empty($no_)) {
                $errorList->isActiveChange("wrongNum", true);
                $errorList->isActiveChange("nullNum", true);
            } else {
                $validPDOcheck = $pdo->prepare("SELECT * FROM `room` WHERE `no`=:no_");
                $validPDOcheck->execute([":no_" => $no_]);
                $row = $validPDOcheck->fetch();
                $validPDOcheck->execute([":no_" => $no_]);
                if (empty($validPDOcheck->fetch()) || $_POST['id'] == $row['room_id']) {
                    $this->no = filter_var($no_, FILTER_VALIDATE_INT);
                } else {
                    $errorList->isActiveChange("wrongNum", true);
                    $errorList->isActiveChange("invalidNum", true);
                }
            }
            $name_ = $_POST['name'];
            if (empty($name_)) {
                $errorList->isActiveChange("wrongName", true);
                $errorList->isActiveChange("nullName", true);
            } else {
                $this->name = filter_var($name_, FILTER_SANITIZE_STRING);
            }
            $phone_ = $_POST['phone'];
            if (empty($phone_)) {
                $errorList->isActiveChange("wrongPhone", true);
                $errorList->isActiveChange("nullPhone", true);
            } else {
                $validPDOcheck = $pdo->prepare("SELECT * FROM `room` WHERE `phone`=:phone_");
                $validPDOcheck->execute([":phone_" => $phone_]);
                $row = $validPDOcheck->fetch();
                $validPDOcheck->execute([":phone_" => $phone_]);
                if (empty($validPDOcheck->fetch()) || $_POST['id'] == $row['room_id']) {
                    $this->phone = filter_var($phone_, FILTER_VALIDATE_INT);
                } else {
                    $errorList->isActiveChange("wrongPhone", true);
                    $errorList->isActiveChange("invalidPhone", true);
                }
            }
            if ($errorList->noError()) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
