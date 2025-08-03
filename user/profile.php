<?php
include "../database/database.php";

session_start();

// Determine the active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

// Handle search query
$searchQuery = $_GET['search'] ?? '';

// Pagination setup
$itemsPerPage = 10; // Number of profile cards per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$profileAccount = null;
$profileUsername = null;
$person = null;

$thisRole = null;
$isEmailVerified = null;

$personFullName = null;

$isUserProfile = false;

$thisProfilePicture = null;

$thisUserID = null;
$thisPersonID = null;

$userID = $_SESSION['userID'];


if (isset($_GET['id']) && $_GET['id'] !== "") {
    $profileUsername = $_GET['id'];

   try {
        $sql = "SELECT * FROM user_account WHERE Username = '$profileUsername'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);

        if ($rowCount > 0) {
            $profileAccount = mysqli_fetch_assoc($result);

            $thisUserID = $profileAccount['UserID'];
            $thisPersonID = $profileAccount['PersonID'];
            $thisRole = $profileAccount['Role'];
            $isEmailVerified = $profileAccount['IsEmailVerified'];

            $personSql = "SELECT * FROM person WHERE PersonID = $thisPersonID";
            $personResult = mysqli_query($conn, $personSql);
            $personRowCount = mysqli_num_rows($personResult);

            if ($personRowCount > 0) {
                $person = mysqli_fetch_assoc($personResult);

                $thisFirstName = $person['FirstName'];
                $thisMiddleName = $person['MiddleName'];
                $thisLastName = $person['LastName'];
                $thisExtName = $person['ExtName'];

                if (!empty($thisMiddleName)) {
                    $thisMiddleName = $thisMiddleName[0] . ".";
                }

                $personFullName = $thisFirstName . ' ' . $thisMiddleName . ' ' . $thisLastName . ' ' . $thisExtName;

                $thisProfilePicture = $person['ProfilePicture'];
                $thisGender = $person['Gender'];
        
                if (empty($thisProfilePicture) || $thisProfilePicture == null) {
                    $thisProfilePicture = "/bookingapp/user/$thisGender-no-face.jpg";
                } 

                $thisBirthday = $person['DateOfBirth'];

                if (empty($thisBirthday) || $thisBirthday === "0000-00-00") {
                    $thisBirthday = "";
                }
            } else {
                throw new Exception("No person found.");
            }
        } else {
            throw new Exception("No user account found.");
        }

   } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        showToast($errorMessage, "error", "circle-xmark");
   }
} else {
    header("Location: /bookingapp/index.php");
}


// Check if the profile is owned by the logged in user.
if (isset($_SESSION['userID']) && $_SESSION['userID'] === $profileAccount['UserID']) {
    $isUserProfile = true;
}

// Role information

$thisRole = $profileAccount['Role'];

if ($thisRole === "owner") {
    $sql = "SELECT * FROM establishment_owner WHERE UserID = $thisUserID";
} else {
    $sql = "SELECT * FROM $thisRole WHERE UserID = $thisUserID";
}

$result = mysqli_query($conn, $sql);
$rowCount = mysqli_num_rows($result);

$thisInstitution = "";
$thisPositionTitle = "";
$thisUniversityID = "";

if ($rowCount > 0) {
    $roleRow = mysqli_fetch_assoc($result);

    if ($thisRole === "tenant") {
        $thisTenantID = $roleRow['TenantID'];
        $thisUniversityID = $roleRow["UniversityID"];
    }

    if ($thisRole === "owner") {
        $thisOwnerID = $roleRow['OwnerID'];
    }

    if ($thisRole === "admin") {
        $thisAdminID = $roleRow['AdminID'];
    }

    if ($thisRole === "owner" || $thisRole === "admin") {
        $thisPositionTitle = $roleRow['PositionTitle'];
        $thisInstitution = $roleRow['Institution'];
    }
}

function showToast($message, $type, $icon) {
    echo "<script>";
    echo "showToast($icon, $message, $type);";
    echo "</script>";
}


$generalPromptError = "";
$generalPromptSuccess = "";

// Updating general tab
if (isset($_POST['submitGeneral'])) {
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? '');
    $emailAddress = mysqli_real_escape_string($conn, $_POST['email-address'] ?? '');


    // $thisRole = $role;

    if ($role === 'owner' || $role === 'admin') {
        $institution = mysqli_real_escape_string($conn, $_POST['institution'] ?? '');
        $positionTitle = mysqli_real_escape_string($conn, $_POST['position-title'] ?? '');
    }
    
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');

    $firstName = mysqli_real_escape_string($conn, $_POST['first-name'] ?? '');
    $middleName = mysqli_real_escape_string($conn, $_POST['middle-name'] ?? '');
    $lastName = mysqli_real_escape_string($conn, $_POST['last-name'] ?? '');
    $extName = mysqli_real_escape_string($conn, $_POST['ext-name'] ?? '');


    $oldPhoto  = $thisProfilePicture;
    $profilePicture = $_FILES['fileInput']; // The current profile picture path

    $newFilePath = "";

    try {

       // Validation and Upload Settings
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedTenantDomains = ['gmail.com', 's.msumain.edu.ph', 'msumain.edu.ph'];
        $maxFileSize = 5242880; // 5MB

        // Input Validation
        if (empty($firstName) || empty($lastName) || empty($username) || empty($emailAddress)) {
            throw new Exception('Please complete all fields.');
        }

        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }

        // Tenant Email Domain Validation
        if ($profileAccount['EmailAddress'] !== $emailAddress) {
            $emailDomain = substr(strchr($emailAddress, "@"), 1);
            if ($role === "tenant" && !in_array($emailDomain, $allowedTenantDomains)) {
                throw new Exception('Unacceptable email domain for tenants. Must be: ' . implode(', ', $allowedTenantDomains));
            }
        }

        // Profile Picture Validation
        if ($profilePicture['error'] !== 0) {
            $uploadErrors = [
                1 => 'Photo file exceeds upload_max_filesize.',
                2 => 'Photo file exceeds MAX_FILE_SIZE.',
                3 => 'Photo file uploaded partially.',
                4 => 'No photo file uploaded.',
                6 => 'Temporary folder missing.',
                7 => 'Failed to write file to disk.',
                8 => 'PHP extension stopped file upload.',
            ];
            throw new Exception($uploadErrors[$profilePicture['error']]);
        }

        $fileExtension = strtolower(pathinfo($profilePicture['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Only " . implode(', ', $allowedExtensions) . " are accepted.");
        }

        if (!getimagesize($profilePicture['tmp_name'])) {
            throw new Exception("The file uploaded is not an image type.");
        }

        if ($profilePicture['size'] > $maxFileSize) {
            throw new Exception("Photo file is too large (5MB limit).");
        }

       

        // Upload Profile Picture
        $fileName = $username . '_' . date("mdYHi") . '.' . $fileExtension;
        $target = "profile-pictures/" . $fileName;
        $databaseTarget = "/bookingapp/user/profile-pictures/" . $fileName;

        echo "Old Photo: $oldPhoto | Uploaded Photo: " . $profilePicture['name'] . "<br>";
        echo "Old Photo: $oldPhoto | Uploaded Photo: " . $profilePicture['name'] . "<br>";
        echo "Target: $oldPhoto | Database Target: $databaseTarget";

        try {
            // Check if file is uploaded
            if (!is_uploaded_file($profilePicture['tmp_name'])) {
                throw new Exception('File not uploaded.');
            }

            // Delete old photo
            if (file_exists($oldPhoto)) {
                unlink($oldPhoto);
            }

            // Move uploaded file
            if (!move_uploaded_file($profilePicture['tmp_name'], $target)) {
                throw new Exception('Error uploading file.');
            }

            echo "File uploaded successfully.";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            // Log error or display error message
        }

        // Update user account
        $sql = "UPDATE user_account SET Username = ? WHERE UserID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "si", $username, $thisUserID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        // Update person profile
        $sql = "UPDATE person SET FirstName = ?, MiddleName = ?, LastName = ?, ExtName = ? WHERE PersonID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssssi", $firstName, $middleName, $lastName, $extName, $thisPersonID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqlI_stmt_error($stmt));
        }

        // Update role info
        if ($role === "admin") {
            $sql = "UPDATE admin SET PositionTitle = ?, Institution = ? WHERE UserID = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Prepared statement error: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, 'ssi', $positionTitle, $institution, $thisUserID);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
            }
        } else if ($role === "owner") {
            $sql = "UPDATE establishment_owner SET PositionTitle = ?, Institution = ? WHERE UserID = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Prepared statement error: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, 'ssi', $positionTitle, $institution, $thisUserID);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
            }
        }

        // If there is a change in Email Address
        if ($profileAccount['EmailAddress'] !== $emailAddress) {
            // Update Email Address
            $sql = "UPDATE user_account SET EmailAddress = ?, IsEmailVerified = 0 WHERE UserID = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Prepared statement error: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "si", $emailAddress, $thisUserID);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
            }
        }

        // If there is a change in the Profile Picture
        if ($_FILES['fileInput']['error'] === 0) {
            // Update Profile Picture
            $sql = "UPDATE person SET ProfilePicture = ? WHERE PersonID = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Prepared statement error: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "si", $databaseTarget, $thisPersonID);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
            }
        }

        
        $generalPromptSuccess = "<i class='fa-solid fa-circle-check'></i> Profile successfully updated.";
        $thisProfilePicture = $newFilePath;
        $generalPromptError = "";
        sleep(5);
        header("Location: profile.php?id=$username");

    } catch (Exception $e) {
        $generalPromptError = "<i class='fa-solid fa-circle-xmark'></i> " . $e->getMessage();
        // sleep(3);
        // $generalPromptError = "";
        echo $generalPromptError;
    }

}

