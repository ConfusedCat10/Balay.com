<?php
include "../database/database.php"; 

session_start();

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//required files
require '../assets/phpmailer/src/Exception.php';
require '../assets/phpmailer/src/PHPMailer.php';
require '../assets/phpmailer/src/SMTP.php';

// SMS getaway API credentials
define('API_URL', 'https://api.semaphore.co/api/v4/messages');
define('API_KEY', '98013686e69499f093283d46219d1a5f');
define('SENDER_ID', 'BALAY.com');
define('COUNTRY_CODE', 63); // 63 = Philippines

$errorMsg = "";
$successMsg = "";
$purpose = "";
$thisUser = null;
$thisPersonID = null;
$otpCode = null;

if (isset($_GET['user']) && $_GET['user'] !== '') {
    $thisUserID = $_GET['user'];
    $purpose = $_GET['purpose'];

    // Get user ID
    $sql = "SELECT u.EmailAddress, p.ContactNumber, CONCAT(p.FirstName, ' ', p.LastName, ' ', p.ExtName) AS FullName, u.Username, u.PersonID FROM user_account u INNER JOIN person p ON p.PersonID = u.PersonID WHERE u.UserID = $thisUserID";
    $result = mysqli_query($conn, $sql);

    $errorMsg = mysqli_error($conn);

    if (mysqli_num_rows($result) > 0) {
        $thisUserAccount = mysqli_fetch_assoc($result);

        $thisUser = $thisUserAccount;
        $thisPersonID = $thisUserAccount['PersonID'];
        $thisEmailAddress = $thisUserAccount['EmailAddress'];
        $thisFullName = $thisUserAccount['FullName'];
        $thisContactNumber = $thisUserAccount['ContactNumber'];

        $otpCode = getOTPCode($conn, $thisUserID, $purpose);
        // clearOTPs($conn, $thisUserID);

        if (!empty($otpCode)) {
            if ($purpose === 'email') {
                sendOtpEmail($thisFullName, $thisEmailAddress, $otpCode);
            } else if ($purpose === 'contact') {
                $thisContactNumber = str_replace(' ', '', $thisContactNumber); // Remove spaces
                $formattedContactNumber = formatNumber($thisContactNumber);
                // sendOtpMobile($formattedContactNumber, $otpCode);
            } else if ($purpose === 'password-recovery') { 
                sendOtpEmail($thisFullName, $thisEmailAddress, $otpCode);
            } else {
                $errorMsg = "Failed to send an OTP";
            }
        } else {
            $errorMsg = "Failed to get an OTP code.";
        }
    } else {
        header("Location: /bookingapp/page_not_found.html");
    }
} else {
    header("Location: /bookingapp/page_not_found.html");
}

echo $errorMsg;

function clearOTPs($conn, $userID) {
    $otpSql = "DELETE FROM otp_verifications WHERE UserID = ?";
    $otpStmt = mysqli_prepare($conn, $otpSql);

    if (!$otpStmt) {
        $errorMsg = "Prepared statement error in deleting all OTP code. " . mysqli_error($conn);
    }

    mysqli_stmt_bind_param($otpStmt, 'i', $userID);

    if (!mysqli_stmt_execute($otpStmt)) {
        $errorMsg = "Statement execution error in deleting all OTP code. " . mysqli_stmt_error($otpStmt);
    }
}

// Set OTP in database
function getOTPCode($conn, $userID, $purpose) {
    $otpCode = generateOTP();
    // $errorMsg = $otpCode;
    $expiresAt = date('Y-m-d H:i:s', strtotime('+3 minutes'));

    $otpSql = "INSERT INTO otp_verifications (UserID, Purpose, OTP_Code, ExpiresAt) VALUES (?, ?, ?, ?)";
    $otpStmt = mysqli_prepare($conn, $otpSql);

    if (!$otpStmt) {
        $errorMsg = "Prepared statement error in inserting an OTP code. " . mysqli_error($conn);
    }

    mysqli_stmt_bind_param($otpStmt, "isss", $userID, $purpose, $otpCode, $expiresAt);

    if (mysqli_stmt_execute($otpStmt)) {
        return $otpCode;
    } else {
        $errorMsg = "Failed to get an OTP.";
        return null;
    }
}

