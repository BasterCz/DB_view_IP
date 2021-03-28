<?php
class Key
{
    protected $storage = [
        "roomId" => 0,
        "roomName" => "",
        "roomNo" => 0,
        "iHaveKey" => false,
        "selected" => false
    ];
    public function getStorage()
    {
        return $this->storage;
    }
    public function __construct(int $roomId,string $roomName,string $roomNo, bool $iHaveKey)
    {
        $this->storage['roomId'] = $roomId;
        $this->storage['roomName'] = $roomName;
        $this->storage['roomNo'] = $roomNo;
        $this->storage['iHaveKey'] = $iHaveKey;
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