$passwordPromptError = "";
$passwordPromptSuccess = "";

// Update Password
if (isset($_POST['submitPassword'])) {
    $currentPassword = mysqli_real_escape_string($conn, $_POST['current-password']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new-password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm-password']);

    $savedPassword = $profileAccount['Password'];

    $hashedCurrentPassword = md5($currentPassword);

    try {
        // Check if the entered current password matches the one that is saved in the database
        if ($hashedCurrentPassword !== $savedPassword) {
            throw new Exception("You've entered a wrong password");
        }

        // Validate password length
        if (strlen($newPassword) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        // Check if password is confirmed
        if ($newPassword !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }

        $hashedNewPassword = md5($newPassword);

        // Update password
        $sql = "UPDATE user_account SET Password = ? WHERE UserID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'si', $hashedNewPassword, $thisUserID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        $passwordPromptSuccess = "<i class='fa-solid fa-circle-check'></i> Password successfully updated.";

    } catch (Exception $e) {
        $passwordPromptError = "<i class='fa-solid fa-circle-xmark'></i> " . $e->getMessage();
    }
}

if (isset($_POST['resetPicture'])) {
    try {
        $sql = "UPDATE person SET ProfilePicture = null WHERE PersonID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $thisPersonID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        $generalPromptSuccess = "<i class='fa-solid fa-image'></i> Profile picture has been removed.";
        $generalPromptError = "";
        sleep(5);
        header("Location: profile.php?id=$profileUsername");
    } catch (Exception $e) {
        $generalPromptError = "<i class='fa-solid fa-circle-xmark'></i> " . $e->getMessage();
    }
}

// Update info
$infoPromptError = "";
$infoPromptSuccess = "";
if (isset($_POST['submitInfo'])) {
    $gender = mysqli_real_escape_string($conn, $_POST['gender'] ?? null);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday'] ?? null);
    $religion = mysqli_real_escape_string($conn, $_POST['religion'] ?? null);
    $contact = mysqli_real_escape_string($conn, $_POST['contact'] ?? null);
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? null);

    try {
        // Validate contact
        if (!preg_match('/^09\d{2}\s?\d{3}\s?\d{4}$/', $contact)) {
            throw new Exception("Invalid contact format.");
        }

        // Update person (except for contact yet)
        $sql = "UPDATE person SET Gender = ?, DateOfBirth = ?, Religion = ?, HomeAddress = ? WHERE PersonID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssssi", $gender, $birthday, $religion, $address, $thisPersonID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        $currentContactNumber = $person['ContactNumber'];

        // If contact number is changed
        if ($contact !== $currentContactNumber) {
            $sql = "UPDATE person SET ContactNumber = ?, IsContactVerified = 0 WHERE PersonID = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Prepared statement error: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "si", $contact, $thisPersonID);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
            }
        }

        // Success
        $infoPromptSuccess = "<i class='fa-solid fa-circle-check'></i> Information successfully updated!";
        header("Location: profile.php?id=$profileUsername");

    } catch (Exception $e) {
        $infoPromptError = "<i class='fa-solid fa-circle-xmark'></i> " . $e->getMessage();
    }
}

// Update social links
$socialPromptError = "";
$socialPromptSuccess = "";

