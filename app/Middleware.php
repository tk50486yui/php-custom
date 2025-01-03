<?php
class Middleware
{
    protected function redirect($path)
    {
        header("Location: /$path");
        exit();
    }
}
