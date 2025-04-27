<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['FIRSTNAME'], $_POST['MNAME'], $_POST['LASTNAME'], $_POST['AGE'], $_POST['GENDER'], $_POST['generated_code'])) {
        $studentName = $_POST['FIRSTNAME'];
        $mName = $_POST['MNAME'];
        $lastName = $_POST['LASTNAME'];
        $age = $_POST['AGE'];
        $gender = $_POST['GENDER'];
        $generatedCode = $_POST['generated_code'];

        try {
            $stmt = $conn->prepare("INSERT INTO student (FIRSTNAME, MNAME, LASTNAME, AGE, GENDER, generated_code) VALUES (:FIRSTNAME, :MNAME, :LASTNAME, :AGE, :GENDER, :generated_code)");

            $stmt->bindParam(":FIRSTNAME", $firstName, PDO::PARAM_STR); 
            $stmt->bindParam(":MNAME", $mName, PDO::PARAM_STR);
            $stmt->bindParam(":LASTNAME", $lastName, PDO::PARAM_STR);
            $stmt->bindParam(":AGE", $age, PDO::PARAM_STR);
            $stmt->bindParam(":GENDER", $gender, PDO::PARAM_STR);
            $stmt->bindParam(":generated_code", $generatedCode, PDO::PARAM_STR);

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
