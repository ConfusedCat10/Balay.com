<?php
include "../database/database.php";

session_start();

$error = "";

if (isset($_POST['recover-password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');

    try {
        if (!$email) {
            $errorMsg = "Please fill up with your email!";
            throw new Exception($errorMsg);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = "Invalid email address.";
            throw new Exception($errorMsg);
        }

        // Check if the entered email is existing
        $sql = "SELECT * FROM user_account WHERE EmailAddress = '$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $username = $row['Username'];

            header("Location: /bookingapp/pages/update_password.php?user=$username");
        } else {
            $errorMsg = "The email address that you entered does not exist!";
            throw new Exception($errorMsg);
        }

    } catch (Exception $e) {
        $error = '<i class="fa-solid fa-circle-xmark"></i>' .$e->getMessage();
    }
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Account Recovery</title>

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
    </style>

</head>
<body class="body-otp">

    <!-- Header -->
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
                        <input type="email" name="email" id="emailInput" placeholder="Enter Email Address" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                        <button type="submit" name="recover-password" class="option-btn" id="viaEmailBtn" >Send via Email</button>
                        <p class="error-prompt" style="color: red; text-align: center; margin-top: 20px; font-size: 15px"><?php echo $error; ?></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "../php/footer.php"; ?>
    
    <script>

       

        var emailInput = document.getElementById('emailInput');
        var viaEmailBtn = document.getElementById('viaEmailBtn');

        viaEmailBtn.disabled = true;

        emailInput.addEventListener("input", function() {
            if (emailInput.value !== '') {
                viaEmailBtn.disabled = false;
                document.getElementById('sentToEmail').value = emailInput.value
            } else {
                viaEmailBtn.disabled = true;
            }
        });

        // Check the Internet connection
        window.addEventListener('load', function() {
            function updateStatus() {
                if (!navigator.onLine) {
                    window.location.href = "/bookingapp/no_internet_connection.php";
                }
            }

            updateStatus();

            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);

        });
        
  
    </script>
</body>
</html>