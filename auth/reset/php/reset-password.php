<?php
session_start();
require "../../../vendor/autoload.php";
require "../../../config.php";
require "../../../utils.php";

$email = mysqli_real_escape_string($conn, $_POST["email"]);
$otp = mysqli_real_escape_string($conn, $_POST["otp"]);
$newPassword = mysqli_real_escape_string($conn, $_POST["new-password"]);
$cpassword = mysqli_real_escape_string($conn, $_POST["cpassword"]);

if (!empty($email) && !empty($otp) && !empty($newPassword) && !empty($cpassword)) {
    $hashedPass = Utils::hashPassword($newPassword);
    $sql = mysqli_query($conn, "UPDATE users SET password = '{$hashedPass}' WHERE email = '{$email}'");
    
    if($sql){
        echo "success";
    }else{
        Utils::logErrors("The update password query failed,\nSomething went wrong. Please try again later", "reset-password.php");
        echo "Something went wrong. Please try again later";
    }

} else {
    echo "All inputs are required";
}

?>