if (isset($_POST['submitSocial'])) {
    $facebook = mysqli_real_escape_string($conn, $_POST['social-facebook'] ?? '');
    $twitter = mysqli_real_escape_string($conn, $_POST['social-twitter'] ?? '');
    $instagram = mysqli_real_escape_string($conn, $_POST['social-instagram'] ?? '');
    $youtube = mysqli_real_escape_string($conn, $_POST['social-youtube'] ?? '');
    $tiktok = mysqli_real_escape_string($conn, $_POST['social-tiktok'] ?? '');
    $linkedin = mysqli_real_escape_string($conn, $_POST['social-linkedin'] ?? '');
    $website = mysqli_real_escape_string($conn, $_POST['social-website'] ?? '');

    try {
        $sql = "UPDATE user_socials SET FacebookURL = ?, TwitterX = ?, Instagram = ?, YouTube = ?, TikTok = ?, LinkedIn = ?, Website = ? WHERE PersonID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "sssssssi", $facebook, $twitter, $instagram, $youtube, $tiktok, $linkedin, $website, $thisPersonID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        $socialPromptSuccess = "Social links are successfully updated.";
        // header("Location: profile.php?id=$profileUsername");
    } catch (Exception $e) {
        $socialPromptError = "<i class='fa-solid fa-circle-mark'></i> " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $personFullName; ?>'s Profile</title>

    <?php
    include "../php/head_tag.php";
    ?>
    <!-- <link rel="stylesheet" href="/bookingapp/css/w3.css"> -->

    <link rel="stylesheet" href="/bookingapp/css/profile.css">
    <link href="/bookingapp/css/bootstrap.css" rel="stylesheet">

    <style>
        .form-inline {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3px;
        }

        .container {
            margin-top: 25px;
            margin-bottom: 25px;
            padding-bottom: 50px;
            display: block;
            height: auto;        
        }

        nav {
            display: flex !important;
        }

        .balay-nav-links a:hover {
            text-decoration: none !important;
        }

        .card {
            padding: 10px;
            width: 100%;
        }

        .col-md-3, .col-md-9 {
            padding: 10px;
        }

        input, select {
            width: 100%;
        }

        .modal-content {
            width: 40%;
            position: relative;
            display: flex;
            margin: auto;
            border-radius: 8px
        }

        .modal-content h3 {
            font-size: 24px;
            font-weight: bold;
        }

        .modal-content .close {
            position: absolute;
            top: 20px;
            right: 25px;
            margin-left: 10px;
        }

        .modal {
            justify-content: center;
        }

        .toggle-password {
            position: absolute;
            right: 20px;
            top: 11px;
            cursor: pointer;
        }

        .edit-btn {
            float: right;
            background-color: #ffd700;
            color: black;
            border: none;
            outline: none;
            border-radius: 5px;
        }

        .edit-btn:hover {
            background-color: maroon;
            color: white;
        }

        .search-form {
            display: flex;
            padding: 10px;
        }

        .search-form input,
        .search-form button {
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
        }

        .search-form button {
            color: white;
            background-color: maroon;
            border: none;
            outline: none;
        }

        .search-form button:hover {
            color: black;
            background-color: #ffd700;
        }

        .profile-card {
            width: 100%;
            max-width: 350px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .profile-header {
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-name {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-role {
            font-size: 0.9rem;
            color: #555;
            margin: 0 !important;
        }

        .profile-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 15px;
        }

        .profile-details p {
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .toggle-icon {
            font-size: 1.5rem;
            font-weight: bold;
            color: maroon;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.2s;
        }

        .profile-details.open {
            max-height: 500px;
            padding: 15px;
        }

        .profile-header .toggle-icon.open {
            transform: translateY(-50%) rotate(45deg);
        }

        .profile-actions {
            padding: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
            background-color: maroon;
            font-size: 0.9rem;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn:hover {
            background-color: #ffd700;
            color: black;
        }

        .tab-content {
            padding: 20px;
        }

        

       
        @media (max-width: 1000px) {
            .form-inline input, .form-inline select {
                width: 100% !important;
            }

            .form-inline {
                display: block;
            }
            nav {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php include "../php/header.php"; ?>
    </header>

    <div class="container clearfix">
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        <a href="#account-general" class="list-group-item list-group-item-action active" data-toggle="list">General</a>
                        <?php if ($isUserProfile) { ?>
                        <a href="#account-change-password" class="list-group-item list-group-item-action" data-toggle="list">Change password</a>
                        <?php } ?>
                        <a href="#account-info" class="list-group-item list-group-item-action" data-toggle="list">Info</a>
                        <!-- <a href="#account-social-links" class="list-group-item list-group-item-action" data-toggle="list">Social links</a> -->
                        <?php if ($isUserProfile) { ?>
                        <a href="#account-connections" class="list-group-item list-group-item-action" data-toggle="list" hidden>Connections</a>
                        <?php } ?>
                        <?php if ($thisRole === 'owner') { ?>
                        <a href="#establishments" class="list-group-item list-group-item-action" data-toggle="list">Owned establishments</a>
                        <?php } ?>
                        <?php if ($thisRole === 'tenant') { ?>
                        <a href="#residencies" class="list-group-item list-group-item-action" data-toggle="list">Residencies</a>
                        <a href="#residency_history" class="list-group-item list-group-item-action" data-toggle="list">Residency history</a>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content">
                        <!-- General -->
                        <div class="tab-pane fade active show" id="account-general">
                            <form method="post" id="general-form" enctype="multipart/form-data">
                                <p id="general-prompt" style="margin: auto; font-size: 12px; margin-left: 20px;"></p>
                                <p style="color: red; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $generalPromptError; ?></p>
                                <p style="color: green; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $generalPromptSuccess; ?></p>
                                <input type="hidden" name="role" id="role" value="<?php echo $thisRole; ?>">
                                <?php if ($isUserProfile) { ?>
                                    <button type="button" class="btn edit-btn" id="edit-general" name="isEditingGeneral" onclick="editGeneral()"><i class="fa-solid fa-edit"></i> Edit</button>
                                <?php } ?>
                                <div class="card-body media align-items-center">
                                    <img src="<?php echo $thisProfilePicture; ?>" alt="<?php echo $thisLastName . ' pic'; ?>" id="profile-picture-preview" class="d-block ui-w-80" style="margin: 1.2rem; object-fit: cover;">
                                    <?php if ($isUserProfile) { ?>
                                    <div class="media-body ml-4" id="profile-picture-settings" hidden>
                                        <label for="fileInput" class="btn btn-outline-primary"><i class="fa-solid fa-upload"></i> Upload new photo
                                            <input type="file" name="fileInput" id="fileInput" class="account-settings-fileinput" accept=".jpg, .jpeg, .png, .gif">
                                            <input type="hidden" name="old-photo" value="<?php echo $thisProfilePicture; ?>">

                                        </label> &nbsp;
                                        <button class="btn btn-default md-btn-flat" name="resetPicture" onclick="resetPicture()"><i class="fa-solid fa-trash"></i> Remove photo</button>
                                        <div class="small mt-1" >Allowed JPG, GIF or PNG. Max size of 800K</div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="border-light m-0">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="institutional-email" class="form-label">Email address 
                                                <?php
                                                    if ($isUserProfile) {
                                                        if ($isEmailVerified) {
                                                            echo '<span style="background-color: #00552b; color: white; font-size: 10px; padding: 3px; border-radius: 4px;"><i class="fa-solid fa-circle-check"></i> Verified</span>';
                                                        } else {
                                                            echo '<span style="background-color: red; color: white; font-size: 10px; padding: 3px; border-radius: 4px;"><i class="fa-solid fa-circle-xmark"></i> Verify Now!</span>';
                                                        }
                                                    }
                                                ?>
                                            </label>
                                            <input type="text" name="email-address" id="email-address" class="form-control mb-1" value="<?php echo $profileAccount['EmailAddress']; ?>" disabled>

                                            <?php if ($isUserProfile && !$isEmailVerified) { ?>
                                            <div class="alert alert-warning mt-3">
                                                Your email is not confirmed. Please check your inbox to verify. <br>
                                                <a href="/bookingapp/otp/otp_form.php?user=<?php echo $thisUserID; ?>&purpose=email">Resend confirmation</a>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <?php if ($thisRole === 'tenant') { ?>
                                            <div class="form-group">
                                                <label for="id-number" class="form-label">Tenant ID:</label>
                                                <input type="number" name="" id="" class="form-control mb-1" readonly value="<?php echo $thisTenantID; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="id-number" class="form-label">University ID:</label>
                                                <input type="number" name="role-id" id="" class="form-control mb-1" readonly value="<?php echo $thisUniversityID; ?>">
                                            </div>
                                        <?php } ?>

                                        <?php if ($thisRole === 'owner') { ?>
                                            <div class="form-group">
                                                <label for="id-number" class="form-label">Establishment Owner ID:</label>
                                                <input type="number" name="role-id" id="" class="form-control mb-1" readonly value="<?php echo $thisOwnerID; ?>">
                                            </div>
                                        <?php } ?>

                                        <?php if ($thisRole === 'admin') { ?>
                                            <div class="form-group">
                                                <label for="id-number" class="form-label">Admin ID:</label>
                                                <input type="number" name="role-id" id="" class="form-control mb-1" readonly value="<?php echo $thisAdminID; ?>">
                                            </div>
                                        <?php } ?>

                                        <?php if ($thisRole === 'owner' || $thisRole === 'admin') { ?>
                                            <div class="form-group">
                                                <label for="" class="form-label">Institution/office/company:</label>
                                                <input type="text" name="institution" id="institution" class="form-control mb-1" value="<?php echo $thisInstitution; ?>" placeholder="N/A" disabled>
                                            </div>

                                            <div class="form-group">
                                                <label for="" class="form-label">Position title:</label>
                                                <input type="text" name="position-title" id="position-title" class="form-control mb-1" value="<?php echo $thisPositionTitle; ?>" placeholder="N/A" disabled>
                                            </div>
                                        <?php } ?>

                                        <div class="form-group">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" name="username" id="username" class="form-control mb-1" placeholder="Username" value="<?php echo $profileAccount['Username']; ?>" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="full-name" class="form-label">Full name</label>
                                            <div class="form-inline">
                                                <input type="text" name="first-name" id="first-name" class="form-control mb-1" placeholder="First name" required  value="<?php echo $person['FirstName']; ?>" disabled>

                                                <input type="text" name="middle-name" id="middle-name" class="form-control mb-1" placeholder="Middle name" value="<?php echo $person['MiddleName']; ?>" disabled>

                                                <input type="text" name="last-name" id="last-name" class="form-control mb-1" placeholder="Last name" required value="<?php echo $person['LastName']; ?>" disabled>
                                                
                                                <?php if ($isUserProfile) { ?>
                                                    <select name="ext-name" class="form-control mb-1" id="ext-name" disabled>
                                                        <option value="" <?php if (empty($person['ExtName'])) { echo "selected"; } ?>>None</option>
                                                        <option value="Jr." <?php if ($person['ExtName'] === "Jr.") { echo "selected"; } ?>>Jr.</option>
                                                        <option value="Sr." <?php if ($person['ExtName'] === "Sr.") { echo "selected"; } ?>>Sr.</option>
                                                        <option value="I" <?php if ($person['ExtName'] === "I") { echo "selected"; } ?>>I</option>
                                                        <option value="II" <?php if ($person['ExtName'] === "II") { echo "selected"; } ?>>II</option>
                                                        <option value="III" <?php if ($person['ExtName'] === "III") { echo "selected"; } ?>>III</option>
                                                        <option value="IV" <?php if ($person['ExtName'] === "IV") { echo "selected"; } ?>>IV</option>
                                                        <option value="V" <?php if ($person['ExtName'] === "V") { echo "selected"; } ?>>V</option>
                                                        <option value="VI" <?php if ($person['ExtName'] === "VI") { echo "selected"; } ?>>VI</option>
                                                    </select>
                                                <?php } else {
                                                    if (!empty($thisExtName)) { ?>
                                                        <input type="text" class="form-control mb-1" readonly value="<?php echo $person['ExtName']; ?>">
                                                <?php }
                                                } ?>
                                            </div>
                                        </div>

                                        <?php if ($isUserProfile) { ?>
                                            <div class="text-right mt-3" id="general-btns" hidden>
                                                <button id="submit-general-btn" name="submitGeneral" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>&nbsp;
                                                <button type="submit" id="cancel-general-btn" class="btn btn-default" onclick="cancelEditingGeneral()"><i class="fa-solid fa-ban"></i> Cancel</button>
                                            </div>
                                    <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <?php if ($isUserProfile) { ?>
                        <!-- Current password -->
                        <div class="tab-pane fade" id="account-change-password">
                            <form method="post" id="password-form" enctype="multipart/form-data">
                                    <p id="password-prompt" style="margin: auto; font-size: 12px; margin-left: 20px;"></p>
                                    <p style="color: red; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $passwordPromptError; ?></p>
                                    <p style="color: green; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $passwordPromptSuccess; ?></p>
                                    <?php if ($isUserProfile) { ?>
                                        <button type="button" class="btn edit-btn" id="edit-password" name="isEditingPassword" onclick="editPassword()"><i class="fa-solid fa-edit"></i> Update Password</button>
                                    <?php } ?>
                                <div class="card-body pb-2">

                                    <div class="form-group confirm-password-field">
                                        <label for="current-password" class="form-label">Current password</label>
                                        <input type="password" name="current-password" id="current-password" class="form-control" placeholder="Current password" required>
                                    </div>

                                    <div class="form-group new-password-field">
                                        <label for="new-password" class="form-label">New password</label>
                                        <input type="password" name="new-password" id="new-password" class="form-control" placeholder="New password" required>
                                    </div>

                                    <div class="form-group confirm-password-field">
                                        <label for="confirm-password" class="form-label">Confirm password</label>
                                        <input type="password" name="confirm-password" id="confirm-password" class="form-control" placeholder="Confirm new password" required>
                                    </div>

                                    <?php if ($isUserProfile) { ?>
                                        <div class="text-right mt-3" id="password-btns" hidden>
                                            <button type="submit" id="submit-password-btn" name="submitPassword" class="btn btn-primary">Save changes</button>&nbsp;
                                            <button id="cancel-password-btn" class="btn btn-default" onclick="cancelEditingPassword()">Cancel</button>
                                        </div>
                                <?php } ?>
                                </div>
                            </form>
                        </div>
                        <?php } ?>

                        <!-- Info -->
                        <div class="tab-pane fade" id="account-info">
                            <form method="post" id="info-form" enctype="multipart/form-data">
                                <p id="info-prompt" style="margin: auto; font-size: 12px; margin-left: 20px;"></p>
                                <p style="color: red; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $infoPromptError; ?></p>
                                <p style="color: green; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $infoPromptSuccess; ?></p>
                                <?php if ($isUserProfile) { ?>
                                    <button type="button" class="btn edit-btn" id="edit-info" name="isEditingInfo" onclick="editInfo()"><i class="fa-solid fa-edit"></i> Update Information</button>
                                <?php } ?>
                                <div class="card-body pb-2">
                                    <div class="form-group">
                                        <label for="gender" class="form-label">Gender</label>
                                        <?php if ($isUserProfile) { ?>
                                            <select name="gender" id="gender" class="form-control" required disabled>
                                                <option value="Male" <?php if ($person['Gender'] === "Male") { echo "selected"; } ?>>Male</option>
                                                <option value="Female" <?php if ($person['Gender'] === "Female") { echo "selected"; } ?>>Female</option>
                                            </select>
                                        <?php } else { ?>
                                            <input type="text" name="gender" class="form-control" readonly value="<?php echo $person['Gender']; ?>">
                                        <?php } ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="birthday" class="form-label">Birthday</label>
                                        <input type="<?php if ($isUserProfile) { echo 'date'; } else { echo 'text'; }?>" name="birthday" id="birthday" class="form-control" value="<?php if ($isUserProfile) { echo $thisBirthday; } else { echo date('F d, Y', strtotime($thisBirthday)); } ?>" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="religion" class="form-label">Religion</label>
                                        <?php if ($isUserProfile) { ?>
                                            <select name="religion" id="religion" class="form-control" disabled>
                                                <option value="" <?php if ($person['Religion'] === null) { echo "selected"; } ?>>Do not specify</option>
                                                <option value="Islam" <?php if ($person['Religion'] === "Islam") { echo "selected"; } ?>>Islam</option>
                                                <option value="Christianity" <?php if ($person['Religion'] === "Christianity") { echo "selected"; } ?>>Christianity</option>
                                                <option value="Judaism (Jewish)" <?php if ($person['Religion'] === "Judaism (Jewish)") { echo "selected"; } ?>>Judaism (Jewish)</option>
                                                <option value="Buddhism" <?php if ($person['Religion'] === "Buddhism") { echo "selected"; } ?>>Buddhism</option>
                                                <option value="Hinduism" <?php if ($person['Religion'] === "Hinduism") { echo "selected"; } ?>>Hinduism</option>
                                                <option value="Other religion" <?php if ($person['Religion'] === "Other religion") { echo "selected"; } ?>>Other religion</option>
                                                <option value="No religion" <?php if ($person['Religion'] === "No religion") { echo "selected"; } ?>>No religion</option>
                                            </select>
                                        <?php } else { ?>
                                            <input type="text" name="religion" class="form-control" value="<?php echo $person['Religion']; ?>" readonly>
                                        <?php } ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact" class="form-label">Contact number
                                            <?php
                                                // if ($isUserProfile) {
                                                //     $isContactVerified = $person['IsContactVerified'];
                                                //     if ($isContactVerified) {
                                                //         echo '<span style="background-color: #00552b; color: white; font-size: 10px; padding: 3px; border-radius: 4px;"><i class="fa-solid fa-circle-check"></i> Verified</span>';
                                                //     } else {
                                                //         echo '<span style="background-color: red; color: white; font-size: 10px; padding: 3px; border-radius: 4px;"><i class="fa-solid fa-circle-xmark"></i> Verify Now!</span>';
                                                //     }
                                                // }
                                            ?>
                                        </label>
                                        <input type="tel" name="contact" id="contact" class="form-control" placeholder="09xx xxx xxxx" value="<?php echo $person['ContactNumber']; ?>" disabled required>

                                        <?php if ($isUserProfile && !$isContactVerified) { ?>
                                            <!-- <div class="alert alert-warning mt-3">
                                                Your contact number is not confirmed. Please verify. <br>
                                                <a href="/bookingapp/otp/otp_form.php?user=<?php echo $thisUserID; ?>&purpose=contact">Resend confirmation</a>
                                            </div> -->
                                        <?php } ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="form-label">Home address</label>
                                        <input type="text" name="address" id="address" class="form-control" placeholder="Home address" value="<?php echo $person['HomeAddress']; ?>"  disabled required>
                                    </div>

                                    <?php if ($isUserProfile) { ?>
                                    <div class="text-right mt-3" id="info-btns">
                                        <button name="submitInfo" class="btn btn-primary">Save changes</button>&nbsp;
                                        <button type="button" class="btn btn-default" onclick="cancelEditingInfo()">Cancel</button>
                                    </div>
                                <?php } ?>
                                </div>
                            </form>
                        </div>

                        <!-- Get social link data from the database -->
                        <?php
                        $userSocials = null;
                        $personID = $person['PersonID'];    
                        $sql = "SELECT * FROM user_socials WHERE PersonID = $personID";
                        $result = mysqli_query($conn, $sql);
                        $rowCount = mysqli_num_rows($result);

                        if ($rowCount > 0) {
                            $userSocials = mysqli_fetch_assoc($result);
                        }
                        ?>

                        <!-- Social links -->
                        <div class="tab-pane fade" id="account-social-links">
                            <form method="post" id="social-form" enctype="multipart/form-data" style="display: flex; flex-direction: column; justify-content: flex-end; flex-wrap: nowrap">
                                <p id="social-prompt" style="margin: auto; font-size: 12px; margin-left: 20px;"></p>
                                <p style="color: red; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $socialPromptError; ?></p>
                                <p style="color: green; margin: auto; font-size: 12px; margin-left: 20px;"><?php echo $socialPromptSuccess; ?></p>
                                
                                <div class="card-body pb-2" style="margin-top: 30px;">
                                    <div class="form-group">
                                        <label for="facebook" class="form-label"><i class="fa-brands fa-facebook"></i> Facebook</label>
                                        <?php if (!empty($userSocials['FacebookURL'])) { ?>
                                            <a href="<?php echo $userSocials['FacebookURL']; ?>" target="_blank" style="font-size: 12px; float: right"><i class="fa-solid fa-link"></i> Visit Link</a>
                                        <?php } ?>
                                        <input type="text" name="social-facebook" id="social-facebook" class="form-control" placeholder="<?php if ($isUserProfile) { echo "Enter Facebook URL"; } else { echo "N/A"; } ?>" value="<?php echo $userSocials['FacebookURL']; ?>" disabled>
                                        
                                    </div>

                                    <div class="form-group">
                                        <label for="x-twitter" class="form-label"><i class="fa-brands fa-x-twitter"></i> X</label>
                                        <?php if (!empty($userSocials['TwitterX'])) { ?>
                                            <a href="<?php echo $userSocials['TwitterX']; ?>" target="_blank" style="font-size: 12px; float: right"><i class="fa-solid fa-link"></i> Visit Link</a>
                                        <?php } ?>
                                        <input type="text" name="social-twitter" id="social-twitter" class="form-control" placeholder="<?php if ($isUserProfile) { echo "Enter Twitter/X URL"; } else { echo "N/A"; } ?>" value="<?php echo $userSocials['TwitterX']; ?>" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="instagram" class="form-label"><i class="fa-brands fa-instagram"></i> Instagram</label>
                                        <?php if (!empty($userSocials['Instagram'])) { ?>
                                            <a href="<?php echo $userSocials['Instagram']; ?>" target="_blank" style="font-size: 12px; float: right"><i class="fa-solid fa-link"></i> Visit Link</a>
                                        <?php } ?>
                                        <input type="text" name="social-instagram" id="social-instagram" class="form-control" placeholder="<?php if ($isUserProfile) { echo "Enter Instagram URL"; } else { echo "N/A"; } ?>" value="<?php echo $userSocials['Instagram']; ?>" disabled>  
                                    </div>

                                    <div class="form-group">
                                        <label for="youtube" class="form-label"><i class="fa-brands fa-youtube"></i> YouTube</label>
                                        <?php if (!empty($userSocials['YouTube'])) { ?>
                                            <a href="<?php echo $userSocials['YouTube']; ?>" target="_blank" style="font-size: 12px; float: right"><i class="fa-solid fa-link"></i> Visit Link</a>
                                        <?php } ?>
                                        <input type="text" name="social-youtube" id="social-youtube" class="form-control" placeholder="<?php if ($isUserProfile) { echo "Enter YouTube URL"; } else { echo "N/A"; } ?>" value="<?php echo $userSocials['YouTube']; ?>" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="tiktok" class="form-label"><i class="fa-brands fa-tiktok"></i> TikTok</label>
                                        <?php if (!empty($userSocials['TikTok'])) { ?>
                                            <a href="<?php echo $userSocials['TikTok']; ?>" target="_blank" style="font-size: 12px; float: right"><i class="fa-solid fa-link"></i> Visit Link</a>
                                        <?php } ?>
                                        <input type="text" name="social-tiktok" id="social-tiktok" class="form-control" placeholder="<?php if ($isUserProfile) { echo "Enter TikTok URL"; } else { echo "N/A"; } ?>" value="<?php echo $userSocials['TikTok']; ?>" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="linkedin" class="form-label"><i class="fa-brands fa-linkedin"></i> LinkedIn</label>
                                        <?php if (!empty($userSocials['LinkedIn'])) { ?>
                                            <a href="<?php echo $userSocials['LinkedIn']; ?>" target="_blank" style="font-size: 12px; float: right"><i class="fa-solid fa-link"></i> Visit Link</a>
                                        <?php } ?>
                                        <input type="text" name="social-linkedin" id="social-linkedin" class="form-control" placeholder="<?php if ($isUserProfile) { echo "Enter LinkedIn URL"; } else { echo "N/A"; } ?>" value="<?php echo $userSocials['LinkedIn']; ?>" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="website" class="form-label"><i class="fa-solid fa-globe"></i> Website</label>
                                        <?php if (!empty($userSocials['Website'])) { ?>
                                            <a href="<?php echo $userSocials['Website']; ?>" target="_blank" style="font-size: 12px; float: right"><i class="fa-solid fa-link"></i> Visit Link</a>
                                        <?php } ?>
                                        <input type="text" name="social-website" id="social-website" class="form-control" placeholder="<?php if ($isUserProfile) { echo "Enter website URL"; } else { echo "N/A"; } ?>" value="<?php echo $userSocials['Website']; ?>" disabled>
                                    </div>
                                </div>

                                <?php if ($isUserProfile) { ?>
                                    <button type="button" class="btn edit-btn" id="edit-social" name="isEditingSocial" onclick="editSocial()" style="position: relative; float: right"><i class="fa-solid fa-edit"></i> Update Links</button>
                                <?php } ?>

                                <?php if ($isUserProfile) { ?>
                                    <div class="text-right mt-3" id="social-btns" hidden>
                                        <button type="submit" name="submitSocial" class="btn btn-primary">Save changes</button>&nbsp;
                                        <button type="button" class="btn btn-default" onclick="cancelEditingSocial()">Cancel</button>
                                    </div>
                                <?php } ?>
                            </form>
                        </div>

                        <!-- Connections -->
                        <div class="tab-pane fade" id="account-connections">
                            <div class="card-body">
                                <button type="button" class="btn btn-google">
                                    Connect to 
                                    <i class="fa-brands fa-google"></i> <strong>Google</strong>
                                </button>
                            </div>
                            <hr class="border-light m-0">
                            <div class="card-body">
                                <h5 class="mb-2">
                                    <a href="javascript:void(0)" class="float-right text-muted text-tiny"><i class="ion ion-md-close"></i> Remove</a>
                                    You are connected to Google:
                                </h5>
                                <a href="" class="confidential">[email &#160;protected]</a>
                            </div>
                            <hr class="border-light m-0">
                            <div class="card-body">
                                <button type="button" class="btn btn-apple">
                                    Connect to
                                    <i class="fa-brands fa-apple"></i>
                                    <strong>Apple</strong>
                                </button>
                            </div>
                            
                            <hr class="border-light m-0">
                            <div class="card-body">
                                <button type="button" class="btn btn-facebook">
                                    Connect to
                                    <i class="fa-brands fa-facebook"></i>
                                    <strong>Facebook</strong>
                                </button>
                            </div>
                        </div>

                        <?php if ($thisRole === 'owner') { 
                        $establishments = [];

                        $sql = "SELECT e.EstablishmentID, e.Name, e.Type, e.CreatedAt, e.NoOfFloors, e.Status, e.GenderInclusiveness, ep.Photo1 FROM establishment e INNER JOIN establishment_photos ep ON ep.EstablishmentID = e.EstablishmentID WHERE e.OwnerID = $thisOwnerID AND e.Status != 'removed' ORDER BY e.Name";
                        $result = mysqli_query($conn, $sql);

                        echo mysqli_error($conn);

                        $rowCount = mysqli_num_rows($result);
                        
                        ?> 

                        <div class="tab-pane fade" id="establishments">
                            <div class="tab-content">

                                <?php echo "<p>Showing $rowCount result(s).</p>"; ?>

                                <!-- Profile Cards Container -->
                                <div class="card-container" style="display: flex; gap: 12px; align-items: flex-start">
                                    <?php                     
                                        if ($result && mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Extract data from the current row
                                                $establishmentID = $row['EstablishmentID'];
                                                $encryptedEstID = base64_encode($establishmentID);

                                                $establishmentName = $row['Name'];
                                                $establishmentType = $row['Type'];
                                                $establishmentPhoto = $row['Photo1'];
                                                $establishmentPhoto = isset($establishmentPhoto) && $establishmentPhoto !== "" ? "/bookingapp/establishment/$establishmentPhoto" : "/bookingapp/assets/images/msu-facade.jpg";

                                                $dateCreated = date('F d, Y', strtotime($row['CreatedAt']));
                                                $floors = $row['NoOfFloors'] ?? 1;
                                                $genderInclusivity = $row['GenderInclusiveness'] ?? 'Coed';
                                                $estStatus = $row['Status'];
                                    ?>
                                                <div class="profile-card">  
                                                    <div class="profile-header" onclick="toggleDetails(this)">
                                                        <img src="<?php echo $establishmentPhoto; ?>" alt="<?php echo $establishmentName; ?>" class="profile-pic">
                                                        <div class="profile-info">
                                                            <h3 class="profile-name"><a href="/bookingapp/establishment/establishment.php?est=<?php echo $encryptedEstID; ?>"><?php echo $establishmentName; ?></a></h3>
                                                            <p class="profile-role">
                                                                <?php
                                                                echo $establishmentType;
                                                                ?>
                                                            </p>
                                                        </div>
                                                        <span class="toggle-icon"><i class="fa-solid fa-caret-down"></i></span>
                                                    </div>

                                                    <div class="profile-details hidden">
                                                        <p><strong>Floors:</strong> <?php echo $floors; ?></p>
                                                        <p><strong>Status:</strong> <?php echo $estStatus; ?></p>
                                                        <p><strong>Gender accommodation:</strong> <?php echo $genderInclusivity; ?></p>
                                                        <p><strong>Date created:</strong> <?php echo $dateCreated; ?></p>
                                                    </div>

                                                    <div class="profile-actions">
                                                        <button class="btn" onclick="redirect('/bookingapp/establishment/establishment.php?est=<?php echo $encryptedEstID; ?>')"><i class="fa-solid fa-eye"></i> View Establishment</button>
                                                        <!-- <button class="btn btn-secondary"><i class="fa-solid fa-edit"></i> Edit</button> -->
                                                    </div>                    
                                                </div>
                                    
                                                
                                    <?php
                                            }
                                    ?>
                                    <?php
                                        } else {
                                            echo "<p>No data found for this tab.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php } ?>

                        <?php if ($thisRole === 'tenant') {
                            $establishments = [];

                            $sql = "SELECT res.ResidencyID, r.RoomID, r.RoomName, r.RoomType, e.EstablishmentID,
                                e.Name AS EstablishmentName, e.Type AS EstablishmentType,
                                ep.Photo1, res.DateOfEntry, res.DateOfExit, res.CreatedAt AS BookingDate,
                                res.Status AS ResidencyStatus FROM residency res 
                                INNER JOIN tenant t ON t.TenantID = res.TenantID 
                                INNER JOIN rooms r ON r.RoomID = res.RoomID
                                INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID
                                INNER JOIN establishment_photos ep ON ep.EstablishmentID = e.EstablishmentID
                                WHERE res.Status = 'currently residing' AND res.TenantID = $thisTenantID ORDER BY res.DateOfEntry";
                            $currentResidencyResult = mysqli_query($conn, $sql);
    
                            echo mysqli_error($conn);
    
                            $currentResidencyCount = mysqli_num_rows($currentResidencyResult);
                            // echo "Count $currentResidencyCount";
                        ?> 

                        <div class="tab-pane fade" id="residencies">
                            <div class="tab-content">

                            <h3>Current residence</h3>
                            <?php echo "<p>Showing $currentResidencyCount result(s).</p>"; ?>

                                <!-- Profile Cards Container -->
                                <div class="card-container" style="display: flex; gap: 12px; align-items: flex-start">
                                    <?php                     
                                        if ($currentResidencyCount > 0) {
                                            while ($row = mysqli_fetch_assoc($currentResidencyResult)) {
                                                // Extract data from the current row
                                                $residencyID = $row['ResidencyID'];
                                                $encryptedResidencyID = base64_encode($residencyID);
                                                $roomID = $row['RoomID'];
                                                $roomName = $row['RoomName'];
                                                $roomType = $row['RoomType'];
                                                $establishmentID = $row['EstablishmentID'];
                                                $establishmentName = $row['EstablishmentName'];
                                                $establishmentType = $row['EstablishmentType'];
                                                $establishmentPhoto = $row['Photo1'];
                                                $establishmentPhoto = isset($establishmentPhoto) && $establishmentPhoto !== "" ? "/bookingapp/establishment/$establishmentPhoto" : "/bookingapp/assets/images/msu-facade.jpg";

                                                $dateOfEntry = date("F d, Y", strtotime($row['DateOfEntry']));
                                                $dateOfExit = isset($row['DateOfExit']) ? date("F d, Y", strtotime($row['DateOfExit'])) : "";
                                                $bookingDate = date("F d, Y", strtotime($row['BookingDate']));

                                                $residencyStatus = $row['ResidencyStatus'];
                                    ?>
                                                <div class="profile-card">  
                                                    <div class="profile-header" onclick="toggleDetails(this)">
                                                        <img src="<?php echo $establishmentPhoto; ?>" alt="<?php echo $establishmentName; ?>" class="profile-pic">
                                                        <div class="profile-info">
                                                            <h3 class="profile-name"><a href="/bookingapp/establishment/establishment.php?est=<?php echo base64_encode($establishmentID); ?>"><?php echo $establishmentName; ?></a></h3>
                                                            <p class="profile-role">
                                                                <?php
                                                                    echo "$roomName<br>";
                                                                ?>
                                                            </p>
                                                        </div>
                                                        <span class="toggle-icon"><i class="fa-solid fa-caret-down"></i></span>
                                                    </div>

                                                    <div class="profile-details hidden">
                                                        <p><strong>Room ID:</strong> <?php echo $roomID; ?></p>
                                                        <p><strong>Establishment Type:</strong> <?php echo $establishmentType; ?></p>
                                                        <p><strong>Room Type:</strong> <?php echo $roomType; ?></p>
                                                        <p><strong>Booking date:</strong> <?php echo $bookingDate; ?></p>
                                                        <p><strong>Residency started on:</strong> <?php echo $dateOfEntry; ?></p>
                                                        <p><strong>Residency ended on:</strong> <?php echo $dateOfExit; ?></p>
                                                        <p><strong>Status:</strong> <?php echo $residencyStatus; ?></p>
                                                    </div>

                                                    <div class="profile-actions">
                                                        <button class="btn" onclick="redirect('/bookingapp/establishment/establishment.php?est=<?php echo $encryptedEstID; ?>')"><i class="fa-solid fa-eye"></i> View Establishment</button>
                                                        <!-- <button class="btn btn-secondary"><i class="fa-solid fa-edit"></i> Edit</button> -->
                                                    </div>                    
                                                </div>
                                    
                                                
                                    <?php
                                            }
                                    ?>
                                    <?php
                                        } else {
                                            echo "<p>No data found for this tab.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="residency_history">
                            <div class="tab-content">

                            <h3>Residency history</h3>
                            <?php echo "<p>Showing $currentResidencyCount result(s).</p>"; ?>
                                
                            </div>
                        </div>

                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
        
    </div>

    <?php if ($isUserProfile) { ?>
        <div class="modal" id="passwordModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('passwordModal')" title="Click to close this dialog.">&times;</span>
            <h3 style="color: black">Enter your password to confirm changes.</h3>

            <!-- Password input with toggle button overlaid -->
                <div class="password-container">
                    <input type="password" name="password" id="passwordModalInput" placeholder="Enter your password" onkeydown="enterPasswordModal(event);" required>
                    <span class="toggle-password" id="togglePassword" onclick="togglePassword('togglePassword')">
                        <i id="toggleIcon" title="Click to show the password." class="fas fa-eye slash"></i>
                    </span>
                </div>
                <button type="submit" style="margin-top: 10px;" class="btn-primary" name="login">Confirm</button>
            </div>
        </div>
    <?php } ?>

    
    <div id="toastBox"></div>

    <!-- Footer -->
    <?php include "../php/footer.php"; ?>



    <!-- <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script> -->
    <script src="/bookingapp/js/jquery-1.10.2.min.js"></script>
    <script src="/bookingapp/js/bootstrap.bundle.min.js"></script>
    <script src="/bookingapp/js/script.js"></script>
    <script src="/bookingapp/js/scrollreveal.js "></script>
    <script src="/bookingapp/js/rayal.js"></script>
    <script type="text/javascript"></script>

    <script>


        function toggleDetails(header) {
            const details = header.nextElementSibling;
            const icon = header.querySelector(".toggle-icon");

            if (details.classList.contains("open")) {
                details.classList.remove("open");
                icon.classList.remove("open");
            } else {
                details.classList.add("open");
                details.classList.add("open");
            }
        }

        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();

        // Password

        function enterPassword(event) {
            if (event.key === "Enter" || event.keyCode === 13) {
                event.preventDefault();
                submitLogin();
            }
        }

        // Modal functionalities
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }


        function togglePassword(inputID, toggleID) {
            const passwordInput = document.getElementById(inputID);
            const toggle = document.getElementById(toggleID);

            // Toggle password visibility
            if (passwordInput.type == 'password') {
                passwordInput.type = 'text';
                toggle.classList.add('fa-eye-slash');
                toggle.classList.remove('fa-eye');
                toggle.setAttribute('title', 'Click to hide the password.');
            } else {
                passwordInput.type = 'password';
                toggle.classList.add('fa-eye');
                toggle.classList.remove('fa-eye-slash');                
                toggle.setAttribute('title', 'Click to show the password.');

            }
        }

        function enterPasswordModal(event) {
            if (event.key === "Enter" || event.keyCode === 13) {
                event.preventDefault();
                closeModal('passwordModal');
            }
        }

        const generalPrompt = document.getElementById('general-prompt');

        function editGeneral() {
            const role = document.getElementById('role').value;

            document.getElementById('edit-general').hidden = true;

            document.getElementById('profile-picture-settings').hidden = false;
            document.getElementById('email-address').disabled = false;
            document.getElementById('username').disabled = false;
            document.getElementById('first-name').disabled = false;
            document.getElementById('middle-name').disabled = false;
            document.getElementById('last-name').disabled = false;
            document.getElementById('ext-name').disabled = false;

            if (role === 'owner' || role === 'admin') {
                document.getElementById('institution').disabled = false;
                document.getElementById('position-title').disabled = false;
            }

            generalPrompt.innerHTML = "<i class='fa-solid fa-circle-check'></i> You can edit now!";
            generalPrompt.style.color = "green";

            setTimeout(() => {
                generalPrompt.innerHTML = "";
            }, 3000);
            
            document.getElementById('general-btns').hidden = false;
        }

        function cancelEditingGeneral() {
            const role = document.getElementById('role').value;

            document.getElementById('edit-general').hidden = false;

            document.getElementById('profile-picture-settings').hidden = true;
            document.getElementById('email-address').disabled = true;
            document.getElementById('username').disabled = true;
            document.getElementById('first-name').disabled = true;
            document.getElementById('middle-name').disabled = true;
            document.getElementById('last-name').disabled = true;
            document.getElementById('ext-name').disabled = true;

            if (role === 'owner' || role === 'admin') {
                document.getElementById('institution').disabled = true;
                document.getElementById('position-title').disabled = true;
            }

            generalPrompt.innerHTML = "<i class='fa-solid fa-ban'></i> Editing cancelled";
            generalPrompt.style.color = "red";

            setTimeout(() => {
                generalPrompt.innerHTML = "";
            }, 3000);
            
            document.getElementById('general-btns').hidden = true;

        }

        const infoPrompt = document.getElementById('info-prompt');

        document.addEventListener("DOMContentLoaded", () => {
            cancelEditingPassword();

            document.getElementById('contact').addEventListener('input', function() {
                if (!validateContact(contact.value)) {
                    infoPrompt.innerHTML = "<i class='fa-solid fa-circle-xmark'></i> Invalid contact format.";
                    infoPrompt.style.color = "red";
                } else {
                    infoPrompt.innerHTML = "<i class='fa-solid fa-circle-check'></i> Correct contact format.";
                    infoPrompt.style.color = "green"; 
                }
            });
        });

        // document.getElementById('submit-password-btn').addEventListener("click", () => {
        //     cancelEditingPassword();
        // });

        // Edit Password
        function editPassword() {
            document.getElementById('edit-password').hidden = true;
            document.getElementById('current-password').hidden = false;
            document.getElementById('new-password').hidden = false;
            document.getElementById('confirm-password').hidden = false;
            document.getElementById('password-btns').hidden = false;
        }

        function cancelEditingPassword() {
            document.getElementById('edit-password').hidden = false;      
            document.getElementById('current-password').hidden = true;
            document.getElementById('new-password').hidden = true;
            document.getElementById('confirm-password').hidden = true;
            document.getElementById('password-btns').hidden = true;
        }

        // Edit Information
        function editInfo() {
            document.getElementById('edit-info').hidden = true;
            document.getElementById('gender').disabled = false;
            document.getElementById('birthday').disabled = false;
            document.getElementById('religion').disabled = false;
            document.getElementById('contact').disabled = false;
            document.getElementById('address').disabled = false;
            document.getElementById('info-btns').hidden = false;
        }

        function cancelEditingInfo() {
            document.getElementById('edit-info').hidden = false;
            document.getElementById('gender').disabled = true;
            document.getElementById('birthday').disabled = true;
            document.getElementById('religion').disabled = true;
            document.getElementById('contact').disabled = true;
            document.getElementById('address').disabled = true;
            document.getElementById('info-btns').hidden = true;
        }

        // Edit social links
        function editSocial() {
            document.getElementById('edit-social').hidden = true;
            document.getElementById('social-facebook').disabled = false;
            document.getElementById('social-twitter').disabled = false;
            document.getElementById('social-instagram').disabled = false;
            document.getElementById('social-youtube').disabled = false;
            document.getElementById('social-tiktok').disabled = false;
            document.getElementById('social-linkedin').disabled = false;
            document.getElementById('social-website').disabled = false;
            document.getElementById('social-btns').hidden = false;
        }

        function cancelEditingSocial() {
            document.getElementById('edit-social').hidden = false;
            document.getElementById('social-facebook').disabled = true;
            document.getElementById('social-twitter').disabled = true;
            document.getElementById('social-instagram').disabled = true;
            document.getElementById('social-youtube').disabled = true;
            document.getElementById('social-tiktok').disabled = true;
            document.getElementById('social-linkedin').disabled = true;
            document.getElementById('social-website').disabled = true;
            document.getElementById('social-btns').hidden = true;
        }

        // Profile Pic
        function resetPicture() {
            const preview = document.getElementById('profile-picture-preview');
            const gender = document.getElementById('gender').value;
            showToast('image', 'Profile picture is removed.', 'warning');
            preview.src = `/bookingapp/user/${gender}-no-face.jpg`;

            document.getElementById('fileInput').value = null;
            generalPrompt.innerHTML = "<i class='fa-solid fa-image'></i> Profile picture is removed.";
            generalPrompt.style.color = "grey";

            setTimeout(() => {
                generalPrompt.innerHTML = "";
            }, 3000);
        }

        <?php if ($isUserProfile) { ?>

        document.getElementById("fileInput").addEventListener("change", function() {
            const fileInput = document.getElementById("fileInput");
            const imagePreview = document.getElementById("profile-picture-preview");

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = "block";
                }

                reader.readAsDataURL(fileInput.files[0]);
            } else {
                resetPicture();
            }
        });

        <?php } ?>

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
                    infoPrompt.innerHTML = "Contact number must be at least 11 digits.";
                }
            } else {
                // Display error message if the input is empty
                infoPrompt.innerHTML = "Contact number is required.";
            }
        

            return result;
        }

        var theToday = new Date().toISOString().split('T')[0];

            // Set date input limits (MAX or MIN)
            function setDateLimit(inputID, date, limit) {
                document.getElementById(inputID).setAttribute(limit, date);
            }

            setDateLimit('birthday', theToday, 'max');
    </script>
</body>
</html>