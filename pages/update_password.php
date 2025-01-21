<?php
include "../database/database.php";

session_start();

$error = "";
$username = "";

if (isset($_GET['user']) || $_GET['user'] !== '') {
    $username = $_GET['user'];
}

if (isset($_POST['recover-password'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $newPassword = mysqli_real_escape_string($conn, $_POST['newPassword'] ?? '');
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword'] ?? '');

    $hashedPassword = md5($newPassword);

    try {
        if (!$newPassword || !$confirmPassword) {
            $errorMsg = "Please fill in your new password!";
            throw new Exception($errorMsg);
        }

        if (strlen($newPassword) < 8) {
            $errorMsg = "Passwords must be at least 8-character longer.";
            throw new Exception($errorMsg);
        }

        if ($newPassword !== $confirmPassword) {
            $errorMsg = "Passwords mismatch!";
            throw new Exception($errorMsg);
        }

        // Check if the entered email is existing
        $sql = "UPDATE user_account SET Password = ? WHERE Username = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'ss', $hashedPassword, $user);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $sql = "SELECT * FROM user_account WHERE Username = '$user'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);

        if ($result && $rowCount > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['userID'] = $row['UserID'];
        }

        header("Location: /bookingapp/user/profile.php?id=$user");

    } catch (Exception $e) {
        $error = '<i class="fa-solid fa-circle-xmark"></i>' .$e->getMessage();
    }
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create a new password</title>

    <?php include "../php/head_tag.php"; ?>


    <!-- JS -->
    <!-- <script defer src="../js/otp-script.js"></script> -->
    <link rel="stylesheet" href="/bookingapp/css/logged-out.css">

    <style>
        .message-field {
            width: 100%;
            font-family: Arial, sans-serif;
        }
        .submit-btn-group {
            display: flex;
            float: right;
            width: 100%;
        }
        .submit-btn-group button {
            margin: 5px;
            width: 100%;
            font-size: 12px;
        }
        .submit-btn {
            font-size: 12px;
        }
        .wizard-step {
            width: 100%;
        }

        .wizard-step p {
            font-size: 12px;
            margin-bottom: 10px;
        }

        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .body-otp {
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .recovery-container-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            justify-content: center;
        }

        .recovery-container {
            width: 100%;
            max-width: 500px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
        }

        .option-btn {
            background-color: #8A0000;
            color: white;
            padding: 10px;
            border: none;
            margin: 10px 0;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
        }

        .option-btn:hover {
            background-color: #5A0001;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .otp-inputs input {
            width: 50px;
            height: 60px;
            margin: 0 5px;
            text-align: center;
            font-size: 2rem;
            border: 2px solid #000;
            border-radius: 8px;
            outline: none;
        }

        .otp-inputs input:focus {
            border-color: #8A0000;
        }

        .message-field {
            width: 100%;
            height: 100px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 1rem;
        }

        .submit-btn {
            background-color: #5A0001;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #8A0000;
        }

        .back-btn {
            background-color: #ccc;
            color: black;
            padding: 10px;
            border: none;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
        }

        .timer {
            margin-top: 20px;
            color: #333;
            font-size: 1rem;
        }

        .resend-link {
            color: #007BFF;
            cursor: pointer;
            text-decoration: none;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .resend-link.disabled {
            pointer-events: none;
            color: grey;
        }

        .hidden {
            display: none;
        }

        .wizard-step input,
        .wizard-step select {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            border-radius: 10px;
        }

        .toggle-password {
            float: right;
            margin-top: 20px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: black;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            text-align: right;
        }

        .toggle-password i {
            font-size: 18px;
        }

    </style>

</head>
<body class="body-otp">

    <header class="header">
        <?php include "../php/header.php"; ?>
    </header>

    <!-- Content -->
    <div class="container" style="height: 100%; padding-top: 200px; padding-bottom: 200px">
        <div class="recovery-container-wrapper">
            <div class="recovery-container">
                <!-- Step 1 -->
                <form method="post">
                    <div id="step1" class="wizard-step">
                        <h2>Choose How to Recover Your Account</h2>
                        <input type="hidden" name="username" value="<?php echo $username; ?>">

                        <input type="password" name="newPassword" id="newPassword" placeholder="Enter a new password." style="" required>

                        <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm your password" required>

                        <span class="toggle-password" id="togglePassword" onclick="togglePassword()">
                            <i id="toggleIcon" title="Click to show the passwords." class="fas fa-eye slash"></i> Toggle password
                        </span>

                        <button type="submit" name="recover-password" class="option-btn" id="submitPassword" >Submit new password</button>
                        <p class="error-prompt" style="color: red; text-align: center; font-size: 15px"><?php echo $error; ?></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "../php/footer.php"; ?>
    
    <script>

      

        // Function to toggle password visibility and icon
        function togglePassword() {
            const newPasswordInput = document.getElementById("newPassword");
            const confirmPasswordInput = document.getElementById("confirmPassword");
            const toggleIcon = document.getElementById("toggleIcon");

            const togglePassword = document.getElementById("togglePassword");

            var toggleLabel = togglePassword.textContent;

            // Toggle new password visibility
            if (newPasswordInput.type == 'password') {
                newPasswordInput.type = 'text';
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.setAttribute('title', 'Click to hide the password.');
            } else {
                newPasswordInput.type = 'password';
                toggleIcon.classList.add('fa-eye');
                toggleIcon.classList.remove('fa-eye-slash');                
                toggleIcon.setAttribute('title', 'Click to show the password.');
            }

            // Toggle confirm password visibility
            if (confirmPasswordInput.type == 'password') {
                confirmPasswordInput.type = 'text';
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.setAttribute('title', 'Click to hide the password.');
            } else {
                confirmPasswordInput.type = 'password';
                toggleIcon.classList.add('fa-eye');
                toggleIcon.classList.remove('fa-eye-slash');                
                toggleIcon.setAttribute('title', 'Click to show the password.');

            }
        }
        
  
    </script>
</body>
</html>