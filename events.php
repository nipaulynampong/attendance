<?php
session_start();
require 'QRCodeAttendance/conn/conn.php';

// Function to check if date falls within event period
function checkEventStatus($date) {
    global $conn;
    $query = "SELECT * FROM events WHERE :date BETWEEN start_date AND end_date";
    $stmt = $conn->prepare($query);
    $stmt->execute(['date' => $date]);
    
    $result = $stmt->fetch();
    return $result ? $result : false;
}


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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4F6F52;
            --secondary-color: #739072;
            --light-color: #ECE3CE;
            --dark-color: #3A4D39;
            --background-color: #f5efe6;
            --card-color: #FFFFFF;
            --text-color: #333333;
            --border-radius-lg: 15px;
            --border-radius-md: 10px;
            --border-radius-sm: 6px;
        }

        body, html {
            background-color: var(--background-color);
            font-family: 'Poppins', sans-serif;
        }
        
        /* Override Bootstrap font families */
        .table, .table th, .table td,
        .form-control, .btn, .card, .card-header, .card-body,
        .modal, .modal-title, .modal-body, .modal-footer,
        h1, h2, h3, h4, h5, h6, p, span, div, a, input, select, textarea {
            font-family: 'Poppins', sans-serif !important;
        }

        .container {
            margin: 0 auto;
            padding: 0 20px;
            max-height: 85vh;
            overflow-y: scroll;
        }
        
        /* Scrollbar styling for container */
        .container::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb:hover {
            background: var(--dark-color);
        }

        .navbar {
            background-color: var(--primary-color) !important;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            margin-bottom: 20px;
        }

        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0 !important;
        }

        .form-control {
            border-radius: var(--border-radius-sm);
            border: 1px solid #ced4da;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 111, 82, 0.25);
        }

        .btn {
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease-in-out;
        }

        .modal-content {
            border-radius: var(--border-radius-lg);
            border: none;
        }

        .modal-header {
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            background-color: var(--primary-color);
            color: white;
        }

        .table {
            border-radius: var(--border-radius-md);
            overflow: hidden;
        }

        .table th:first-child {
            border-top-left-radius: var(--border-radius-sm);
        }

        .table th:last-child {
            border-top-right-radius: var(--border-radius-sm);
        }

        .action-buttons .btn {
            border-radius: var(--border-radius-sm);
            margin: 0 2px;
        }

        select.form-control[multiple] {
            border-radius: var(--border-radius-sm);
        }

        .alert {
            border-radius: var(--border-radius-md);
            border: none;
        }

        .badge-event {
            padding: 8px 12px;
            border-radius: var(--border-radius-md);
            font-weight: normal;
        }

        .custom-control-label::before,
        .custom-control-label::after {
            border-radius: var(--border-radius-sm);
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
                                                        <button type="submit" name="delete_event" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this event: <?php echo htmlspecialchars($event['event_name']); ?>?')">
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