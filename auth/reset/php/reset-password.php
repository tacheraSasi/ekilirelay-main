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
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

} else {
    echo "All inputs are required";
}

?>
