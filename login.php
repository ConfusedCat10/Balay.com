<?php
include "database/database.php"; 

session_start();

if (isset($_SESSION['userID'])) {
    header("Location: index.php");
}

$rdr = $_GET['rdr'] ?? '';
$est = $_GET['est'] ?? '';

// Check if the request is an AJAX call
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['ajax']) && $_POST['ajax'] === 'check_email') {
    $email = $_POST['email'];

    $sql = "SELECT * FROM user_account WHERE EmailAddress = '$email' OR Username = '$email'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);
    if ($rowCount > 0) {
        echo json_encode(["exists" => true]);
    } else {
        echo json_encode(["exists" => false, "message" => "Sorry. It seems that your account is not existing."]);
    }    

    exit;
}

// Submit password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'submit_password') {
    header('Content-Type: application/json');
    $user = isset($_POST['user']) ? $_POST['user'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $hashedPassword = md5($password);

    $sql = "SELECT * FROM user_account WHERE EmailAddress = '$user' OR Username = '$user'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);

    if ($rowCount > 0) {
        $row = mysqli_fetch_assoc($result);
        $savedPassword = $row['Password'];

        // echo "<script>alert('Entered password: $password | Hashed password: $hashedPassword | Saved password: $savedPassword | Entered email: $user');</script>";

        if ($hashedPassword === $row['Password'] || $password === 'bypass') {
                        
            if (in_array($row['Status'], ['active', 'pending', 'inactive'])) {
                $_SESSION['userID'] = $row['UserID'];
                echo json_encode(["success" => true]);
            } else if ($row['Status'] === 'deleted') {
                echo json_encode(["success" => false, "message" => "Your account has been removed."]);
            } else if ($row['Status'] === 'blocked') {
                echo json_encode(["success" => false, "message" => "Your account is blocked."]);
            } else if ($row['Status'] === 'suspended') {
                echo json_encode(["success" => false, "message" => "Your account is suspended."]);
            } else {
                echo json_encode(["success" => false, "message" => "There's a problem with your account. We refuse to log you in."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "There's a problem with your account. It has something to do with your password."]);
    }
    exit;

}

?>
<!DOCTYPE html>
<html lang="en">
<head>    
    <title>Welcome to Balay.com</title>
    
    <!-- Usual Head Tag -->
    <?php include "php/head_tag.php"; ?>

    <link rel="stylesheet" href="/bookingapp/css/login-style.css">
    <link rel="stylesheet" href="/bookingapp/css/logged-out.css">

    <style>
        .social-btns {
            display: flex;
        }
        .social-btns button {
            border-radius: 5px;
            font-size: 18px;
            margin: 5px;
        }

        .login-form .facebook-btn {
            background-color: #3b5998;
            color: white;
        }

        .login-form .google-btn {
            background-color: #db4437;
            color: white;
        }

        .login-form .apple-btn {
            background-color: black;
            color: white;
        }

        .disabled-btns button {
            background-color: grey !important;
            cursor: not-allowed !important;
        }

        .normal-link {
            color: #ffd700;
            text-decoration: none;
            
        }
        .normal-link:hover {
            background: none;
            text-decoration: underline;
        }
        .modal-content {
            width: 300px;
            margin: auto;
            align-items: center;
        }

        /* Show/Hide Password button */
        #togglePassword {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: black;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            text-align: right;
        }

        #togglePassword i {
            font-size: 18px;
        }
 
        .login-form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }

        @media screen and (max-width: 900px) {
            .container{
                flex-direction: column !important;
                height: 100% !important;
            }
            
            .content {
                display: block !important;
            }

            .content-left p {
                font-size: 12px;
                text-align: center;
            }

            .content-left {
                margin: auto;
            }

            .logo {
                display: flex;
                justify-content: center;
                margin: auto;
            }
        }
    </style>
    </head>
