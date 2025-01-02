<?php
class Utils
{
    public static function generateOTP($conn, $email): string
    {
        #checking if the email already has an OTP
        $sql = mysqli_query(
            $conn,
            "SELECT * FROM otp WHERE email = '{$email}'"
        );
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
    public static function logErrors($msg, $location): void
    {
        $email = "support@ekilie.com";
        $subject = "ERROR ekiliRelay";
        $time = date("Y-m-d H:i:s");
        $message = "An error occurred at $location on $time with the message: $msg";
        $headers = "From: ERROR_ekiliRelay <errors@ekilie.com>";
        mail($email, $subject, $message, $headers);
    }
    public static function passwordResetEmailTemplate($otp, $email, $username) {
        $resetUrl = 'https://relay.ekilie.com/auth/reset/reset-password.php?otp=' . urlencode($otp) . '&email=' . urlencode($email);
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Password Reset</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    padding: 10px 0;
                    background-color: #007bff;
                    color: #ffffff;
                }
                .content {
                    padding: 20px;
                }
                .footer {
                    text-align: center;
                    padding: 10px 0;
                    background-color: #f4f4f4;
                    color: #777777;
                    font-size: 12px;
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    margin: 20px 0;
                    background-color: #007bff;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Password Reset Request</h1>
                </div>
                <div class="content">
                    <p>Hi ' . htmlspecialchars($username) . ',</p>
                    <p>We received a request to reset your password for your account associated with ' . htmlspecialchars($email) . '.</p>
                    
                    <p>Please use The button below to reset your password. If you did not request a password reset, please ignore this email.</p>
                    <p>Thank you,</p>
                    <p>The ekiliRelay Team</p>
                    <a href="' . $resetUrl . '" class="button">Reset Password</a>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ekiliRelay. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        return $html;
    }
    
    public function hashPassword($password): string
    {
        return md5($password);//TODO:will later change to a more secure hashing algorithm
    }
    
    public function verifyPassword($password, $hash): bool
    {
        return md5($password) === $hash;
    }
}
