<?php 
session_start();
include_once "../../../config.php";


$email = mysqli_real_escape_string($conn, $_POST['email']);

if(!empty($email)){
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
    
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        
    } else {
        echo "No account was found with email $email";
    }
} else {
    echo "Email is required";
}
?>
