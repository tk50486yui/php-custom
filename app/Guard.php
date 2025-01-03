<?php
class Guard
{
    private $namespace;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = array();
        }
    }

    public function __set($key, $value)
    {
        $_SESSION[$this->namespace][$key] = $value;
    }

    public function __get($key)
    {
        return isset($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : null;
    }

    public function __isset($key)
    {
        return isset($_SESSION[$this->namespace][$key]);
    }

    public function __unset($key)
    {
        if (isset($_SESSION[$this->namespace][$key])) {
            unset($_SESSION[$this->namespace][$key]);
        }
    }

    public function destroy()
    {
        if (isset($_SESSION[$this->namespace])) {
            unset($_SESSION[$this->namespace]);
        }
    }

    public function check($key)
    {
        return isset($_SESSION[$this->namespace][$key]);
    }

    public function isEmpty()
    {
        return empty($_SESSION[$this->namespace]);
    }
}
