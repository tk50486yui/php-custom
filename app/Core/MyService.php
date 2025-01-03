<?php

class MyService
{
    public function toEmpty($value)
    {
        return empty($value) ? '' : $value;
    }
}
