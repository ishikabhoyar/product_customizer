<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $dsn = "pgsql:host=ep-falling-cell-a4vwmq8v.us-east-1.aws.neon.tech;port=5432;dbname=neondb;sslmode=require;options=endpoint=ep-falling-cell-a4vwmq8v";
            $this->conn = new PDO($dsn, 'neondb_owner', 'npg_6CA1RPlLuNhs', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}


