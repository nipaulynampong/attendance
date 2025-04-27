<?php
session_start();
require 'QRCodeAttendance/conn/conn.php';

// Function to check if date falls within event period
function checkEventStatus($date) {
    global $conn;
    $query = "SELECT * FROM events WHERE :date BETWEEN start_date AND end_date";
    $stmt = $conn->prepare($query);
    $stmt->execute(['date' => $date]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_event'])) {
        try {
            $query = "INSERT INTO events (event_name, start_date, end_date, description, event_type, location, 
                                        departments, required_attendance) 
                     VALUES (:event_name, :start_date, :end_date, :description, :event_type, :location,
                             :departments, :required_attendance)";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                'event_name' => $_POST['event_name'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'description' => $_POST['description'],
                'event_type' => $_POST['event_type'],
                'location' => $_POST['location'],
                'departments' => isset($_POST['departments']) ? implode(',', $_POST['departments']) : 'All',
                'required_attendance' => isset($_POST['required_attendance']) ? 1 : 0
            ]);

            $_SESSION['success'] = "Event added successfully!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error adding event: " . $e->getMessage();
        }
        header("Location: events.php");
        exit();
    }

    // Handle event deletion
    if (isset($_POST['delete_event'])) {
        try {
            $query = "DELETE FROM events WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['id' => $_POST['event_id']]);
            
            $_SESSION['success'] = "Event deleted successfully!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error deleting event: " . $e->getMessage();
        }
        header("Location: events.php");
        exit();
    }

    // Handle event updates
    if (isset($_POST['update_event'])) {
        try {
            $query = "UPDATE events SET 
                     event_name = :event_name,
                     start_date = :start_date,
                     end_date = :end_date,
                     description = :description,
                     event_type = :event_type,
                     location = :location,
                     departments = :departments,
                     required_attendance = :required_attendance
                     WHERE id = :id";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                'event_name' => $_POST['event_name'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'description' => $_POST['description'],
                'event_type' => $_POST['event_type'],
                'location' => $_POST['location'],
                'departments' => isset($_POST['departments']) ? implode(',', $_POST['departments']) : 'All',
                'required_attendance' => isset($_POST['required_attendance']) ? 1 : 0,
                'id' => $_POST['event_id']
            ]);

            $_SESSION['success'] = "Event updated successfully!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error updating event: " . $e->getMessage();
        }
        header("Location: events.php");
        exit();
    }
}

// Get all departments for the dropdown
$dept_query = "SELECT DISTINCT Department FROM department ORDER BY Department";
$dept_stmt = $conn->query($dept_query);
$departments = $dept_stmt->fetchAll(PDO::FETCH_COLUMN);

