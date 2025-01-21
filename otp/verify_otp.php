<?php

session_start();

if (isset($_POST['verifyOTP'])) {
    $otp1 = mysqli_real_escape_string($conn, $_POST['otp-1']);
    $otp2 = mysqli_real_escape_string($conn, $_POST['otp-2']);
    $otp3 = mysqli_real_escape_string($conn, $_POST['otp-3']);
    $otp4 = mysqli_real_escape_string($conn, $_POST['otp-4']);
    $otp5 = mysqli_real_escape_string($conn, $_POST['otp-5']);
    $otp6 = mysqli_real_escape_string($conn, $_POST['otp-6']);

    $otp = $otp1 . $otp2 . $otp3 . $otp4 . $otp5 . $otp6;
    $userID = mysqli_real_escape_string($conn, $_SESSION['userID']);

    try {
        // Check OTP
        $sql = "SELECT * FROM otp_verifications WHERE UserID = ? AND OTP_Code = ? AND ExpiresAt > NOW()";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statemenet error in OTP verification" .   mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ii", $userID, $otp);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error in OTP verification" . mysqli_stmt_error($stmt));
        }

        $results = mysqli_num_rows($stmt);

        if ($results > 0) {
            // Update user account
            $otpSql = "UPDATE user_account SET IsEmailVerified = 1 WHERE UserID = ?";
            $otpStmt = mysqli_prepare($conn, $otpSql);

            if (!$otpStmt) {
                throw new Exception("Prepared statement error in changing the email verification status of the user account. " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($otpStmt, "i", $userID);

            if (!mysqli_stmt_execute($otpStmt)) {
                throw new Exception("Statement execution error in changing the email verification status of the user account. " . mysqli_stmt_error($otpStmt));
            }

            $successMsg = "Email is successfully verified.";
        } else {
            $errorMsg = "Invalid or expired OTP";
        }
        
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
    }
    exit;
}