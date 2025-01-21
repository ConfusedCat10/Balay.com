<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//required files
require '/bookingapp/assets/phpmailer/src/Exception.php';
require '/bookingapp/assets/phpmailer/src/PHPMailer.php';
require '/bookingapp/assets/phpmailer/src/SMTP.php';

//Create an instance; passing `true` enables exceptions
if (isset($_POST["send"])) {

  $mail = new PHPMailer(true);

    //Server settings
    $mail->isSMTP();                              //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';       //Set the SMTP server to send through
    $mail->SMTPAuth   = true;             //Enable SMTP authentication
    $mail->Username   = 'youremail@gmail.com';   //SMTP write your email
    $mail->Password   = 'bgjnkklncdmbjvga';      //SMTP password
    $mail->SMTPSecure = 'ssl';            //Enable implicit SSL encryption
    $mail->Port       = 465;                                    

    //Recipients
    $mail->setFrom( "dkboystudios@gmail.com", "Balay.com"); // Sender Email and name

    //Content
    $mail->isHTML(true);               //Set email format to HTML
    $mail->Subject = $_POST["subject"];   // email subject headings
    $mail->Body    = "
    <html>
    <head>
        <title>OTP Verification</title>
    </head>
    <body>
        <p>Dear User,</p>
        <p>Thank you for signing up. To verify your email address, please use the following OTP code:</p>
        <h2>$otpCode</h2>
        <p>This code is valid for 10 minutes. Please do not share this code with anyone.</p>
        <p>Thank you,<br>Your Company Name</p>
    </body>
    </html>
    "; //email message

    // Success sent message alert
    $mail->send();
    echo
    " 
    <script> 
     alert('Message was sent successfully!');
     document.location.href = 'index.php';
    </script>
    ";
}
?>