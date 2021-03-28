<?php
class Keys
{
    private $storage = [
        "keys" => [],
        "count" => 0,
    ];
    public function __construct($keys = null)
    {
        if (is_a($keys, 'Key')) {
            $this->storage['keys'][] = $keys;
            $this->storage['count']++;
        } else if (is_array($keys)) {
            foreach ($keys as $key) {
                if (is_a($key, 'Key')) {
                    $this->storage['keys'][]= $keys;
                    $this->storage['count']++;
                }
            }
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
    public function add($keys)
    {
        if (is_a($keys, 'Key')) {
            $this->storage['keys'][] = $keys;
            $this->storage['count']++;
        } else if (is_array($keys)) {
            foreach ($keys as $key) {
                if (is_a($key, 'Key')) {
                    $this->storage['keys'][] = $keys;
                    $this->storage['count']++;
                }
            }
        }
    }
    public function remove(int $roomId)
    {
        foreach ($this->storage['keys'] as $key) {
            if ($key->roomId === $roomId) {
                unset($key);
            }
        }
    }
    public function iHaveAKeyChange(int $roomId, bool $value)
    {
        foreach ($this->storage['keys'] as $key) {
            if ($key->roomId === $roomId) {
                $key->iHaveKey = $value;
            }
        }
    }
    public function selectActual(int $roomId, bool $value)
    {
        foreach ($this->storage['keys'] as $key) {
            if ($key->roomId === $roomId) {
                $key->selected = $value;
            }
        }
    }
    public function getArrayStorage()
    {
        $array = [];
        foreach($this->keys as $key) {
            $array[] = $key->getStorage();
        }
        return $array;
    }
    public function clearIHaveKey() {
        foreach($this->keys as $key) {
            $key->iHaveKey = false;
        }
    }
    public function clearSelected() {
        foreach($this->keys as $key) {
            $key->selected = false;
        }
    }
}
