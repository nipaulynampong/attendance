<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student'], $_POST['FIRSTNAME'], $_POST['MNAME'], $_POST['LASTNAME'], $_POST['AGE'], $_POST['GENDER'])) {
        $studentId = $_POST['STUDENTID'];
        $studentName = $_POST['FIRSTNAME'];
        $gender = $_POST['MANME'];
        $address = $_POST['LASTNAME'];
        $contact = $_POST['AGE'];
        $studentCourse = $_POST['GENDER'];

        try {
            $stmt = $conn->prepare("UPDATE student SET FIRSTNAME = :FIRSTNAME, MNAME = :MNAME, LASTNAME = :LASTNAME, AGE = :AGE, GENDER = :GENDER WHERE STUDENTID = :STUDENTID");
            
            $stmt->bindParam(":STUDENTID", $studentId);
            $stmt->bindParam(":FIRSTNAME", $firstName);
            $stmt->bindParam(":MNAME", $mName);
            $stmt->bindParam(":LASTNAME", $lastName);
            $stmt->bindParam(":AGE", $age);
            $stmt->bindParam(":GENDER", $gender);

            $stmt->execute();

            header("Location: http://localhost/QRCodeAttendance/list.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

    } else {
        echo "
            <script>
                alert('Please fill in all fields!');
                window.location.href = 'http://localhost/QRCodeAttendance/list.php';
            </script>
        ";
    }
}
?>
