<?php 

$loggedIn = false;
$personID = null;  
$accountRole = null;
$userID = null;

$userPerson = array();

$owner = isset($_SESSION['owner']) ? $_SESSION['owner'] : array();
$admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : array();
$tenant = isset($_SESSION['tenant']) ? $_SESSION['tenant'] : array();

if (isset($_SESSION['userID'])) {
    $loggedIn = true;
    $userID = $_SESSION['userID'];

    // echo json_encode($conn);

    // Get user account
    $sql = "SELECT * FROM user_account WHERE UserID = $userID";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        $emailAddress = $row['EmailAddress'];
        $isEmailVerified = $row['IsEmailVerified'];

        $username = $row['Username'];
        $password = $row['Password'];
        
        $dateCreated = $row['DateCreated'];
        $accountRole = $row['Role'];

        $personID = $row['PersonID'];

    }

    // Get person information
    $sql = "SELECT * FROM person WHERE PersonID = $personID";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        $userPerson = $row;

        $profilePicture = $row['ProfilePicture'];
        
        $firstName = $row['FirstName'];
        $middleName = $row['MiddleName'];
        $lastName = $row['LastName'];
        $extName = $row['ExtName'];
        
        $gender = $row['Gender'];
        $dateOfBirth = $row['DateOfBirth'];

        $contact = $row['ContactNumber'];
        $isContactVerified = $row['IsContactVerified'];
        
        $homeAddress = $row['HomeAddress'];
        $religion = $row['Religion'];

        $profilePicture = $row['ProfilePicture'];
        
        if (empty($profilePicture)) {
            $profilePicture = "/bookingapp/user/$gender-no-face.jpg";
        } 
    }

    $isLoggedIn = true;

    if ($accountRole === 'tenant') {
        $sql = "SELECT * FROM tenant WHERE UserID = $userID";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['tenant'] = mysqli_fetch_assoc($result);
        }
    } else if ($accountRole === 'owner') {
        $sql = "SELECT * FROM establishment_owner WHERE UserID = $userID";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['owner'] = mysqli_fetch_assoc($result);
        }
    } else if ($accountRole === 'admin') {
        $sql = "SELECT * FROM admin WHERE UserID = $userID";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['admin'] = mysqli_fetch_assoc($result);
        }
    }
}

// Get role information

?>

<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="shortcut icon" href="/bookingapp/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="/bookingapp/css/style.css">
<link rel="stylesheet" href="/bookingapp/css/remixicon.css">
<link rel="stylesheet" href="/bookingapp/css/search.css">


<script defer src="/bookingapp/assets/fontawesome/js/brands.js"></script>
<script defer src="/bookingapp/assets/fontawesome/js/solid.js"></script>
<script defer src="/bookingapp/assets/fontawesome/js/regular.js"></script>
<script defer src="/bookingapp/assets/fontawesome/js/fontawesome.js"></script>

<script defer src="/bookingapp/js/script.js"></script>
