<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Malinta</title>
    <link rel="icon" href="1.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins';
            background: #9CAFAA;
            height: 100vh;
            overflow: hidden;
        }

        .big-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 1200px; /* Adjust the width as needed for the big container */
            height: 630px;
            background: white; /* Set background color of the big container */
            border-radius: 20px; /* Add border radius to the big container */
            padding: 20px; /* Add padding to the big container */
            box-sizing: border-box; /* Include padding in width and height */
            display: flex; /* Use flexbox to align items */
        }

        .container {
            width: 550px; /* Set a fixed width for .container */
            height: 500px;
            background: #f5f5f5; /* Set background to super light grey */
            border-radius: 15px; /* Add border radius */
            padding: 30px; /* Add padding to the center */
            box-sizing: border-box; /* Include padding in width and height */
            margin-left: 90px;
            margin-top: 40px;
            display: flex; /* Use flexbox */
            flex-direction: column; /* Arrange items vertically */
            justify-content: space-evenly; /* Evenly space items vertically */
            align-items: stretch; /* Stretch items horizontally */
        }

        .left-image {
            width: 450px; /* Set width for the image */
            height: 450px; /* Maintain aspect ratio */
            border-radius: 15px; /* Add border radius to the image */
            margin-left: 30px;
            margin-top: 90px;
        }

        .center {
            width: 100%;
        }

        .center h1 {
            text-align: center;
            padding: 30px 1px; /* Adjust the top and bottom padding */
            border-bottom: 2px solid silver;
            color: black; /* Set color to black */
            line-height: 1; /* Adjust the line-height to make the text higher */
        }

        .text {
            position: relative;
            margin: 30px 0;
            padding: 10px;
        }

        .text input {
            width: 90%;
            padding: 0 5px;
            height: 40px;
            font-size: 16px;
            border: none;
            background: none;
            outline: none;
        }

        .text input:disabled {
            background-color: #ddd; /* Change disabled input background color */
            cursor: not-allowed; /* Change cursor for disabled input */
        }

        .text label {
            position: absolute;
            top: 50%;
            left: 5px;
            color: black;
            transform: translateY(-50%);
            font-size: 16px;
            pointer-events: none;
            transition: .5s;
        }

        .text span::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 0;
            width: 0%;
            height: 2px;
            background: #2691d9;
            transition: .5s;
        }

        .text input:focus ~ label,
        .text input:valid ~ label {
            top: -5px;
            color: #2691d9;
        }

        .text input:focus ~ span::before,
        .text input:valid ~ span::before {
            width: 100%;
        }

        .pass {
            margin: -5px 0 20px 5px;
            color: #a6a6a6;
            cursor: pointer;
        }

        .pass:hover {
            text-decoration: underline;
        }

        input[type="submit"] {
            width: 100%;
            height: 50px;
            margin-bottom: 20px; /* Add margin-bottom for space below the button */
            border: none;
            background: #2691d9;
            border-radius: 25px;
            font-size: 18px;
            color: #e9f4fb;
            font-weight: 700;
            cursor: pointer;
            outline: none;
            transition: .5s;
        }

        input[type="submit"]:hover {
            background: #e9f4fb;
            color: #2691d9;
        }

        /* Overlay styles */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Initially hidden */
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #overlayText {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
        }

        #cooldownTimer {
            color: red;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            display: none; /* Initially hidden */
        }
    </style>
</head>
<body>
    <div class="big-container">
        <img src="2.png" alt="Attendance" class="left-image">
        <div class="container">
            <div class="center">
                <h1> WELCOME BACK <br> ADMINISTRATOR </h1>
                <form id="loginForm">
                    <div class="text" required>
                        <input type="text" id="usernameInput" required>
                        <span></span>
                        <label><br> Username </label>
                    </div>
                    <div class="text" required>
                        <input type="password" id="passwordInput" required>
                        <span></span>
                        <label> Password </label>
                    </div>
                    <input type="submit" value="LOGIN">
                    <div class="pass" id="forgotPass">Forgot Password? Contact the developer </div>
                </form>
            </div>
        </div>
    </div>

    <div id="overlay">
        <div id="overlayText">Incorrect username or password.</div>
    </div>

    <div id="cooldownTimer">
        Cooldown remaining: <span id="cooldownTimerCount">60</span> seconds
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const usernameInput = document.getElementById('usernameInput');
        const passwordInput = document.getElementById('passwordInput');
        const overlay = document.getElementById('overlay');
        const cooldownTimer = document.getElementById('cooldownTimer');
        const cooldownTimerCount = document.getElementById('cooldownTimerCount');
        const forgotPass = document.getElementById('forgotPass');

        let loginAttempts = 0;
        let cooldownActive = false;
        let cooldownTime = 60;

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData();
            formData.append('username', usernameInput.value);
            formData.append('password', passwordInput.value);

            fetch('verify_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "dashboard.php";
                } else {
                    loginAttempts++;
                    if (loginAttempts === 4) {
                        // Activate cooldown
                        cooldownActive = true;
                        cooldownTimer.style.display = 'block';
                        usernameInput.disabled = true;
                        passwordInput.disabled = true;
                        document.querySelector('input[type="submit"]').disabled = true;
                        const cooldownInterval = setInterval(() => {
                            cooldownTime--;
                            cooldownTimerCount.textContent = cooldownTime;
                            if (cooldownTime <= 0) {
                                clearInterval(cooldownInterval);
                                cooldownActive = false;
                                loginAttempts = 0;
                                cooldownTime = 60;
                                cooldownTimer.style.display = 'none';
                                usernameInput.disabled = false;
                                passwordInput.disabled = false;
                                document.querySelector('input[type="submit"]').disabled = false;
                            }
                        }, 1000);
                    }

                    if (loginAttempts >= 1 && loginAttempts <= 3) {
                        overlay.style.display = 'flex';
                        setTimeout(() => {
                            overlay.style.display = 'none';
                        }, 3000);
                    }

                    // Clear input fields after failed attempt
                    usernameInput.value = '';
                    passwordInput.value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                overlay.textContent = 'An error occurred. Please try again.';
                overlay.style.display = 'flex';
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 3000);
            });
        });

        // Redirect to Facebook when "Forgot Password" is clicked
        forgotPass.addEventListener('click', function() {
            window.location.href = "https://www.facebook.com/pauchi.wani";
        });
    </script>
</body>
</html>
