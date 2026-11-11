
<?php

require_once __DIR__ . '/../config/database.php';

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            global $pdo;

            self::$connection = $pdo;
        }

        return self::$connection;
    }
}
