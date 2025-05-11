<?php
require_once __DIR__ . '/../config/database.php';

class DatabaseConnection {
    private static $instance = null;
    private $connection = null;
    private $retryCount = 0;

    private function __construct() {
        $this->connect();
    }

    // Singleton pattern to ensure only one database connection
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        while ($this->retryCount < DB_MAX_RETRIES) {
            try {
                $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                if ($this->connection->connect_error) {
                    throw new Exception("Connection failed: " . $this->connection->connect_error);
                }

                // Set charset and timezone
                $this->connection->set_charset("utf8mb4");
                $this->connection->query("SET time_zone = '+08:00'");
                
                return;
            } catch (Exception $e) {
                $this->retryCount++;
                if ($this->retryCount >= DB_MAX_RETRIES) {
                    throw new Exception("Failed to connect after " . DB_MAX_RETRIES . " attempts: " . $e->getMessage());
                }
                sleep(DB_RETRY_DELAY);
            }
        }
    }

    public function getConnection() {
        if ($this->connection === null || !$this->connection->ping()) {
            $this->connect();
        }
        return $this->connection;
    }

    public function prepare($query) {
        return $this->getConnection()->prepare($query);
    }

    public function query($query) {
        return $this->getConnection()->query($query);
    }

    public function beginTransaction() {
        $this->getConnection()->begin_transaction();
    }

    public function commit() {
        $this->getConnection()->commit();
    }

    public function rollback() {
        $this->getConnection()->rollback();
    }

    public function escapeString($string) {
        return $this->getConnection()->real_escape_string($string);
    }

    public function getLastError() {
        return $this->getConnection()->error;
    }

    public function getLastInsertId() {
        return $this->getConnection()->insert_id;
    }

    public function close() {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    public function __destruct() {
        $this->close();
    }
} 