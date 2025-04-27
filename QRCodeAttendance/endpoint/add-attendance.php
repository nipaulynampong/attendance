<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qr_code'])) {
        $qrCode = $_POST['qr_code'];

        $selectStmt = $conn->prepare("SELECT tbl_student_id FROM tbl_student WHERE generated_code = :generated_code");
        $selectStmt->bindParam(":generated_code", $qrCode, PDO::PARAM_STR);

        if ($selectStmt->execute()) {
            $result = $selectStmt->fetch();
            if ($result !== false) {
                $studentID = $result["tbl_student_id"];

                // Check if there's an existing time_in for the student
                $checkStmt = $conn->prepare("SELECT * FROM tbl_attendance WHERE tbl_student_id = :tbl_student_id AND time_out IS NULL");
                $checkStmt->bindParam(":tbl_student_id", $studentID, PDO::PARAM_STR);
                if ($checkStmt->execute()) {
                    $existingRecord = $checkStmt->fetch();
                    if ($existingRecord !== false) {
                        $timeOut = date("Y-m-d H:i:s"); // Record the time_out
                        $updateStmt = $conn->prepare("UPDATE tbl_attendance SET time_out = :time_out WHERE id = :attendance_id");
                        $updateStmt->bindParam(":time_out", $timeOut, PDO::PARAM_STR);
                        $updateStmt->bindParam(":attendance_id", $existingRecord['id'], PDO::PARAM_INT);
                        $updateStmt->execute();
                    } else {
                        // No existing record with time_in, so create a new record with time_in
                        $timeIn = date("Y-m-d H:i:s");
                        $stmt = $conn->prepare("INSERT INTO tbl_attendance (tbl_student_id, time_in) VALUES (:tbl_student_id, :time_in)");
                        $stmt->bindParam(":tbl_student_id", $studentID, PDO::PARAM_STR);
                        $stmt->bindParam(":time_in", $timeIn, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                } else {
                    echo "Failed to execute the statement to check existing record.";
                }

                header("Location: http://localhost/qr-code-attendance-system/index.php");
                exit();
            } else {
                echo "No student found in QR Code";
            }
        } else {
            echo "Failed to execute the statement.";
        }
    } else {
        echo "
            <script>
                alert('Please fill in all fields!');
                window.location.href = 'http://localhost/qr-code-attendance-system/index.php';
            </script>
        ";
    }
}
?>
