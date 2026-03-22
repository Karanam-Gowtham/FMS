<?php
    include "connection.php";
    

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Handling Sign Up
            if (isset($_POST['signUp'])) {
                $name = $_POST['name'];
                $userid = $_POST['userid'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $conpass = $_POST['conpass'];
        
                if ($password === $conpass) {
                    // Check if userid or email already exists
                    $checkQuery = "SELECT * FROM reg_pg WHERE userid = ? OR email = ?";
                    $stmt = $conn->prepare($checkQuery);
                    $stmt->bind_param("ss", $userid, $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
        
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        if ($row['userid'] === $userid) {
                            echo "<script>alert('This userid is already used. Please try another..');</script>";
                        } elseif ($row['email'] === $email) {
                            echo "<script>alert('This email is already used. Please try another...');</script>";
                        }
                    } else {
                        // Insert into database if userid and email are unique
                        $stmt = $conn->prepare("INSERT INTO reg_pg (name, userid, email, password) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssss", $name, $userid, $email, $password);
        
                        if ($stmt->execute() === TRUE) {
                            echo "<script>alert('Registration successful!');</script>";
                        } else {
                            echo "Error: " . $stmt->error;
                        }
                    }
                    $stmt->close();
                } else {
                    echo "<script>alert('Passwords do not match!');</script>";
                }
            }
        
        
         
        

        // Handling Sign In
        if (isset($_POST['signIn'])) {
            $userid = $_POST['userid'];
            $password = $_POST['password'];

            $stmt = $conn->prepare("SELECT * FROM reg_pg WHERE userid = ? AND password = ?");
            $stmt->bind_param("ss", $userid, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $login_stmt = $conn->prepare("INSERT INTO login_pg (userid, password) VALUES (?, ?)");
                $login_stmt->bind_param("ss", $userid, $password);

                if ($login_stmt->execute() === TRUE) {
                    $_SESSION['username'] = $userid;
                    
                    header("Location: acd_year.php");
                    exit();
                } else {
                    echo "Error: " . $login_stmt->error;
                }
                $login_stmt->close();
            } else {
                echo "<script>alert('Wrong User ID or password!');</script>";
            }
            $stmt->close();
        }
        }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <title>FILE HUB</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="" method="POST">
                <h1 id="hav_signup">Sign Up</h1>
                <input type="text" name="name" placeholder="Name" id="name" required />
                <input type="text" name="userid" placeholder="User ID" id="id_signup" required />
                <input type="email" name="email" placeholder="Email" id="mail" required />
                <input type="password" name="password" placeholder="Password" id="pass_signup" required />
                <input type="password" name="conpass" placeholder="Confirm Password" id="conpass" required />
                <button type="submit" name="signUp">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="" method="POST">
                <h1 id="hav_signin">Faculty<br>Sign In</h1>
                <input type="text" name="userid" placeholder="User ID" id="id_signin" required />
                <input type="password" name="password" placeholder="Password" id="pass_signin" required />
                <button type="submit" name="signIn">Log In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1 id="vit_left">sign In</h1>
                    <p>Effortlessly upload and download files with secure, user-friendly simplicity.</p><br><br>
                    <p style="color: rgb(0, 68, 255)">Already have an account?</p>
                    <button class="press" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h2 id="vit_right">Faculty<br>Sign Up</h1>
                    <p>Effortlessly upload and download files with secure, user-friendly simplicity.</p><br><br>
                    <p style="color: rgb(0, 68, 255)">Don't have an account?</p>
                    <button class="press" id="signUp" style="color: rgb(57, 49, 5)">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        const signUpButton = document.getElementById("signUp");
        const signInButton = document.getElementById("signIn");
        const container = document.getElementById("container");

        signUpButton.addEventListener("click", () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener("click", () => {
            container.classList.remove("right-panel-active");
        });
    </script>
    <!-- My Uploads Button -->
    <button class="admin-login-btn" onclick="location.href='admin/admins.php'">Admin Login</button>
</body>
</html>

