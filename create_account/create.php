<?php
include "../database/database.php"; 

session_start();

// if (isset($_SESSION['userID']) && !isset($_SESSION['admin'])) {
//     header("Location: /bookingapp/page_not_found.php");
// }

$selectedRole = "";
$errorMsg = "";
$success = "";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$allowedRoles = ['admin', 'tenant', 'owner'];

$selectedRole = isset($_GET['role']) ? $_GET['role'] : 'tenant';

// Get role
if (!in_array($selectedRole, $allowedRoles)) {
    header("Location: /bookingapp/page_not_found.html");
}

// if (!isset($_SESSION['userID']) && $selectedRole !== 'tenant') {
//     header("Location: /bookingapp/page_not_found.php");
// }


// Create an account into the database
if (isset($_POST['submit-account'])) {
    // Fetch and sanitize input
    $selectedRole = mysqli_real_escape_string($conn, $_POST['role'] ?? '');
    $emailAddress = mysqli_real_escape_string($conn, $_POST['email'] ?? '');

    $firstName = mysqli_real_escape_string($conn, $_POST['first-name'] ?? '');
    $middleName = mysqli_real_escape_string($conn, $_POST['middle-name'] ?? '');
    $lastName = mysqli_real_escape_string($conn, $_POST['last-name'] ?? '');
    $extName = mysqli_real_escape_string($conn, $_POST['ext-name'] ?? '');
    $gender = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');
    $contact = mysqli_real_escape_string($conn, $_POST['contact'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    $universityID =  mysqli_real_escape_string($conn, $_POST['university-id'] ?? '');
        

 
    try {
        // Validation
        if (empty($selectedRole) || empty($emailAddress) || empty($firstName) || empty($lastName) || empty($username) || empty($password) || empty($confirmPassword)) {
            $errorMsg = "All fields are required.";
            throw new Exception($errorMsg);
        }

        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = "Invalid email address.";
            throw new Exception($errorMsg);
        }

        if (!preg_match('/^09\d{2}\s?\d{3}\s?\d{4}$/', $contact)) {
            $errorMsg = "Invalid contact number. Format: 09xx xxx xxxx.";
            throw new Exception($errorMsg);
        }

        if (strlen($password) < 8) {
            $errorMsg = "Password must be at least 8 characters.";
            throw new Exception($errorMsg);
        }

        if ($password !== $confirmPassword) {
            $errorMsg = "Passwords do not match.";
            throw new Exception($errorMsg);
        }

        if ($selectedRole === 'tenant' && !preg_match('/^\d{7}$|^\d{9}$/', $universityID ?? '')) {
            $errorMsg = "University ID must be 7 or 9 digits.";
            throw new Exception($errorMsg);
        }

        
        // echo "Role: $selectedRole<br>";
        // echo "Full name: $firstName $middleName $lastName $extName<br>";
        // echo "Gender: $gender<br>";
        // echo "Contact: $contact<br>";
        // echo "Address: $address<br>";
        // echo "Username: $username<br>";
        // echo "Password: $password<br>";
        // echo "Confirm password: $confirmPassword<br>";
        // echo "Email: $emailAddress<br>";

        // if ($selectedRole === 'tenant') {
        //     echo "University ID: $universityID"; 
        // }

        $hashedPassword = md5($password);

        // Check existence
        if (personExists($conn, $firstName, $middleName, $lastName, $extName, $gender)) {
            $errorMsg = "Person already exists. Please login.";
            throw new Exception($errorMsg);
        }

        if (userAccountExists($conn, $emailAddress, $username)) {
            $errorMsg = "Username or email already exists.";
            throw new Exception($errorMsg);
        }

        if ($selectedRole === 'tenant' && universityIDExists($conn, $universityID)) {
            $errorMsg = "University ID already exists.";
            throw new Exception($errorMsg);
        }

        // Add person, socials and user account
        if (!addPerson($conn, $firstName, $middleName, $lastName, $extName, $gender, $contact, $address)) {
            $errorMsg = "Failed to add person.";
            throw new Exception($errorMsg);
        }
        $personID = mysqli_insert_id($conn);

        if (!addSocials($conn, $personID)) {
            $errorMsg = "Failed to add socials";
            throw new Exception($errorMsg);
        }

        if (!addUserAccount($conn, $personID, $emailAddress, $username, $hashedPassword, $selectedRole)) {
            $errorMsg = "Failed to create user account.";
            throw new Exception($errorMsg);
        }
        $userID = mysqli_insert_id($conn);

        // Role-specific operations
        if ($selectedRole === 'tenant' && !addTenant($conn, $userID, $universityID)) {
            $errorMsg = "Failed to add tenant.";
            throw new Exception($errorMsg);
        }
        if ($selectedRole === 'owner' && !addOwner($conn, $userID)) {
            $errorMsg = "Failed to add owner.";
            throw new Exception($errorMsg);
        }
        if ($selectedRole === 'admin' && !addAdmin($conn, $userID)) {
            $errorMsg = "Failed to add admin.";
            throw new Exception($errorMsg);
        }

        if ($selectedRole === 'tenant') {
            $_SESSION['userID'] = $userID;
            header("Location: /bookingapp/index.php");
        } else {
            header("Location: /bookingapp/admin/accounts.php?tab=$selectedRole");
        }

    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
    }
}



// if (isset($_POST['submit-account'])) {
//     $errorMsg = "Account submitted.";
//     // header('Location: index.php');
//     return $errorMsg;
// }

function personExists($conn, $firstName, $middleName, $lastName, $extName, $gender) {
    $bool = false;
    $sql = "SELECT * FROM person WHERE FirstName = '$firstName' AND MiddleName = '$middleName' AND LastName = '$lastName' AND ExtName = '$lastName' AND Gender = '$gender'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}

function userSocialsExist($conn, $personID) {
    $sql = "SELECT * FROM user_socials WHERE PersonID = $personID";
    $result = mysqli_query($conn, $sql);

    return mysqli_num_rows($result) > 0;
}


function userAccountExists($conn, $emailAddress, $username) {
    $acctSql = "SELECT * FROM user_account WHERE EmailAddress = '$emailAddress' OR Username = '$username'";
    $result = mysqli_query($conn, $acctSql);

    return mysqli_num_rows($result) > 0;
}

function universityIDExists($conn, $universityID) {
    $roleSql = "SELECT * FROM tenant WHERE UniversityID = $universityID";
    $result = mysqli_query($conn, $roleSql);

    return mysqli_num_rows($result) > 0;
}

function userTenantExists($conn, $userID) {
    $roleSql = "SELECT * FROM tenant WHERE UserID = $userID";
    $result = mysqli_query($conn, $roleSql);

    return mysqli_num_rows($result) > 0;
}

function userOwnerExists($conn, $userID) {
    $roleSql = "SELECT * FROM establishment_owner WHERE UserID = $userID";
    $result = mysqli_query($conn, $roleSql);

    return mysqli_num_rows($result) > 0;
}

function userAdminExists($conn, $userID) {
    $roleSql = "SELECT * FROM admin WHERE UserID = $userID";
    $result = mysqli_query($conn, $roleSql);

    return mysqli_num_rows($result) > 0;
}

function addPerson($conn, $firstName, $middleName, $lastName, $extName, $gender, $contactNumber, $homeAddress) {
    
    $sql = "INSERT INTO person (FirstName, MiddleName, LastName, ExtName, Gender, ContactNumber, HomeAddress) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $errorMsg = "Prepared statement error in creating the user's personal profile. " . mysqli_error($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "sssssss", $firstName, $middleName, $lastName, $extName, $gender, $contactNumber, $homeAddress);
    
    if (!mysqli_stmt_execute($stmt)) {
        $errorMsg = "Statement execution error in creating the user's personal profile." . mysqli_stmt_error($stmt);
        return false;
    } 

    return true;
}

function addSocials($conn, $personID) {
    // Add person to user_socials
    $personSql = "INSERT INTO user_socials (PersonID) VALUES (?)";
    $personStmt = mysqli_prepare($conn, $personSql);

    if (!$personStmt) {
        $errorMsg = "Prepared statement error in adding person to user_socials. " . mysqli_error($conn);
        return false;
    }

    mysqli_stmt_bind_param($personStmt, "i", $personID);

    if (!mysqli_stmt_execute($personStmt)) {
        $errorMsg = "Statement execution error in adding person to user_socials. " . mysqli_stmt_error($personStmt);
        return false;
    }

    return true;
}

function addUserAccount($conn, $personID, $emailAddress, $username, $password, $role) {
    
    $acctSql = "INSERT INTO user_account (PersonID, EmailAddress, Username, Password, Role) VALUES (?, ?, ?, ?, ?)";
    $acctStmt = mysqli_prepare($conn, $acctSql);

    if (!$acctStmt) {
        $errorMsg = "Prepared statement error in creating the user account. " . mysqli_error($conn);
        return false;
    }

    mysqli_stmt_bind_param($acctStmt, "issss", $personID, $emailAddress, $username, $password, $role);

    if (!mysqli_stmt_execute($acctStmt)) {
        throw new Exception("Statement execution error in creating the user account. " . mysqli_stmt_error($acctStmt));
        return false;
    }

    return true;
}

function addTenant($conn, $userID, $universityID) {
    $roleSql = "INSERT INTO tenant (UserID, UniversityID) VALUES (?, ?)";
    $roleStmt = mysqli_prepare($conn, $roleSql);
    
    if (!$roleStmt) {
        $errorMsg = "Prepared statement error in adding the user as a tenant. " . mysqli_error($conn);
        return false;
    }

    mysqli_stmt_bind_param($roleStmt, "is", $userID, $universityID);

    if (!mysqli_stmt_execute($roleStmt)) {
        $errorMsg = "Statement execution error in adding the user as a tenant. " . mysqli_stmt_error($roleStmt);
        return false;
    }
    return true;
}

function addOwner($conn, $userID) {
    $roleSql = "INSERT INTO establishment_owner (UserID) VALUES (?)";
    $roleStmt = mysqli_prepare($conn, $roleSql);
    
    if (!$roleStmt) {
        $errorMsg = "Prepared statement error in adding the user as an owner. " . mysqli_error($conn);
        return false;
    }

    mysqli_stmt_bind_param($roleStmt, "i", $userID);

    if (!mysqli_stmt_execute($roleStmt)) {
        $errorMsg = "Statement execution error in adding the user as an owner. " . mysqli_stmt_error($roleStmt);
        return false;
    }

    return true;
}

function addAdmin($conn, $userID) {
    $roleSql = "INSERT INTO admin (UserID) VALUES (?)";
    $roleStmt = mysqli_prepare($conn, $roleSql);
    
    if (!$roleStmt) {
        $errorMsg = "Prepared statement error in adding the user as an admin. " . mysqli_error($conn);
        return false;
    }

    mysqli_stmt_bind_param($roleStmt, "i", $userID);

    if (!mysqli_stmt_execute($roleStmt)) {
        $errorMsg = "Statement execution error in adding the user as an admin. " . mysqli_stmt_error($roleStmt);
        return false;
    }

    return true;
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
            align-items: center;
        }

        .error-msg {
            color: red;
            font-style: italic;
            font-size: 12px;
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
            transition: border-color 0.3s;
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
        <form method="post" id="create-account-form" enctype="multipart/form-data">

            <input type="hidden" name="role" id="role" value="<?php echo $selectedRole; ?>">
            
            <div class="step">
                <h2>
                    <?php
                    if ($selectedRole === 'admin') {
                        echo 'Admin';
                    } else if ($selectedRole === 'tenant') {
                        echo 'Tenant';
                    } else if ($selectedRole === 'owner') {
                        echo 'Establishment Owner';
                    }
                    ?>
                    Information
                </h2>

                <?php if ($selectedRole === "tenant") { ?>
                    <!-- <label for="email" class="mandatory">Institutional Email:</label> -->

                    <!-- <div class="form-inline">
                        <input type="text" name="email-prefix" class="w3-input w3-half" id="email-prefix" placeholder="Enter email" value="<?= htmlspecialchars($_POST['email-prefix'] ?? '', ENT_QUOTES); ?>" required>

                        <select name="email-domain" class="w3-select w3-third" id="email-domain" required>
                            <option value="@gmail.com">@gmail.com</option>
                            <option value="@s.msumain.edu.ph">@s.msumain.edu.ph</option>
                            <option value="@msumain.edu.ph">@msumain.edu.ph</option>
                        </select>
                    </div>               -->
                    
                    <label for="university-id" class="mandatory">University ID Number:</label>
                    <input type="text" name="university-id" class="w3-input w3-medium" id="university-id" maxlength="9" placeholder="Enter your university ID number" value="<?= htmlspecialchars($_POST['university-id'] ?? '', ENT_QUOTES); ?>" required>

                <?php } ?>

                <label for="email" class="mandatory">Email Address:</label>
                <input type="email" name="email" id="email" class="w3-input" placeholder="Enter your email address" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>

                <!-- Common fields for all roles -->
                <label for="full-name" class="mandatory">Full name:</label>
                <div class="form-inline">
                    <input type="text" name="first-name" id="first-name" class="w3-input" placeholder="First name" value="<?= htmlspecialchars($_POST['first-name'] ?? '', ENT_QUOTES); ?>" required>
                    <input type="text" name="middle-name" id="middle-name" class="w3-input" placeholder="Middle name (optional)" value="<?= htmlspecialchars($_POST['middle-name'] ?? '', ENT_QUOTES); ?>">
                    <input type="text" name="last-name" id="last-name" class="w3-input" placeholder="Last name" value="<?= htmlspecialchars($_POST['last-name'] ?? '', ENT_QUOTES); ?>" required>
                    <select name="ext-name" id="ext-name" class="w3-select">
                        <option value=""  <?php if (!isset($_POST['extName']) || $_POST['extName'] === '') { echo 'selected'; } ?> disabled>Ext. (optional)</option>
                        <option value="Jr." <?php if (isset($_POST['extName']) && $_POST['extName'] === 'Jr.') { echo 'selected'; } ?>>Jr.</option>
                        <option value="Sr." <?php if (isset($_POST['extName']) && $_POST['extName'] === 'Sr.') { echo 'selected'; } ?>>Sr.</option>
                        <option value="II" <?php if (isset($_POST['extName']) && $_POST['extName'] === 'II') { echo 'selected'; } ?>>II</option>
                        <option value="III" <?php if (isset($_POST['extName']) && $_POST['extName'] === 'III') { echo 'selected'; } ?>>III</option>
                        <option value="IV" <?php if (isset($_POST['extName']) && $_POST['extName'] === 'IV') { echo 'selected'; } ?>>IV</option>
                        <option value="V <?php if (isset($_POST['extName']) && $_POST['extName'] === 'V') { echo 'selected'; } ?>>">V</option>
                        <option value="VI" <?php if (isset($_POST['extName']) && $_POST['extName'] === 'VI') { echo 'selected'; } ?>>VI</option>
                    </select>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="gender" class="mandatory">Gender:</label>
                        <select name="gender" id="gender" class="w3-select" required>
                            <option value="" selected disabled>Select...</option>
                            <option value="Male" <?php if (isset($_POST['gender']) && $_POST['gender'] === 'Male') { echo 'selected'; } ?>>Male</option>
                            <option value="Female"  <?php if (isset($_POST['gender']) && $_POST['gender'] === 'Female') { echo 'selected'; } ?>>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact" class="mandatory">Contact number:</label>
                        <input type="tel" maxlength="15" name="contact" id="contact" class="w3-input" placeholder="09xx xxx xxxx" value="<?= htmlspecialchars($_POST['contact'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                </div>
            
                <div class="form-group">
                    <label for="address" class="mandatory">Home address:</label>
                    <input type="text" name="address" id="address" class="w3-input" placeholder="Enter where do you live" value="<?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES); ?>" required>
                </div>

                <div class="form-group">
                    <label for="username" class="mandatory">Account username:</label>
                    <input type="text" name="username" id="username" class="w3-input" placeholder="Enter a username" value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?>" required>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="password" class="mandatory">Password:</label>
                        <input type="password" name="password" id="password" class="w3-input" placeholder="Enter a password." value="<?= htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password" class="mandatory">Confirm password:</label>
                        <div class="form-inline" style="align-items: center; gap: 10px;">
                            <input type="password" name="confirm-password" id="confirm-password" class="w3-input" placeholder="Re-enter your password." value="<?= htmlspecialchars($_POST['confirm-password'] ?? '', ENT_QUOTES); ?>" required>
                            <a class="w3-tiny w3-button toggle-password" title="Click to show password"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" class="prev w3-left" onclick="history.back()">Go Back</button>
                    <div class="error-container" style="text-align: center;">
                        <p class="error-msg" id="error-msg"></p>
                        <p class="error-msg"><?php echo $errorMsg; ?></p>
                    </div>
                    <button type="submit" name="submit-account" id="submit-account" class="next w3-right">Submit</button>
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

        var errorMsg = document.getElementById('error-msg');
        var submitButton = document.getElementById('submit-account');
        var role = '<?php echo $_GET['role']; ?>';

        const firstName = document.getElementById('first-name');
        const lastName = document.getElementById('last-name');
        const gender = document.getElementById('gender');
        const contact = document.getElementById('contact');
        const address = document.getElementById('address');
        const username = document.getElementById('username');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm-password');

        let idNumber = "";
        let email =  document.getElementById("email");

        let idNumberOK = false;
        let emailOK = false;
        let contactOK = false;
        let passwordOK = false;
        let passwordsMatch = false;

        document.addEventListener("DOMContentLoaded", function() {
            validateFields(role);
        });

        function validateFields(role) {

            if (role === 'tenant') {
                // Tenant fields
                idNumber = document.getElementById("university-id");

                idNumber.addEventListener("input", function() {
                    if (!validateIDNumber(idNumber.value)) {
                        displayError(idNumber, "You entered invalid ID number.");
                    } else {
                        clearErrors([idNumber]);
                        errorMsg.textContent = '';
                        idNumberOK = true;
                    }
                });
            }


            email.addEventListener('input', function() {
                if (!validateEmail(email.value, role)) {
                    if (role === 'tenant') {
                        displayError(email, "You entered an invalid email address. Be sure the email is under the MSU Main Campus domain.");
                    } else {
                        displayError(email, "You entered an invalid email address.");
                    }
                } else {
                    clearErrors([email]);
                    errorMsg.textContent = '';
                    emailOK = true;
                }
            });

            contact.addEventListener('input', function() {
                if (!validateContact(contact.value)) {
                    displayError(contact, "You entered an invalid contact number.");
                } else {
                    clearErrors([contact]);
                    errorMsg.textContent = '';
                    contactOK = true;
                }
            });

            password.addEventListener('input', function() {
                if (validatePassword(password)) {
                    clearErrors([password]);
                    errorMsg.textContent = '';
                    passwordOK = true;
                }
            });
            
            confirmPassword.addEventListener('input', function() {
                if (validatePasswordMatch(password, confirmPassword)) {
                    clearErrors([confirmPassword]);
                    errorMsg.textContent = '';
                    passwordsMatch = true;
                }
            });
        }

        function validateIDNumber(idNumber) {
            const re = /^[0-9]{7}$|^[0-9]{9}$/;
            return re.test(idNumber);
        }

        function validateEmail(email, role) {
            var pattern = /^[^\s@]+@[^\s@]+\.x[^\s@]+$/;

            if (role === 'tenant') {
                $pattern = '/^[a-zA-Z0-9._%+-]+@(s\.)?msumain\.edu\.ph$/';
            }

            return pattern.test(email);
        }

        function validateContact(contact) {
            let result = false;
            
            // Get the raw input value (digits only)
            let inputValue = event.target.value.replace(/[^\d\s]/g, '');

            // Ensure the first two digits start with "09"
            if (inputValue.length >= 2) {
                inputValue = '09' + inputValue.substring(2);
            }

            // Check if the input value is not empty
            if (inputValue.length > 0) {
                // Check if the input is at least 11 digits
                if (inputValue.length >= 11) {
                    // Format the contact number with spaces
                    inputValue = inputValue.replace(/^(\d{4})(\d{3})(\d{4})$/, '$1 $2 $3');

                    // Update the input field with the formatted value
                    event.target.value = inputValue;

                    result = true;
                } else {
                    // Display error message if the input is less than 11 digits
                    errorMsg.textContent = "Contact number must be at least 11 digits.";
                }
            } else {
                // Display error message if the input is empty
                errorMsg.textContent = "Contact number is required.";
            }
        

            return result;
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

        // // Submit Account
        // async function submitAccount(role) {
        //     if (!validateFields(role)) {
        //         console.log("Validation failed for " + role + " account creation.");
        //         showToast('circle-xmark', 'Please fill all required fields correctly.', 'error');

        //         return false;
        //     }

        //     var instEmailPrefix = null;
        //     var instEmailDomain = null;
        //     var idNumber = null;

        //     var email = null;

        //    if (role === 'tenant') {
        //         // Tenant fields
        //         instEmailPrefix = document.getElementById("email-prefix");
        //         instEmailDomain = document.getElementById("email-domain");
        //         idNumber = document.getElementById("university-id");

        //         email = instEmailPrefix + instEmailDomain;
        //    } else {
        //         // Owner fields
        //         email = document.getElementById("email");
        //    }

        //     showToast('hourglass-half', 'Signing you up...', 'information');
        //     const formData = new FormData(document.getElementById('create-account-form'));
        //     formData.append('ajax', 'submit_account');
        //     formData.append('role', role);

        //     console.log("Sending data", formData.toString());

        //     try {
        //         const response = await fetch('create.php', {
        //             method: 'POST',
        //             body: formData
        //         });

        //         const result = await response.json();

        //         if (result.success) {
        //             showToast('check', result.message, 'success');
        //             showToast('circle-check', 'Successfully signed up!', 'success');
        //             setTimeout(() => {
        //                 window.location.href = `otp_form.php?email=${email}`;
        //             }, 3000);
        //         } else {
        //             showToast('circle-xmark', result.message, 'success');
        //         }
        //     } catch (error) {
        //         showToast('circle-xmark', 'An error occurred. Please try again later.', 'error');
        //         console.error('Error: ', error);
        //     }

        //     return false;
        // }

        function displayError(input, message) {
            input.classList.add("error-border");
            // showToast("circle-xmark", message, "error");
            errorMsg.textContent = message;
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

        // Toggle password functionality
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordField = document.getElementById('password');
                const confirmPasswordField = document.getElementById('confirm-password');

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