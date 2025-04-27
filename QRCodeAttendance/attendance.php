<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <!-- Custom CSS -->
    <style>
        .custom-container {
            width: 90vw; /* 90% of viewport width */
            margin: auto; /* Center the container */
        }

        #divvideo {
            border-radius: 20px;
            padding: 10px;
            background: #f5efe6;
            max-height: 100vh; /* 70% of viewport height */
            overflow: hidden; /* Hide overflow */
            margin-left: -70px;
            margin-top: 10px;
            width: 1200px;
            border: 2px solid #f5efe6;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        #divvideo table {
            width: 100%; /* Full width of the container */
        }

        #divvideo thead th {
            background-color: #d6dac9; /* Change the background color of table header */
            color: black; /* Change the text color of table header */
        }

    </style>
</head>
<body style="background:white; color: black; font-family: 'Poppins';">
    <div class="container custom-container">
        <div class="row">
            <div class="col-md-12">
            <?php
                if(isset($_SESSION['error'])){
                    echo "
                    <div class='alert alert-danger alert-dismissible' style='background:red;color:#fff'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        <h4><i class='icon fa fa-warning'></i> Error!</h4>
                        ".$_SESSION['error']."
                    </div>
                    ";
                    unset($_SESSION['error']);
                }
                if(isset($_SESSION['success'])){
                    echo "
                    <div class='alert alert-success alert-dismissible' style='background:green;color:#fff'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        <h4><i class='icon fa fa-check'></i> Success!</h4>
                        ".$_SESSION['success']."
                    </div>
                    ";
                    unset($_SESSION['success']);
                }
                ?>
            </div>
            <div class="col-md-12">
                <div id="divvideo" style="font-family:'Poppins';">
                    <h3 style="font-family:'Poppins'; font-weight: bold">Attendance Summary Report</h3><br>
                    <table id="example1" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>EMPLOYEE ID</th>
                                <th>TIME IN</th>
                                <th>TIME OUT</th>
                                <th>LOGDATE</th>
                                <th>STATUS</th>
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
                            $sql ="SELECT * FROM attendance LEFT JOIN employee ON attendance.EMPLOYEEID=employee.EmployeeID";
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
                    <button onclick="Export()" class="btn btn-success">Export</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function Export() {
            var conf = confirm("Please confirm if you wish to proceed in exporting the attendance in to Excel File");
            if (conf == true) {
                window.open("export.php",'_blank');
            }
        }
    </script>

    <!-- Include jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(function () {
            $("#example1").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
            });
        });
    </script>
</body>
</html>