// OTP generation and verification
function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Send OTP to user's email
function sendOtpEmail($fullName, $email, $otpCode) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "noormacalandong265@gmail.com";
        $mail->Password = "bgjnkklncdmbjvga";
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;

        // Recipeints
        $mail->setFrom("dkboystudios@gmail.com", "DK Creative Studios");
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Verification Code";
        $mail->Body = "
        <html>
        <head>
            <title>OTP Verification</title>
        </head>
        <body>
            <p>Dear $fullName,</p>
            <p>Thank you for signing up. To verify your email address, please use the following <strong>One-Time PIN (OTP)</strong> code:</p>
            <h2>$otpCode</h2>
            <p>This code is valid for 3 minutes. Please do not share this code with anyone.</p>
            <p>Thank you, Balay.com</p>
        </body>
        </html>
        "; // email message

        // Success sent message alert
        $mail->send();

        $successMsg = "The OTP has been sent to $email";
                
    } catch (Exception $e) {
        $errorMsg = "Failed to send OTP email. Error: {$mail->ErrorInfo}";
    }
}

// Send OTP to user's mobile number
function sendOtpMobile($contact, $otp) {
    // Request parameters array
    $requestParams = array(
        'api_key' => API_KEY,
        'sender_id' => SENDER_ID,
        'recepient_no' => COUNTRY_CODE.$contact,
        'message' => "Dear User, your OTP for verification is $otp, which will expire after 10 minutes. From BALAY.com",
    );

    // Append parameters to API URL
    $apiURL = API_URL.'?';
    foreach($requestParams as $key => $value) {
        $apiURL = $key . '=' . urlencode($value).'&';
    }
    $apiURL = rtrim($apiURL, '&');

    // Send the GET request with cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $successMsg = "The OTP has been sent to mobile number $contact";

    echo $response;
    return $response;
}

// Format number to international format (from '0' to '+63')
function formatNumber($mobileNumber) {
    // Check if the number starts with '0'
    if (substr($mobileNumber, 0, 1) === "0") {
        return "+" . COUNTRY_CODE . substr($mobileNumber, 1);
    }
    return $mobileNumber; // Already in international format
}

