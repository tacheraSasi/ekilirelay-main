<?php
class Utils{
    public static function generateOTP($conn, $email):string{
        #checking if the email already has an OTP
        $sql = mysqli_query($conn, "SELECT * FROM otp WHERE email = '{$email}'");
        if (mysqli_num_rows($sql) > 0) {
            // $row = mysqli_fetch_assoc($sql);
            return "exists";
        } // Also delete the previous OTP if it has expires

        $prefix = "otp_";
        $length = 10 - strlen($prefix);
        $otp = $prefix . bin2hex(random_bytes($length / 2));
        $currentTimestamp = time();
        $expires = strtotime("+7 days", $currentTimestamp);
        $expires = date("Y-m-d H:i:s", $expires);
        $sql = "INSERT INTO otp (email,value,expires_at) VALUES ('$email','$otp','$expires')";

        if (mysqli_query($conn, $sql)) {
            return $otp;
        } else {
            return "failed";
        }
    }
    public static function logErrors($msg,$location):void{
    $email = "support@ekilie.com";
    $subject = "ERROR ekiliRelay";
    $time = date("Y-m-d H:i:s");
    $message = "An error occurred at $location on $time with the message: $msg";
    $headers = "From: ERROR_ekiliRelay <support@ekilie.com>";
    mail($email,$subject,$message,$headers);
}
}
