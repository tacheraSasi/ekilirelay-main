<?php
session_start();
require "../../../vendor/autoload.php";
require "../../../config.php";

$email = mysqli_real_escape_string($conn, $_POST["email"]);

if (!empty($email)) {
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);

        $otp = generateOTP($conn, $email);
        // Sending the OTP to the user's email for password reset logic

        if (
            mail(
                $email,
                "Password Reset -> ekiliRelay",
                "Your OTP is $otp",
                "From:ekiliRelay <support@ekilie.com>"
            )
        ) {
            echo "success";
        }
    } else {
        echo "No account was found with email $email";
    }
} else {
    echo "Email is required";
}

function generateOTP($conn, $email){
    $prefix = "ekilie-";
    $length = 16 - strlen($prefix);
    $otp = $prefix . bin2hex(random_bytes($length / 2));
    $sql = "INSERT INTO otp (email,value) VALUES ('$email','$otp')";
    if (mysqli_query($conn, $sql)) {
        return $otp;
    } else {
        echo "Something went wrong. Please try again later";
        return $otp;
    }
}

function emailTemplate(){
}
?>
