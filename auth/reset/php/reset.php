<?php
session_start();
require "../../../vendor/autoload.php";
require "../../../config.php";
require "../../../utils.php";

$email = mysqli_real_escape_string($conn, $_POST["email"]);

if (!empty($email)) {
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);

        $otp = Utils::generateOTP($conn, $email);
        if ($otp == "exists") {
            echo "Check you emails for the reset password link";
        }else if($otp == "failed"){
            Utils::logErrors("Something went wrong. Please try again later", "reset.php");
            echo "Something went wrong. Please try again later";
            //Add in a way to log errors
        }
        // Sending the OTP to the user's email for password reset logic
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:ekiliRelay <support@ekilie.com>";
        if (
            mail(
                $email,
                "Password Reset -> ekiliRelay",
                Utils::passwordResetEmailTemplate($otp, $email, $row["name"]),
                $headers
            )
        ) {
            // echo "success"; #I Will do something else here
        }
    } else {
        echo "No account was found with email $email";
    }
} else {
    echo "Email is required";
}

?>
