<?php
// Fichier créer pour des raisons de sécurité et pour ne pas push mes credentials sur github.
class Database
{
    private static $pdo = null;

    const HOST = 'HOST_NAME';
    const DBNAME = 'DB_NAME';
    const USER = 'USER_NAME';
    const PASSWORD = 'PASSWORD';

    public static function getConnection()
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DBNAME . ';charset=utf8';

            try {
                self::$pdo = new PDO($dsn, self::USER, self::PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    'error' => 'Database connection failed'
                ]);
                exit;
            }
        }

        return self::$pdo;
    }
}
