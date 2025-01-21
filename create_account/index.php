<?php
include "../database/database.php"; 

session_start();

if (isset($_SESSION['userID'])) {
    header("Location: index.php");
}

// Create an account into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedRole = $_POST['role'];

    if ($selectedRole == 'tenant') {
        // Tenant Forms
        $emailPrefix = $_POST['email-prefix'];
        $emailDomain = $_POST['email-domain'];
        $emailAddress = $emailPrefix . $emailDomain;

        $universityID = $_POST['university-id'];
        $firstName = $_POST['first-name-tenant'];
        $middleName = $_POST['middle-name-tenant'];
        $lastName = $_POST['last-name-tenant'];
        $extName = $_POST['ext-name-tenant'];

        $gender = $_POST['tenant-gender'];
        $contact = $_POST['tenant-contact'];
        $address = $_POST['tenant-address'];

        $username = $_POST['tenant-username'];
        $password = $_POST['tenant-password'];

    } else if ($selectedRole == 'owner') {
        // Establishment Owner Forms
        $emailAddress = $_POST['email-owner'];

        $firstName = $_POST['first-name-owner'];
        $middleName = $_POST['middle-name-owner'];
        $lastName = $_POST['last-name-owner'];
        $extName = $_POST['ext-name-owner'];

        $gender = $_POST['owner-gender'];
        $contact = $_POST['owner-contact'];
        $address = $_POST['owner-address'];

        $username = $_POST['owner-username'];
        $password = $_POST['owner-password'];
    }

    // Sanitize fields
    $selectedRole = mysqli_real_escape_string($conn, $selectedRole);
    $emailAddress = mysqli_real_escape_string($conn, $emailAddress);
    
    $firstName = mysqli_real_escape_string($conn, $firstName);
    $middleName = mysqli_real_escape_string($conn, $middleName);
    $lastName = mysqli_real_escape_string($conn, $lastName);
    $extName = mysqli_real_escape_string($conn, $extName);

    // $_SESSION['fullName'] = $firstName . ' ' . $lastName;

    $gender = mysqli_real_escape_string($conn, $gender);
    $contact = mysqli_real_escape_string($conn, $contact);
    $address = mysqli_real_escape_string($conn, $address);


    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $hashedPassword = md5($password);

    echo "You made it here!";

    // Create account
    try {

        // Check if the person already existed
        $sql = "SELECT * FROM person WHERE FirstName = ? AND MiddleName = ? AND LastName = ? AND ExtName = ? AND Gender = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            throw new Exception("Prepared statement error in finding if the person already exists. " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $middleName, $lastName, $extName, $gender);

        if (mysqli_stmt_execute($stmt)) {
            throw new Exception("The person you are creating an account for already exists in the system. Please login instead.");
            return;
        } else {
            throw new Exception("Statement execution error in finding if the person already exists." . mysqli_stmt_error($stmt));
        }

        // Create the personal profile
        $sql = "INSERT INTO person (FirstName, MiddleName, LastName, ExtName, Gender, ContactNumber, HomeAddress) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error in creating the user's personal profile. " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "sssssss", $firstName, $middleName, $lastName, $extName, $gender, $contact, $address);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error in creating the user's personal profile." . mysqli_stmt_error($stmt));
        } else {
            $personNo = mysqli_insert_id($conn);

            // Add person to user_socials
            $personSql = "INSERT INTO user_socials (PersonID) VALUES (?)";
            $personStmt = mysqli_prepare($conn, $personSql);

            if (!$personStmt) {
                throw new Exception("Prepared statement error in adding person to user_socials. " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($personStmt, "i", $personNo);

            if (!mysqli_stmt_execute($personStmt)) {
                throw new Exception("Statement execution error in adding person to user_socials. " . mysqli_stmt_error($personStmt));
            }

            // Check if email address or username already exists
            $acctSql = "SELECT * FROM user_account WHERE EmailAddress = ? OR Username = ?";
            $acctStmt = mysqli_prepare($conn, $acctSql);

            if (!$acctStmt) {
                throw new Exception("Prepared statement error in finding if user's email address or username already exists. " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($acctStmt, "ss", $emailAddress, $username);

            if (mysqli_stmt_execute($acctStmt)) {
                throw new Exception("Failed to create an account. Either the email address or the username exists.");
            } else {
                throw new Exception("Statement execution error in finding if user's email address or username already exists. " . mysqli_stmt_error($acctStmt));
            }

            // Add user account
            $acctSql = "INSERT INTO user_account (PersonID, EmailAddress, Username, Password, Role) VALUES (?, ?, ?, ?, ?)";
            $acctStmt = mysqli_prepare($conn, $acctSql);

            if (!$acctStmt) {
                throw new Exception("Prepared statement error in creating the user account. " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($acctStmt, "issss", $personNo, $emailAddress, $username, $hashedPassword, $selectedRole);

            if (!mysqli_stmt_execute($acctStmt)) {
                throw new Exception("Statement execution error in creating the user account. " . mysqli_stmt_error($acctStmt));
            } else {
                $userID = mysqli_insert_id($conn);

                // Add depending on the selected role

                $roleTable = null;

                if ($selectedRole === 'tenant') {
                    // Check if university ID number already exists
                    $roleSql = "SELECT * FROM tenant WHERE UniversityID = ?";
                    $roleStmt = mysqli_prepare($conn, $roleSql);

                    if (!$roleStmt) {
                        throw new Exception("Prepared statement error in finding if user tenant's university ID number already exists. " . mysqli_error($conn));
                    }

                    mysqli_stmt_bind_param($roleStmt, "s", $universityID);

                    if (mysqli_stmt_execute($roleStmt)) {
                        throw new Exception("Failed to create a tenant account. University ID number already exists in the system.");
                    } else {
                        throw new Exception("Statement execution error in finding if user tenant's university ID number already exists. " . mysqli_stmt_error($roleStmt));
                    }

                    // Check if user already exists as a tenant
                    $roleSql = "SELECT * FROM tenant WHERE UserID = ?";
                    $roleStmt = mysqli_prepare($conn, $roleSql);

                    if (!$roleStmt) {
                        throw new Exception("Prepared statement error in finding if user exists as a tenant. " . mysqli_error($conn));
                    }

                    mysqli_stmt_bind_param($roleStmt, "s", $userID);

                    if (mysqli_stmt_execute($roleStmt)) {
                        throw new Exception("Failed to create a tenant account. User already exists as a tenant.");
                    } else {
                        throw new Exception("Statement execution error in finding if user exists as a tenant. " . mysqli_stmt_error($roleStmt));
                    }

                    $roleSql = "INSERT INTO tenant (UserID, UniversityID) VALUES (?, ?)";
                    $roleStmt = mysqli_prepare($conn, $roleSql);
                    
                    
                    if (!$roleStmt) {
                        throw new Exception("Prepared statement error in adding the user as a tenant. " . mysqli_error($conn));
                    }

                    mysqli_stmt_bind_param($roleStmt, "is", $userID, $universityID);

                    if (!mysqli_stmt_execute($roleStmt)) {
                        throw new Exception("Statement execution error in adding the user as a tenant. " . mysqli_stmt_error($roleStmt));
                    } else {
                        $otpCode = generateOTP();
                        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                        $purpose = 'email';

                        $optSql = "INSERT INTO otp_verifications (UserID, Purpose, OTP_Code, ExpiresAt) VALUES (?, ?, ?, ?)";
                        $otpStmt = mysqli_prepare($conn, $optSql);

                        if (!$optStmt) {
                            throw new Exception("Prepared statement error in inserting an OTP code. " . mysqli_error($conn));
                        }

                        mysqli_stmt_bind_param($otpStmt, "isss", $userID, $purpose, $otpCode, $expiresAt);

                        if (mysqli_stmt_execute($otpStmt)) {
                            sendOtpEmail($emailAddress, $otpCode);
                        } else {
                            echo json_encode(["success" => false, "message" => "Failed to create account."]);
                        }
                    }

                } else if ($selectedRole === 'owner') {
                    // Check if user exists as an owner
                    $roleSql = "SELECT * FROM owner WHERE UserID = ?";
                    $roleStmt = mysqli_prepare($conn, $roleSql);

                    if (!$roleStmt) {
                        throw new Exception("Prepared statement error in finding if user already exists as an owner. " . mysqli_error($conn));
                    }

                    mysqli_stmt_bind_param($roleStmt, "s", $universityID);

                    if (mysqli_stmt_execute($roleStmt)) {
                        throw new Exception("Failed to create an owner account. User already exists as an owner in the system.");
                    } else {
                        throw new Exception("Statement execution error in finding if user already exists as an owner. " . mysqli_stmt_error($roleStmt));
                    }

                    $roleSql = "INSERT INTO establishment_owner (UserID) VALUES (?)";
                    $roleStmt = mysqli_prepare($conn, $roleSql);

                    if (!$roleSql) {
                        throw new Exception("Prepared statement error in adding the user as an establishment owner. " . mysqli_error($conn));
                    }

                    mysqli_stmt_bind_param($roleStmt, "i", $userID);

                    if (!mysqli_stmt_execute($roleStmt)) {
                        throw new Exception("Statement execution error in adding the user as an establishment owner. " . mysqli_stmt_error($roleStmt));
                    } else {
                        $otpCode = generateOTP();
                        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                        $purpose = 'email';

                        $optSql = "INSERT INTO otp_verifications (UserID, Purpose, OTP_Code, ExpiresAt) VALUES (?, ?, ?, ?)";
                        $otpStmt = mysqli_prepare($conn, $optSql);

                        if (!$optStmt) {
                            throw new Exception("Prepared statement error in inserting an OTP code. " . mysqli_error($conn));
                        }

                        mysqli_stmt_bind_param($otpStmt, "isss", $userID, $purpose, $otpCode, $expiresAt);

                        if (mysqli_stmt_execute($otpStmt)) {
                            // Email OTP logic here
                            sendOtpEmail($emailAddress, $otpCode);
                        } else {
                            echo json_encode(["success" => false, "message" => "Failed to create account."]);
                        }
                    }
                }
            }
        }


    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        echo json_encode(["success" => false, "message" => $errorMessage]);
    }

    exit;
}

// OTP generation and verification
function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Send OTP to user's email
function sendOtpEmail($email, $otpCode) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dkboystudios@gmail.com";
        $mail->Password = "bgjnkklncdmbjvga";
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;

        // Recipeints
        $mail->setFrom("dkboystudios@gmail.com", "DK Creative Studios");
        $mail->addAddress($email);
        $mail->addReplyTo("dkboystudios@gmail.com", "DK Creative Studios");


        $userFullName = $_SESSION['fullName'];

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Verification Code";
        $mail->Body = "
        <html>
        <head>
            <title>OTP Verification</title>
        </head>
        <body>
            <p>Dear $userFullName,</p>
            <p>Thank you for signing up. To verify your email address, please use the following <strong>One-Time PIN (OTP)</strong> code:</p>
            <h2>$otpCode</h2>
            <p>This code is valid for 10 minutes. Please do not share this code with anyone.</p>
            <p>Thank you, Balay.com</p>
        </body>
        </html>
        "; // email message

        // Success sent message alert
        $mail->send();
        
        echo json_encode(["success" => true, "message" => "You have successfully created an account. An OTP code has already been sent to verify your email address."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to send OTP email. Error: {$mail->ErrorInfo}"]);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select a role</title>

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
        <!-- Step 1: Choose your role -->
        <div class="step" style="width: 50%">
            <h2 style="text-align: center;">Select Your Role</h2>
            <div class="role-selection">
                <label class="role-option">
                    <input type="radio" name="role" id="radio-tenant" value="tenant">
                    <div class="role-content">
                        <img src="/bookingapp/assets/icons/tenant-icon.jpg" alt="tenant icon" class="role-icon">
                        <span>Tenant</span>
                    </div>
                </label>
                <label class="role-option">
                    <input type="radio" name="role" id="radio-owner" value="owner">
                    <div class="role-content">
                        <img src="/bookingapp/assets/icons/dormitory-icon.png" alt="dormitory icon" class="role-icon"> 
                        <span>Establishment Owner</span>
                    </div>
                </label>
            </div>
            <div class="error" id="role-error"></div>
            <!-- <button type="button" id="next-1" class="next w3-right">Next</button> -->
        </div>
    </div>

    
    <div id="toastBox"></div>

    <!-- Footer -->
    <footer>
        <div class="footer-bar" style="font-size: 12px;">
            Copyright &copy; <span id="year"></span> <br>College of Information and Computing Sciences<br> Mindanao State University - Main Campus.
        </div>
    </footer>


    <script>
        
        // Global variables to track the current Step
        let currentStep = 1;
        let selectedRole = '';

        // Event listeners for Next/Previous buttons
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('click', function() {
                selectedRole = this.value;
                window.location.href = `create.php?role=${encodeURIComponent(selectedRole)}`;
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
            }, 5000);
        }
    </script>

</body>
</html>