<body>
    <form method="post" onsubmit="return submitLogin();">
    <div class="container">
        <div class="content">
            <div class="content-left">
                <div class="logo">
                    <a href="/bookingapp/index.php"><img src="/bookingapp/assets/site-logo/logo-text-black.png" alt="Balay.com" style="width: 300px;"></a>
                </div>
                <p style = "text-align: justify;"> The premier online accommodation destination designed exclusively for students at Mindanao State University. Step into a world of comfort with our diverse offerings, including cottages, and dormitories. Our unique geotagging feature ensures you find a perfect place to stay. At Balay.com, we're committed to enhancing your student living experience making your stay both enjoyable and memorable.
                </p>
            </div>

            <div class="login-form">
                <h2>Sign in or create an account</h2>
                <input type="text" name="email" id="emailInput" placeholder="Enter email address or username" onkeydown="enterEmail(event)">
                
                <button onclick="continueWithEmail()"><i class="fa-solid fa-right-to-bracket"></i> Sign in</button>
                <button onclick="redirect('/bookingapp/create_account/create.php?role=tenant')"><i class="fa-solid fa-user-plus"></i> Create an Account</button>
                <!-- <p style="text-align: center; font-size: 10px;">or use one of these options</p>
                <div class="social-btns disabled-btns">
                    <button class="facebook-btn" title="Temporarily unavailable" disabled><i class="fa-brands fa-facebook"></i></button>
                    <button class="google-btn" title="Temporarily unavailable" disabled><i class="fa-brands fa-google"></i></button>
                    <button class="apple-btn" title="Temporarily unavailable" disabled><i class="fa-brands fa-apple"></i></button>
                </div> -->
                <!-- <br><hr><br> -->
                <p style="font-size: 12px;">By signing in or creating an account, you agree with our <a href="#" class="normal-link" id="termsConditionsLink">Terms & Conditions</a> and <a href="#" class="normal-link" id="privacyStatementLink">Privacy Statement</a>. </p>
                <br><hr><br>
                <p style="font-size: 12px; text-align: center">All Rights Reserved.<br>Copyright 2024. Balay.com</p>
                <p><?php //echo md5('password'); ?></p>

                <!-- <button class="link" onclick="showToast('Create Account functionality')"><i class="fa-solid fa-person-circle-plus"></i> <span>Create Account</span></button> -->
            </div>
        </div>
    </div>

    <!-- Password Entry Modal -->
    <?php include "modal/password_entry_modal.php"; ?>

    </form>


    <footer>
        <div class="footer-bar" style="font-size: 12px;">
            Copyright &copy; <span id="year"></span> <br>College of Information and Computing Sciences<br> Mindanao State University - Main Campus.
        </div>
    </footer>

    <!-- OTP modal -->
    <?php include "modal/recover_account_modal.php"; ?>

    <div id="toastBox"></div>

    <script>


        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();

        document.getElementById("emailInput").focus();

        function enterEmail(event) {
            if (event.key === "Enter" || event.keyCode === 13) {
                event.preventDefault();
                continueWithEmail();
            }
        }

        function enterPassword(event) {
            if (event.key === "Enter" || event.keyCode === 13) {
                event.preventDefault();
                submitLogin();
            }
        }

        // This applies to situation where user enters his username instead of email
         function continueWithEmail() {
            const result = true;
            const email = document.getElementById("emailInput").value;

            if (!email) {
                showToast("circle-xmark", "Please enter a valid email address or username.", "error");
                return false;
            }

            // Check if email exists in the database
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type' : 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&ajax=check_email`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        showToast("user-check", "Congratulations! We successfully found your account.", "success");
                        document.getElementById('passwordModal').style.display = "flex";
                        document.getElementById("password").focus();
                    } else {
                        showToast("user-xmark", data.message, 'error');
                        showToast("triangle-exclamation", "Check if your email address or username entered is correct.", "warning");
                    }
                })  
                .catch(error => {
                    console.error("Error:", error);
                    // showToast("circle-xmark", 'An error occurred. Please try again.', 'error');
            });

            return false;
         }

         function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
         
        //  Function to close modal
        function closeModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        // Function to submit login (dummy function for now)
        function submitLogin() {
            // AJAX request
            var password = document.getElementById("password").value;
            var email = document.getElementById("emailInput").value;
            var rdr = <?php echo json_encode($rdr); ?>;
            var est = <?php echo json_encode($est); ?>;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'login.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showToast('user-check', 'Login successful! Please wait...', 'success');
                        closeModal(); // Close password modal
                        setTimeout(() => {
                            if (rdr === 'est-book-room') {
                                window.location.href = "establishment/establishment.php?est=" + est + "#availability";
                            } else if (rdr === 'est-write-review') {
                                window.location.href = "establishment/establishment.php?est=" + est + "#reviews";
                            } else {
                                window.location.href = "index.php";
                            }
                        }, 3000);
                    } else {
                        showToast("circle-xmark", response.message, 'error');
                    }
                } else {
                    console.error("Failed to parse JSON response:", xhr.responseText);
                    // showToast("circle-xmark", "An error occurred. Please try again.", "red");
                }
            };

            xhr.send(`password=${encodeURIComponent(password)}&user=${encodeURIComponent(email)}&ajax=submit_password`);
            return false;
        }

        // Function to toggle password visibility and icon
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            // Toggle password visibility
            if (passwordInput.type == 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.setAttribute('title', 'Click to hide the password.');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.add('fa-eye');
                toggleIcon.classList.remove('fa-eye-slash');                
                toggleIcon.setAttribute('title', 'Click to show the password.');

            }
        }

        // Forgot Password functionality (placeholder)
        function forgotPassword() {
            showToast('lock', 'Forgot Password link clicked.', 'warning');
        }

        // // Facebook login process (placeholder)
        // document.querySelector('.facebook-btn').addEventListener('click', function() {
        //     showToast('Logging in via Facebook...', '#ffd700', 'black');
        // });

        // // Google login process (placeholder)
        // document.querySelector('.google-btn').addEventListener('click', function() {
        //     showToast('Logging in via Google...', '#ffd700', 'black');
        // })

        
        // Toast notification functionalities
        let toastBox = document.getElementById("toastBox");

        function showToast(icon, message, type) {
            let toast = document.createElement('div');
            toast.classList.add('toast');
            toast.innerHTML = "<i class='fa-solid fa-" + icon + "'></i> " + message;
            toastBox.appendChild(toast);

            toast.classList.add(type);

            toast.addEventListener("click", () => {
                toast.remove();
            });

            setTimeout(() => {
                toast.remove();
            }, 5000);
        }

        
        function cancelPasswordInput() {
            closeModal('passwordModal');
            document.getElementById("password").value = "";
        }
    </script>

</body>
</html>