// Get all events
$events_query = "SELECT * FROM events ORDER BY start_date DESC";
$events_stmt = $conn->query($events_query);
$events_result = $events_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4F6F52;
            --secondary-color: #739072;
            --light-color: #ECE3CE;
            --dark-color: #3A4D39;
            --background-color: #f5efe6;
            --card-color: #FFFFFF;
            --text-color: #333333;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 12px;
            border: none;
        }

        .table td {
            padding: 12px;
            border: none;
            border-bottom: 1px solid #f2f2f2;
        }

        .table tr:hover {
            background-color: rgba(79, 111, 82, 0.05);
        }

        .badge-event {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: normal;
        }

        .badge-Company {
            background-color: #A6E3A1;
            color: #2D5E2D;
        }

        .badge-Department {
            background-color: #ADE8F4;
            color: #1A5F7A;
        }

        .badge-Training {
            background-color: #FFECD6;
            color: #996600;
        }

        .badge-Other {
            background-color: #E9D8FD;
            color: #6B46C1;
        }

        .required-attendance {
            color: #FF6B6B;
            font-weight: bold;
        }

        .table .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
        }

        .table .btn i {
            margin-right: 0;
        }

        .table .btn-info {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        .table .btn-warning {
            background-color: #FFA41B;
            border-color: #FFA41B;
            color: white;
        }

        .table .btn-danger {
            background-color: #FF6B6B;
            border-color: #FF6B6B;
            color: white;
        }

        .table .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }

        .table .btn-info:hover {
            background-color: #658366;
            border-color: #658366;
        }

        .table .btn-warning:hover {
            background-color: #F29727;
            border-color: #F29727;
        }

        .table .btn-danger:hover {
            background-color: #EE5D5D;
            border-color: #EE5D5D;
        }

        .action-buttons {
            white-space: nowrap;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .action-buttons .btn,
        .action-buttons form {
            margin: 0;
            padding: 0;
        }

        .action-buttons .btn {
            border-radius: 0;
            margin: 0;
            height: 32px;
            width: 32px;
        }

        .action-buttons .btn:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .action-buttons .btn:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .action-buttons form {
            display: inline-flex;
        }

        td {
            vertical-align: middle !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-calendar-alt"></i> Event Management</a>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add New Event</h5>
                    </div>
                    <div class="card-body">
                        <form action="events.php" method="POST">
                            <div class="form-group">
                                <label>Event Name</label>
                                <input type="text" name="event_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Event Type</label>
                                <select name="event_type" class="form-control" required>
                                    <option value="">Select Event Type</option>
                                    <option value="Company">Company</option>
                                    <option value="Department">Department</option>
                                    <option value="Training">Training</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Departments</label>
                                <select name="departments[]" class="form-control" multiple>
                                    <option value="All">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>">
                                            <?php echo htmlspecialchars($dept); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="required_attendance" name="required_attendance">
                                    <label class="custom-control-label" for="required_attendance">Required Attendance</label>
                                </div>
                            </div>

                            <button type="submit" name="add_event" class="btn btn-primary">Add Event</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Events List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($events_result as $event): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($event['event_name']); ?>
                                                <?php if ($event['required_attendance']): ?>
                                                    <span class="required-attendance ml-1" title="Required Attendance">*</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $start = date('M d, Y', strtotime($event['start_date']));
                                                    $end = date('M d, Y', strtotime($event['end_date']));
                                                    echo $start === $end ? $start : "$start - $end";
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-event badge-<?php echo $event['event_type']; ?>">
                                                    <?php echo $event['event_type']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($event['location']); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewEvent<?php echo $event['id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editEvent<?php echo $event['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="events.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                                        <button type="submit" name="delete_event" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this event?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- View Modal -->
                                        <div class="modal fade" id="viewEvent<?php echo $event['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><?php echo htmlspecialchars($event['event_name']); ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                                                        <p><strong>Departments:</strong> <?php echo htmlspecialchars($event['departments']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editEvent<?php echo $event['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Event</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="events.php" method="POST">
                                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                                            
                                                            <div class="form-group">
                                                                <label>Event Name</label>
                                                                <input type="text" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Start Date</label>
                                                                <input type="date" name="start_date" class="form-control" value="<?php echo $event['start_date']; ?>" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>End Date</label>
                                                                <input type="date" name="end_date" class="form-control" value="<?php echo $event['end_date']; ?>" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Description</label>
                                                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($event['description']); ?></textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Event Type</label>
                                                                <select name="event_type" class="form-control" required>
                                                                    <option value="">Select Event Type</option>
                                                                    <option value="Company" <?php echo $event['event_type'] === 'Company' ? 'selected' : ''; ?>>Company</option>
                                                                    <option value="Department" <?php echo $event['event_type'] === 'Department' ? 'selected' : ''; ?>>Department</option>
                                                                    <option value="Training" <?php echo $event['event_type'] === 'Training' ? 'selected' : ''; ?>>Training</option>
                                                                    <option value="Other" <?php echo $event['event_type'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Location</label>
                                                                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($event['location']); ?>">
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Departments</label>
                                                                <select name="departments[]" class="form-control" multiple>
                                                                    <?php 
                                                                    $event_depts = explode(',', $event['departments']);
                                                                    ?>
                                                                    <option value="All" <?php echo in_array('All', $event_depts) ? 'selected' : ''; ?>>All Departments</option>
                                                                    <?php foreach ($departments as $dept): ?>
                                                                        <option value="<?php echo htmlspecialchars($dept); ?>" 
                                                                                <?php echo in_array($dept, $event_depts) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($dept); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input" 
                                                                           id="edit_required_attendance<?php echo $event['id']; ?>" 
                                                                           name="required_attendance"
                                                                           <?php echo $event['required_attendance'] ? 'checked' : ''; ?>>
                                                                    <label class="custom-control-label" 
                                                                           for="edit_required_attendance<?php echo $event['id']; ?>">Required Attendance</label>
                                                                </div>
                                                            </div>

                                                            <button type="submit" name="update_event" class="btn btn-primary">Update Event</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Validation for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.querySelector('input[name="start_date"]');
            const endDate = document.querySelector('input[name="end_date"]');

            startDate.addEventListener('change', function() {
                endDate.min = this.value;
            });

            endDate.addEventListener('change', function() {
                startDate.max = this.value;
            });
        });
    </script>
</body>
</html>