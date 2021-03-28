<?php
class SingleError
{
    private $storage = [
        "name" => "",
        "isActive" => false
    ];
    public function getStorage()
    {
        return $this->storage;
    }
    public function __construct(string $name, bool $isActive)
    {
        $this->storage['name'] = $name;
        $this->storage['isActive'] = $isActive;
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
}