// Verify OTP
if (isset($_POST['verifyOTP'])) {
    $otp1 = mysqli_real_escape_string($conn, $_POST['otp-1']);
    $otp2 = mysqli_real_escape_string($conn, $_POST['otp-2']);
    $otp3 = mysqli_real_escape_string($conn, $_POST['otp-3']);
    $otp4 = mysqli_real_escape_string($conn, $_POST['otp-4']);
    $otp5 = mysqli_real_escape_string($conn, $_POST['otp-5']);
    $otp6 = mysqli_real_escape_string($conn, $_POST['otp-6']);

    $otp = $otp1 . $otp2 . $otp3 . $otp4 . $otp5 . $otp6;
    // echo "OTP Entered: $otp | OTP sent: $otpCode";
    $userID = mysqli_real_escape_string($conn, $thisUserID);
    $purpose = mysqli_real_escape_string($conn, $purpose);

    try {
        $otpSql = "SELECT * FROM otp_verifications WHERE OTP_Code = '$otp'";
        $result = mysqli_query($conn, $otpSql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Update user account
            if ($purpose === 'email') {
                $otpSql = "UPDATE user_account SET IsEmailVerified = 1 WHERE UserID = ?";
            } else if ($purpose === 'contact') {
                $otpSql = "UPDATE person SET IsContactVerified = 1 WHERE PersonID = ?";
            }
            $otpStmt = mysqli_prepare($conn, $otpSql);

            if (!$otpStmt) {
                throw new Exception("Prepared statement error in changing the $purpose verification status of the user account. " . mysqli_error($conn));
            }

            if ($purpose === 'email') {
                mysqli_stmt_bind_param($otpStmt, "i", $userID);
            } else if ($purpose === 'contact') {
                mysqli_stmt_bind_param($otpStmt, "i", $thisPersonID);
            }

            if (!mysqli_stmt_execute($otpStmt)) {
                throw new Exception("Statement execution error in changing the $purpose verification status of the user account. " . mysqli_stmt_error($otpStmt));
            }

            if ($purpose === 'email' || $purpose === 'password-recovery') {
                $successMsg = "Your email address has been verified.";
            } else {
                $successMsg = "Your contact number has been verified.";
            }
            
            sleep(3);
            $thisUsername = $thisUser['Username'];
            // clearOTPs($conn, $userID);

            

            if (isset($_SESSION['userID'])) {
                header("Location: /bookingapp/user/profile.php?id=$thisUsername");
            } else {
                if ($purpose === 'password-recovery') {
                    header("Location: /bookingapp/pages/update_password.php?user=$thisUsername");
                } else {
                    header("Location: /bookingapp/login.php");
                }
            }

        } else {
            $errorMsg = "Invalid or expired OTP!";

            if ($purpose === 'email' || $purpose === 'password-recovery') {
                $successMsg = "Another OTP is sent to your email address.";
            } else {
                $successMsg = "Another OTP is sent to your contact number.";
            }
            // sleep(3);
            // $errorMsg = "";
        }
        
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
    }
    // exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php if ($purpose !== 'password-recovery') { ?>
        <title>Verify <?php if ($purpose === 'email') { echo 'email address'; } else if ($purpose === 'contact') { echo 'mobile number'; } ?></title>
    <?php } else { ?>
        <title>OTP for Password Recovery</title>
    <?php } ?>

    <?php
    include "../php/head_tag.php";
    ?>


    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .step-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex: 1;
            margin: 20px;
        }

        form {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
        }

        .btn-group {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }
        
        .form-group {
            margin-right: 10px;
        }


        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12px;
        }
        
        .step h2 {
            margin-bottom: 10px;
        }

        button {
            padding: 10px;
            margin: 5px;
            border: none;
            background-color: gold;
            color: maroon;
            border-radius: 5px;
            cursor: pointer;
        }

        button[disabled] {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .error {
            color: red;
        }

        .error-border {
            border: 2px solid red;
        }

        .error-text {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* Role Selection Styling */
        .role-selection {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .role-option {
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: 2px solid transparent;
            padding: 10px;
            border-radius: 8px;
            transition: border-color: 0.3s;
            width: 100%;
        }

        .role-option:hover {
            border-color: gold;
        }

        .role-option input {
            display: none;
        }

        .role-option .role-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .role-option img {
            width: 50px;
            height: 50px;
        }

        .role-option span {
            margin-top: 10px;
            font-size: 1rem;
        }

        .role-option input:checked + .role-content {
            border-color: maroon;
        }

        .highlight-role {
            border-color: maroon;
        }

        #otp-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .otp-input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 20px;
            margin: 0 5px;
        }

        .form-inline {
            display: flex;
        }

        .form-inline input, .form-inline select {
            margin-right: 5px;
        }

        .mandatory:after {
            content: "*";
            color: red;
        }

        

        @media (max-width: 600px) {
            form {
                width: 90%;
                max-width: 500px;
            }

            .form-inline {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php
        include "../php/header.php";
        ?>
    </header>

    <!-- Content -->
     <div class="step-container">
        <form method="post">
            <!-- Step 3 - OTP Verification -->
            <div class="step" id="step-3" style="display: flex; justify-content: center; flex-direction: column; gap: 10px;">
                <?php if ($purpose !== 'password-recovery') { ?>
                    <h2>Verify Your <?php if ($purpose === 'email') { echo 'Email Address'; } else { echo 'Mobile Number'; } ?></h2>
                <?php } else { ?>
                    <h2>Recover your password</h2>
                <?php } ?>
                <p>Enter the 6-character OTP sent to your <?php if ($purpose === 'email' || $purpose === 'password-recovery') { echo 'email address'; } else { echo 'mobile number'; } ?>:</p>
                <p style="color: red; text-align: center; margin: auto;"><?php echo $errorMsg; ?></p>
                <p style="color: green; text-align: center; margin: auto;"><?php echo $successMsg; ?></p>
                <div id="otp-container" style="margin-top: 10px">
                    <input type="text" maxlength="1" id="otp-1" name="otp-1" class="otp-input" oninput="moveToNextInput(this, 'otp-2')">
                    <input type="text" maxlength="1" id="otp-2" name="otp-2" class="otp-input" oninput="moveToNextInput(this, 'otp-3')">
                    <input type="text" maxlength="1" id="otp-3" name="otp-3" class="otp-input" oninput="moveToNextInput(this, 'otp-4')">
                    <input type="text" maxlength="1" id="otp-4" name="otp-4" class="otp-input" oninput="moveToNextInput(this, 'otp-5')">
                    <input type="text" maxlength="1" id="otp-5" name="otp-5" class="otp-input" oninput="moveToNextInput(this, 'otp-6')">
                    <input type="text" maxlength="1" id="otp-6" name="otp-6" class="otp-input" oninput="validateOTP()">
                </div>
                <span style="margin: auto;" id="otpTimer">OTP valid for...</span>
                <span style="margin: auto;" id="resendLink">Click <a style="color: blue; cursor: pointer; text-decoration: underline" onclick="location.reload()">here</a> to resend.</span>
                <span id="otp-error" style="color: red; text-align: center; margin: auto;"></span>

                <div class="btn-group">
                    <?php if ($purpose !== 'password-recovery') { ?>
                        <button type="button" onclick="skipVerification()">Verify later</button>
                        <button id="submit-otp-btn" name="verifyOTP" type="submit">Verify now</button>
                    <?php } else { ?>
                        <button id="submit-otp-btn" name="verifyOTP" type="submit">Recover password</button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>

    
    <div id="toastBox"></div>

    <!-- Footer -->
    <footer>
        <div class="footer-bar" style="font-size: 12px;">
            Copyright &copy; <span id="year"></span> <br>College of Information and Computing Sciences<br> Mindanao State University - Main Campus.
        </div>
    </footer>


    <script>

        
        const otpTimer = document.getElementById('otpTimer');
        const resendLink = document.getElementById('resendLink');

        resendLink.style.display = 'none'; // Hide by default

        // OTP Countdown Timer
        function startOtpTimer(seconds) {
            let timeLeft = seconds;
            const timer = setInterval(() => {
                let minutes = Math.floor(timeLeft / 60);
                let secondsLeft = timeLeft % 60;
                otpTimer.textContent = `OTP valid for ${minutes}:${secondsLeft < 10 ? '0' + secondsLeft : secondsLeft}`;
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(timer);
                    otpTimer.innerText = "OTP expired.";
                    otpTimer.style.color = 'red';
                    resendLink.style.display = 'block';
                    document.getElementById('submit-otp-btn').disabled = true;
                }

            }, 1000);
        }

        startOtpTimer(300);

        // OTP validation
        function moveToNextInput(currentInput, nextInputId) {
            if (currentInput.value.length === currentInput.maxLength) {
                document.getElementById(nextInputId).focus();
            }
            validateOTP();
        }


        function displayError(input, message) {
            input.classList.add("error-border");
            showToast("circle-xmark", message, "error");
            const errorMessage = document.createElement("small");
            errorMessage.classList.add("error-text");
            errorMessage.textContent = message;
            input.parentNode.appendChild(errorMessage);
        }

        function clearErrors(fields) {
            fields.forEach(field => {
                field.classList.remove("error-border");
                const errorText = field.parentNode.querySelector(".error-text");
                if (errorText) {
                    errorText.remove();
                }
            });
        }

        function highlightErrors(fields) {
            fields.forEach(field => {
                if (!field.value) {
                    field.classList.add("error-border");
                }
            })
        }

        function validateOTP() {
            const otp1 = document.getElementById("otp-1").value;
            const otp2 = document.getElementById("otp-2").value;
            const otp3 = document.getElementById("otp-3").value;
            const otp4 = document.getElementById("otp-4").value;
            const otp5 = document.getElementById("otp-5").value;
            const otp6 = document.getElementById("otp-6").value;

            const otp = otp1 + otp2 + otp3 + otp4 + otp5 + otp6;
            const otpRegex = /^[A-Za-z0-9]{6}$/;

            if (otp.length === 6 && otpRegex.test(otp)) {
                document.getElementById("otp-error").innerText = "";
                document.getElementById("submit-otp-btn").disabled = false;
            } else {
                document.getElementById("otp-error").innerText = "Please enter a valid 6-character OTP.";
                document.getElementById("submit-otp-btn").disabled = true;
            }

            return otp;
        }

        // Backspace functionality to move back to the previous input
        document.querySelectorAll('.otp-input').forEach(input => {
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Backspace' && this.value === '') {
                    const prevInput = this.previousElementSibling;
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.value = '';
                    }
                }
            });
        });

        function skipVerification() {
            showToast("envelope", 'You can verify your e-mail later in the account settings.', 'information');
            setTimeout(() => {
                window.location.href = "/bookingapp/user/profile.php?id=<?php echo $thisUser['Username']; ?>";
            }, 5000);
        }

        // Toggle password functionality
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordField = document.getElementById(`${selectedRole}-password`);
                const confirmPasswordField = document.getElementById(`${selectedRole}-confirm-password`);

                if (passwordField.type == 'password') {
                    passwordField.type = 'text';
                    confirmPasswordField.type = 'text';
                    this.title = "Click to hide the passwords.";
                    this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
                } else {
                    passwordField.type = 'password';
                    confirmPasswordField.type = 'password';
                    this.title = "Click to show the passwords.";
                    this.innerHTML = '<i class="fa-solid fa-eye"></i>';
                }
            });
        });



        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();

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
            }, 3000);
        }
    </script>

</body>
</html>