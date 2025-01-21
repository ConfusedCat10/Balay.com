<?php
include "../database/database.php"; 

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//required files
require '../assets/phpmailer/src/Exception.php';
require '../assets/phpmailer/src/PHPMailer.php';
require '../assets/phpmailer/src/SMTP.php';

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
    <title>Create an account</title>

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

        .step {
            display: none;
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
        <form action="" method="post" id="create-account-form">
            <!-- Step 1: Choose your role -->
            <div class="step" id="step-1">
                <h2>Select Your Role</h2>
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

            <!-- Step 2: Tenant Form -->
            <div class="step" id="step-2-tenant">
                <h2>Tenant Information</h2>

                <label for="email" class="mandatory">Institutional Email:</label>

                <div class="form-inline">
                    <input type="text" name="email-prefix" class="w3-input w3-half" id="email-prefix" placeholder="Enter email" required>

                    <select name="email-domain" class="w3-select w3-third" id="email-domain">
                        <option value="@gmail.com">@gmail.com</option>
                        <option value="@s.msumain.edu.ph">@s.msumain.edu.ph</option>
                        <option value="@msumain.edu.ph">@msumain.edu.ph</option>
                    </select>
                </div>
                
                <label for="university-id" class="mandatory">University ID Number:</label>
                <input type="text" name="university-id" class="w3-input w3-medium" id="university-id" maxlength="9" placeholder="Enter your university ID number">

                <!-- Common fields for both tenant and owner -->
                <label for="full-name-tenant" class="mandatory">Full name:</label>
                <div class="form-inline">
                    <input type="text" name="first-name-tenant" id="first-name-tenant" class="w3-input" placeholder="First name">
                    <input type="text" name="middle-name-tenant" id="middle-name-tenant" class="w3-input" placeholder="Middle name (optional)">
                    <input type="text" name="last-name-tenant" id="last-name-tenant" class="w3-input" placeholder="Last name">
                    <select name="ext-name-tenant" id="ext-name-tenant" class="w3-select">
                        <option value="" selected disabled>Ext. (optional)</option>
                        <option value="">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                    </select>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="tenant-gender" class="mandatory">Gender:</label>
                        <select name="tenant-gender" id="tenant-gender" class="w3-select">
                            <option value="" selected disabled>Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tenant-contact" class="mandatory">Contact number:</label>
                        <input type="tel" maxlength="11" name="tenant-contact" id="tenant-contact" class="w3-input" placeholder="09xx xxx xxxx">
                    </div>
                </div>
            
                <div class="form-group">
                    <label for="tenant-address" class="mandatory">Home address:</label>
                    <input type="text" name="tenant-address" id="tenant-address" class="w3-input" placeholder="Enter where do you live">
                </div>

                <div class="form-group">
                    <label for="tenant-username" class="mandatory">Account username:</label>
                    <input type="text" name="tenant-username" id="tenant-username" class="w3-input" placeholder="Enter a username">
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="tenant-password" class="mandatory">Password:</label>
                        <input type="password" name="tenant-password" id="tenant-password" class="w3-input" placeholder="Enter a password.">
                    </div>

                    <div class="form-group">
                        <label for="tenant-confirm-password" class="mandatory">Confirm password:</label>
                        <div class="form-inline" style="align-items: center; gap: 10px;">
                            <input type="password" name="tenant-confirm-password" id="tenant-confirm-password" class="w3-input" placeholder="Re-enter your password.">
                            <a class="w3-tiny w3-button toggle-password" title="Click to show password"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" id="prev-tenant" class="prev w3-left">Previous</button>
                    <button type="button" id="submit-tenant" class="next w3-right" onclick="submitTenantAccount()">Submit</button>
                </div>
            </div>

            <!-- Step 2 - Owner Form -->
            <div class="step" id="step-2-owner">
                <h2>Establishment Owner Information</h2>

                <label for="email-owner" class="mandatory">Email Address:</label>
                <input type="email" name="email-owner" id="email-owner" class="w3-input" placeholder="Enter your email address">

                <!-- Common fields for both tenant and owner -->
                <!-- Common fields for both tenant and owner -->
                <label for="full-name-owner" class="mandatory">Full name:</label>
                <div class="form-inline">
                    <input type="text" name="first-name-owner" id="first-name-owner" class="w3-input" placeholder="First name">
                    <input type="text" name="middle-name-owner" id="middle-name-owner" class="w3-input" placeholder="Middle name (optional)">
                    <input type="text" name="last-name-owner" id="last-name-owner" class="w3-input" placeholder="Last name">
                    <select name="ext-name-owner" id="ext-name-owner" class="w3-select">
                        <option value="" selected disabled>Ext. (optional)</option>
                        <option value="">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                    </select>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="owner-gender" class="mandatory">Gender:</label>
                        <select name="owner-gender" id="owner-gender" class="w3-select">
                            <option value="" selected disabled>Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="owner-contact" class="mandatory">Contact number:</label>
                        <input type="tel" maxlength="11" name="owner-contact" id="owner-contact" class="w3-input" placeholder="09xx xxx xxxx">
                    </div>
                </div>
            
                <div class="form-group">
                    <label for="owner-address" class="mandatory">Home address:</label>
                    <input type="text" name="owner-address" id="owner-address" class="w3-input" placeholder="Enter where do you live">
                </div>

                <div class="form-group">
                    <label for="owner-username" class="mandatory">Account username:</label>
                    <input type="text" name="owner-username" id="owner-username" class="w3-input" placeholder="Enter a username">
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="owner-password" class="mandatory">Password:</label>
                        <input type="password" name="owner-password" id="owner-password" class="w3-input" placeholder="Enter a password.">
                    </div>

                    <div class="form-group">
                        <label for="owner-confirm-password" class="mandatory">Confirm password:</label>
                        <div class="form-inline" style="align-items: center; gap: 10px;">
                            <input type="password" name="owner-confirm-password" id="owner-confirm-password" class="w3-input" placeholder="Re-enter your password.">
                            <a class="w3-tiny w3-button toggle-password" title="Click to show password"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </div>
                </div>


                <div class="btn-group">
                <button type="button" id="prev-owner" class="prev w3-left">Previous</button>
                <button type="button" id="submit-owner" class="next w3-right" onclick="submitOwnerAccount()">Submit</button>
                </div>
            </div>

            <!-- Step 3 - OTP Verification -->
            <div class="step" id="step-3">
                <h2>Verify Your Email</h2>
                <p>Enter the 6-character OTP sent to your email:</p>
                <div id="otp-container" style="margin-top: 10px">
                    <input type="text" maxlength="1" id="otp-1" class="otp-input" oninput="moveToNextInput(this, 'otp-2')">
                    <input type="text" maxlength="1" id="otp-2" class="otp-input" oninput="moveToNextInput(this, 'otp-3')">
                    <input type="text" maxlength="1" id="otp-3" class="otp-input" oninput="moveToNextInput(this, 'otp-4')">
                    <input type="text" maxlength="1" id="otp-4" class="otp-input" oninput="moveToNextInput(this, 'otp-5')">
                    <input type="text" maxlength="1" id="otp-5" class="otp-input" oninput="moveToNextInput(this, 'otp-6')">
                    <input type="text" maxlength="1" id="otp-6" class="otp-input" oninput="validateOTP()">
                </div>
                <span id="otp-error" style="color: red;"></span>

                <div class="btn-group">
                    <button id="submit-otp-btn" type="button" onclick="verifyOTP()">Verify now</button>
                    <button type="button" onclick="skipVerification()">Verify later</button>
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

        // Global variables to track the current Step
        let currentStep = 1;
        let selectedRole = '';

        // Show step 1 by default
        document.getElementById('step-1').style.display = 'block';

        // Event listeners for Next/Previous buttons
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('click', function() {
                selectedRole = this.value;
                document.getElementById('step-1').style.display = 'none';
                document.getElementById(`step-2-${selectedRole}`).style.display = 'block';
            });
        });

        document.getElementById('prev-tenant').addEventListener('click', function() {
            showStep('step-1', 'step-2-tenant');
        });

        document.getElementById('prev-owner').addEventListener('click', function() {
            showStep('step-1', 'step-2-owner');
        });
        

        // Helper functions
        function showStep(nextStep, currentStep) {
            document.getElementById(currentStep).style.display = 'none';
            document.getElementById(nextStep).style.display = 'block';
        }

        // OTP validation
        function moveToNextInput(currentInput, nextInputId) {
            if (currentInput.value.length === currentInput.maxLength) {
                document.getElementById(nextInputId).focus();
            }
            validateOTP();
        }

        function validateFields(role) {
            // Common fields
            const firstName = document.getElementById(`first-name-${role}`);
            const lastName = document.getElementById(`last-name-${role}`);
            const gender = document.getElementById(`${role}-gender`);
            const contact = document.getElementById(`${role}-contact`);
            const address = document.getElementById(`${role}-address`);
            const username = document.getElementById(`${role}-username`);
            const password = document.getElementById(`${role}-password`);
            const confirmPassword = document.getElementById(`${role}-confirm-password`);

            // Tenant fields
            const instEmailPrefix = document.getElementById("email-prefix");
            const instEmailDomain = document.getElementById("email-domain");
            const idNumber = document.getElementById("university-id");

            // Owner fields
            const ownerEmail = document.getElementById("email-owner");

            clearErrors([firstName, lastName, gender, contact, address, username, password, confirmPassword]);

            if (!firstName.value || !lastName.value || !gender.value || !contact.value || !address.value || !username.value || !password.value || !confirmPassword.value) {
                showToast("circle-xmark", "Please complete the necessary fields.", "error");
                highlightErrors([firstName, lastName, gender, contact, address, username, password, confirmPassword]);
                return false;
            }


            if (role === 'tenant') {
                clearErrors([instEmailPrefix, instEmailDomain, idNumber]);

                if (!instEmailPrefix.value || !instEmailDomain.value || !idNumber.value) {
                    showToast("cicle-xmark", "Please complete the necessary fields.", "error");
                    highlightErrors([instEmailPrefix, instEmailDomain, idNumber]);
                    
                    return false;
                }

                if (!validateIDNumber(idNumber.value)) {
                    displayError(idNumber, "You entered invalid ID number.");
                    return false;
                }
            } else if (role === 'owner') {
                clearErrors([ownerEmail]);

                if (!ownerEmail.value) {
                    displayError(ownerEmail, "You missed to enter an email address.");
                    return false;
                }

                if (!validateEmail(ownerEmail.value)) {
                    displayError(ownerEmail, "You entered an invalid email address.");
                    return false;
                }
            }

            if (!validateContact(contact.value)) {
                displayError(contact, "You entered an invalid contact number.");
                return false;
            }

            if (!validatePassword(password)) {
                return false;
            }

            if (!validatePasswordMatch(password, confirmPassword)) {
                return false;
            }

            return true;
        }

        function validateIDNumber(idNumber) {
            const re = /^[0-9]{7}$|^[0-9]{9}$/;
            return re.test(idNumber);
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function validateContact(contact) {
            const re = /^09\d{9}$/;
            return re.test(contact);
        }

        function validatePassword(password) {
            // check the length of characters
            if (password.value.length < 8) {
                displayError(password, "Password must be at least 8 characters.");
                return false;
            }
            return true;
        }

        function validatePasswordMatch(password, confirmPassword) {
            // Add more password rules
            if (password.value.trim() !== confirmPassword.value.trim()) {
                displayError(confirmPassword, "Passwords mismatch");
                return false;
            }
            return true;
        }

        // Submit Tenant Account
        function submitTenantAccount() {
            if (validateFields('tenant')) {
                const formData = new FormData(document.getElementById('create-account-form'));
                formData.append('role', 'tenant');

                fetch('create_account.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Server response: ", data);

                    if (data.success) {
                        showToast("circle-check", data.message, 'success');
                    } else {
                        showToast("circle-xmark", data.message, 'error');
                    }
                })
                .catch(error => {
                    console.log("Error during fetch: ", error);
                    showToast("circle-xmark", error, "error");
                    showToast("circle-xmark", "An error occurred. Please try again.", "error");
                });
            } else {
                console.log("Validation failed for tenant account creation.");
                showToast('circle-xmark', 'Failed to create a tenant account', 'error');
            }
        }

        // Submit Owner Account
        function submitOwnerAccount() {
            if (validateFields('owner')) {
                const formData = new FormData(document.getElementById('create-account-form'));
                formData.append('role', 'owner');

                fetch('create_account.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => { 
                    console.log("Server response: ", data);

                    if (data.success) {
                        showToast('circle-check', data.message, 'success');
                    } else {
                        showToast('circle-xmark', data.message, 'error');
                    }
                })
                .catch(error => {                    
                    showToast("circle-xmark", error, "error");
                    showToast("circle-xmark", "An error occurred. Please try again.", "error");
                });
            } else {
                console.log("Validation failed for owner account creation.");
                showToast('circle-xmark', 'Failed to create an establishment owner account.', 'error');
            }
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

        // Verify OTP
        function verifyOTP() {
            const otp = validateOTP();

            fetch("verify_otp.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `otp=${encodeURIComponent(otp)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast("circle-check", data.message, 'success');
                    window.location.href = "index.php";
                } else {
                    showToast("circle-xmark", data.message, 'error');
                }
            })
            .catch(error => {
                console.error("Error: ", error);
                showToast("circle-xmark", "An error occurred. Please try again.", "error");
            });
        } 

        function skipVerification() {
            showToast("envelope", 'You can verify your e-mail later in the account settings.', 'information');
            window.location.href = "index.php";
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
            }, 5000);
        }
    </script>

</body>
</html>