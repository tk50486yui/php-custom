<?php
require_once '../app/Core/BaseRepo.php';

class MyRepo extends BaseRepo
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function toArray($array)
    {
        if (is_null($array)) {
            return [];
        }

        if (!is_array($array)) {
            return [$array];
        }

        if (is_array($array) && count($array) > 0 && array_keys($array) !== range(0, count($array) - 1)) {
            return [$array];
        }

        return $array;
    }

    public function getFirst($array)
    {
        $array = $this->toArray($array);
        if (count($array) > 0) {
            return $array[0];
        }
        return [];
    }
}
