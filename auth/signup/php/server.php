<?php
session_start();
include_once "../../config.php";

require '../../vendor/autoload.php';

$username = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm-password']);

if (!empty($username) && !empty($email)  && !empty($password)) {
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}' 
           OR name = '{$username}'");
    if (mysqli_num_rows($sql) > 0) {
        echo "username $username is already taken.";
    } else {
        $ran_id = rand(time(), 700000000);
        if($password == $confirmPassword){
            $encrypt_pass = md5($password);
            $insert_query = mysqli_query($conn, 
            "INSERT INTO users (unique_id, name, email, auth)
            VALUES ({$ran_id}, '{$username}', '{$email}', '{$encrypt_pass}')");
            
            $check = mysqli_query($conn, 
            "SELECT * FROM users WHERE email = '{$email}' AND name = '{$username}'");
            if(mysqli_num_rows($check)>0){
                $result = mysqli_fetch_assoc($check);
                $_SESSION['relay_user'] = $result['unique_id'];
                if(isset($_SESSION['relay_user'])){
                    //creating an api key
                    $apiKey = generateApiKey();
                    $relay_user_uid = $result['unique_id'];
                    $insert_query = mysqli_query($conn, 
                    "INSERT INTO data (user, api_key)
                    VALUES ({$relay_user_uid}, '{$apiKey}')");

                    //send an email
                    sendWelcomeMail($username,$email);

                }else{
                    echo 'something went wrong. Please try again later';
                }
            }else{
                echo'something went wrong. Please try again later.';
            }
        } else{
            echo'password does not match!';
        } 
    }
} else {
    echo "All input fields are required!";
}

function generateApiKey(){
    $prefix = 'relay-';
    $length = 32 - strlen($prefix);
    $apiKey = $prefix . bin2hex(random_bytes($length / 2));
    return $apiKey;

}

function sendWelcomeMail($username,$email){
    $to = $email;
    $subject = "Welcome to ekiliRelay!";
    $message =  '
    <html>
        <body style="background-color: #11171a; color: #d9f8e9; font-family: Arial, sans-serif; padding: 20px;">
            <div style="max-width: 600px; margin: auto; background-color: #1c2428; padding: 20px; border-radius: 10px;">
                <h1 style="color: #6FB98F; text-align: center;">Welcome to EkiliRelay!</h1>
                <p style="font-size: 16px; line-height: 1.6;">
                    Hello <strong>'.$username.' </strong>,<br><br>
                    We\'re excited to welcome you to EkiliRelay, the powerful email API designed specifically for developers. Seamlessly integrate email functionality into your applications with reliability, security, and ease.
                    <br><br>
                    Stay motivated and consistent on your development journey. Explore our resources to enhance your skills and maximize the power of EkiliRelay.
                    <br><br>
                    Visit our website for tips, documentation, and more at <a href="https://relay.ekilie.com/docs" style="color: #6FB98F; text-decoration: none;">relay.ekilie.com/docs</a>.
                </p>
                <div style="margin-top: 30px; border-top: 2px solid #6FB98F; padding-top: 20px; text-align: center;">
                    <img src="https://ekilie.com/assets/img/favicon.jpeg" alt="Ekilie Logo" style="width: 80px; height: 80px; border-radius: 50%; background-color: #9FE2BF; padding: 10px;">
                    <p style="color: #6FB98F; margin-top: 10px; font-size: 14px;">ekiliRelay</p>
                    <p style="color: #5b8373; font-size: 12px;">Empowering your emails, one API at a time.</p>
                </div>
            </div>
        </body>
    </html>
    ';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ekiliRelay  <support@ekilie.com>";
    
    if (mail($to, $subject, $message, $headers)) {
        echo "success";
    } else {
        echo "Email sending failed!";
    }

}

