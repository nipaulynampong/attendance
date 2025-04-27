<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <style>
        /* Styles for overlay message */
        .overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <h1>Attendance System</h1>
    <!-- Placeholder for other HTML content -->
    
    <script>
        // AJAX function to handle POST request
        function postRequest(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                // Check if the response indicates employee existence
                if (data.exists) {
                    // Employee exists
                    // Call a function to display overlay message
                    displayOverlayMessage("Employee exists. Attendance recorded successfully.");
                } else {
                    // Employee does not exist
                    // Call a function to display overlay message
                    displayOverlayMessage("Employee not found.");
                }
            })
            .catch(error => {
                // Handle error
                console.error('Error:', error);
            });
        }

        // Function to display overlay message
        function displayOverlayMessage(message) {
            // Create an overlay element
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            overlay.textContent = message;

            // Append the overlay to the body
            document.body.appendChild(overlay);

            // Set a timeout to remove the overlay after a certain duration
            setTimeout(() => {
                document.body.removeChild(overlay);
            }, 3000); // Adjust the duration as needed
        }

        // Example usage:
        const employeeId = "123"; // Replace with actual employee ID
        const postData = { employee_id: employeeId };

        postRequest('attendance.php', postData);
    </script>
</body>
</html>
