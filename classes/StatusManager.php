<?php
require_once __DIR__ . '/DatabaseConnection.php';
require_once __DIR__ . '/ErrorHandler.php';
require_once __DIR__ . '/../config/constants.php';

class StatusManager {
    private $db;
    private $errorHandler;
    private static $instance = null;

    private function __construct() {
        $this->db = DatabaseConnection::getInstance();
        $this->errorHandler = ErrorHandler::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function updateStatus($studentId, $date, $status, $adminId) {
        try {
            // Validate inputs
            if (!$this->validateStatus($status)) {
                throw new Exception("Invalid status value provided");
            }

            if (!$this->validateDate($date)) {
                throw new Exception("Invalid date format");
            }

            // Start transaction
            $this->db->beginTransaction();

            // Check if status already exists
            $existingStatus = $this->getStatus($studentId, $date);
            
            if ($existingStatus) {
                // Update existing status
                $query = "UPDATE attendance_records 
                         SET status = ?, modified_by = ?, modified_at = NOW() 
                         WHERE student_id = ? AND date = ?";
                $params = [$status, $adminId, $studentId, $date];
            } else {
                // Insert new status
                $query = "INSERT INTO attendance_records 
                         (student_id, date, status, created_by, created_at, modified_by, modified_at) 
                         VALUES (?, ?, ?, ?, NOW(), ?, NOW())";
                $params = [$studentId, $date, $status, $adminId, $adminId];
            }

            $result = $this->db->execute($query, $params);

            // Log the change
            $this->logStatusChange($studentId, $date, $status, $adminId);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            $this->errorHandler->handleError($e);
            return false;
        }
    }

    public function getStatus($studentId, $date) {
        try {
            $query = "SELECT status FROM attendance_records 
                     WHERE student_id = ? AND date = ?";
            $result = $this->db->execute($query, [$studentId, $date]);
            
            if ($result && $row = $result->fetch_assoc()) {
                return $row['status'];
            }
            return null;

        } catch (Exception $e) {
            $this->errorHandler->handleError($e);
            return null;
        }
    }

    public function getStudentStatuses($studentId, $startDate, $endDate) {
        try {
            $query = "SELECT date, status FROM attendance_records 
                     WHERE student_id = ? AND date BETWEEN ? AND ? 
                     ORDER BY date ASC";
            $result = $this->db->execute($query, [$studentId, $startDate, $endDate]);
            
            $statuses = [];
            while ($row = $result->fetch_assoc()) {
                $statuses[$row['date']] = $row['status'];
            }
            return $statuses;

        } catch (Exception $e) {
            $this->errorHandler->handleError($e);
            return [];
        }
    }

    public function getDailyReport($date) {
        try {
            $query = "SELECT ar.student_id, s.name, ar.status 
                     FROM attendance_records ar 
                     JOIN students s ON ar.student_id = s.id 
                     WHERE ar.date = ? 
                     ORDER BY s.name ASC";
            $result = $this->db->execute($query, [$date]);
            
            $report = [];
            while ($row = $result->fetch_assoc()) {
                $report[] = $row;
            }
            return $report;

        } catch (Exception $e) {
            $this->errorHandler->handleError($e);
            return [];
        }
    }

    private function validateStatus($status) {
        return in_array($status, [
            STATUS_PRESENT,
            STATUS_ABSENT,
            STATUS_LATE,
            STATUS_EXCUSED
        ]);
    }

    private function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function logStatusChange($studentId, $date, $status, $adminId) {
        try {
            $query = "INSERT INTO attendance_logs 
                     (student_id, date, status, admin_id, created_at) 
                     VALUES (?, ?, ?, ?, NOW())";
            $this->db->execute($query, [$studentId, $date, $status, $adminId]);
        } catch (Exception $e) {
            // Log the error but don't throw it (non-critical operation)
            $this->errorHandler->logError($e);
        }
    }

    public function getStatusStatistics($startDate, $endDate) {
        try {
            $query = "SELECT status, COUNT(*) as count 
                     FROM attendance_records 
                     WHERE date BETWEEN ? AND ? 
                     GROUP BY status";
            $result = $this->db->execute($query, [$startDate, $endDate]);
            
            $stats = [];
            while ($row = $result->fetch_assoc()) {
                $stats[$row['status']] = $row['count'];
            }
            return $stats;

        } catch (Exception $e) {
            $this->errorHandler->handleError($e);
            return [];
        }
    }
} 