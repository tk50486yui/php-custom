<?php

class Database
{
    private static $host = 'localhost';
    private static $user = '';
    private static $pass = '';
    private static $name = '';
    private static $conn;

    private static function connect()
    {
        if (!self::$conn) {
            self::$conn = mysqli_connect(self::$host, self::$user, self::$pass, self::$name);

            if (!self::$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            mysqli_query(self::$conn, "SET NAMES 'utf8'");
            mysqli_query(self::$conn, "SET CHARACTER_SET_CLIENT=utf8'");
            mysqli_query(self::$conn, "SET CHARACTER_SET_RESULTS=utf8'");
        }

        return self::$conn;
    }

    public static function getConnection()
    {
        return self::connect();
    }
}
