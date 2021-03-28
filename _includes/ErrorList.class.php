<?php
class ErrorList
{
    private $storage = [
        "error" => [],
        "count" => 0,
    ];
    public function __construct(array $error)
    {
        foreach ($error as $e) {
            if(is_a($e, 'SingleError'))
                $this->storage['error'][]= $e;
                $this->storage['count']++;
        }
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
    public function isActiveChange(string $name, bool $value)
    {
        foreach ($this->error as $e) {
            if ($e->name === $name) {
                $e->isActive = $value;
            }
        }
    }
    public function getArrayStorage()
    {
        $array = [];
        foreach($this->error as $e) {
            $array[$e->name] =  $e->isActive;
        }
        return $array;
    }
    public function getError($name) {
        foreach($this->error as $e) {
            if($e->name === $name ) {
                return $e;
            }
        }
    }

    public function noError() {
        foreach($this->error as $e) {
            if($e->isActive === true) return false;
        }
        return true;
    }
    public function isErrActive(string $name) {
        foreach ($this->error as $e) {
            if ($e->name === $name) {
                return $e->isActive;
            }
        }
    }
}
