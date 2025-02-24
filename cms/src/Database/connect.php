<?php

require_once __DIR__ . "/../../config.php";
require_once ABS_PATH . "/config.php";

class connect {
    private static $instance = null;
    private PDO $connection;

    private function __construct() {
        $dbHost = DBHOST;
        $dbUser = DBUSER;
        $dbPassword = DBPASS;
        $dbName = DBNAME;

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true, // Persistentní připojení
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Optimalizace fetch
            ];
            $this->connection = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPassword, $options);
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e ->getMessage());
            die("Database connection error.");
        }
    }

    // Singleton instance
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new connect();
        }
        return self::$instance;
    }

    // Vrací PDO připojení
    public function getConnection() {
        return $this->connection;
    }

    public function query(string $sql, array $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []) {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }

}
?>
