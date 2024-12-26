<?php
session_start();
require '../../../vendor/autoload.php';
include_once "../../../config.php";


$email = mysqli_real_escape_string($conn, $_POST['email']);

if(!empty($email)){
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);

        $otp = generateOTP($conn,$email);
        // Send the OTP to the user's email for password reset logic
    } else {
        echo "No account was found with email $email";
    }
} else {
    echo "Email is required";
}

function generateOTP($conn,$email){
    $prefix = 'ekilie-';
    $length = 16 - strlen($prefix);
    $otp = $prefix . bin2hex(random_bytes($length / 2));
    $sql = "INSERT INTO otp (email,value) VALUES ('$email','$otp')";
    return $otp;
}
?>