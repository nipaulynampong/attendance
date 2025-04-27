<div style="border-radius: 5px;padding:10px;background:#fff;" id="divvideo">
                <table id="example1" class="table table-bordered">
                    <thead style="background-color: #d6dac9; color: black;">
                    <tr>
                        <td>NAME</td>
                        <td>EMPLOYEE ID</td>
                        <td>TIME IN</td>
                        <td>TIME OUT</td>
                        <td>LOGDATE</td>
                        <td>STATUS</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
$server = "localhost";
$username="root";
$password="";
$dbname="hris";

$conn = new mysqli($server,$username,$password,$dbname);
$date = date('Y-m-d');
if($conn->connect_error){
    die("Connection failed" .$conn->connect_error);
}
$sql ="SELECT * FROM attendance 
       LEFT JOIN employee ON attendance.EMPLOYEEID=employee.EmployeeID 
       WHERE DATE(LOGDATE) <= CURDATE()
       ORDER BY LOGDATE DESC, TIMEIN DESC";
$query = $conn->query($sql);
while ($row = $query->fetch_assoc()){
?>
<tr>
    <td><?php echo $row['Last Name'].', '.$row['First Name'];?></td>
    <td><?php echo $row['EMPLOYEEID'];?></td>
    <td><?php echo $row['TIMEIN'];?></td>
    <td><?php echo $row['TIMEOUT'];?></td>
    <td><?php echo $row['LOGDATE'];?></td>
    <td><?php echo $row['STATUS'];?></td>
</tr>
<?php
}
?>

                    </tbody>
                </table>

            </div>