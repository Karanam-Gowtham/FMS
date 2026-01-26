<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        /* Background Image and Overlay */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('stuff/file_management.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }

        /* Black overlay for dimming effect */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.35); /* 25% opacity black */
            z-index: 1;
        }

        /* Container for buttons */
        .button-container {
            position: relative;
            z-index: 2;
            text-align: center;
            background: rgba(255, 255, 255, 0.55);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1.5s;
        }

        /* Animation for the container */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Button styling */
        .button-container button {
            width: 400px;
            padding: 15px;
            margin: 10px 0;
            font-size: 1.2em;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0px 4px 15px rgba(255, 150, 150, 0.3);
        }

        .button-container button:hover {
            background: linear-gradient(135deg, #fad0c4, #ff9a9e);
            box-shadow: 0px 4px 20px rgba(255, 150, 150, 0.5);
            transform: scale(1.05);
        }

        /* Animated glow effect on hover */
        .button-container button:focus {
            outline: none;
            box-shadow: 0px 0px 10px 5px rgba(255, 182, 193, 0.6);
        }

        /* Button for Register and Login */
        .button-container .register-btn {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
        }

        .button-container .register-btn:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
        }

        .button-container .login-btn {
            background: linear-gradient(135deg, #34e89e, #0f3443);
        }

        .button-container .login-btn:hover {
            background: linear-gradient(135deg, #0f3443, #34e89e);
        }
    </style>
</head>
<body>

<div class="overlay"></div> <!-- Black overlay for dimming effect -->

<div class="button-container">
    <button class="register-btn" onclick="location.href='reg.php'">Register</button><br>
    <button class="login-btn" onclick="location.href='admin/admins.php'">Login</button>
</div>

</body>
</html>
