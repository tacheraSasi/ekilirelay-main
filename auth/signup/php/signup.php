<?php
session_start();
include_once "../../../php/config.php";
$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$lname = mysqli_real_escape_string($conn, $_POST['lname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {

    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
    if (mysqli_num_rows($sql) > 0) {
        echo "$email - already exists!";
    } else {
        if (isset($_FILES['image'])) {
            $img_name = $_FILES['image']['name'];
            $img_type = $_FILES['image']['type'];
            $tmp_name = $_FILES['image']['tmp_name'];
            
            $img_explode = explode('.', $img_name);
            $img_ext = end($img_explode);

            $extensions = ["jpeg", "png", "jpg", "JPG", "PNG", "JPEG"];
            if (in_array($img_ext, $extensions) === true) {
                $types = ["image/jpeg", "image/JPEG", "image/jpg", "image/JPG", "image/png", "image/PNG"];
                if (in_array($img_type, $types) === true) {
                    $time = time();
                    $space = '_';
                    $new_img_name = $time . $space . $fname . $space . $lname;

                    if (move_uploaded_file($tmp_name, "../../../php/images/" . $new_img_name . '.' . $img_ext)) {
                        $ran_id = rand(time(), 100000000);
                        $status = "Active now";
                        $encrypt_pass = md5($password);
                        $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}.{$img_ext}', '{$status}')");

                        if ($insert_query) {
                            $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                            if (mysqli_num_rows($select_sql2) > 0) {
                                $result = mysqli_fetch_assoc($select_sql2);
                                $_SESSION['unique_id'] = $result['unique_id'];
                                 // Sending email to the user
                                      // Prepare and send email to the user
                                                        
                                    $to = $email;
                                    $subject = "Welcome to Ekilie!";
                                    $message = "
                                    <html>
                                    <body style='background-color: #36454F; color: #9FE2BF; font-family: Arial, sans-serif; padding: 20px;'>
                                        <h1>Stay Consistent and Motivated!</h1>
                                        <p>
                                            Hello $fname $lname,<br><br>
                                            Welcome to Ekilie! We're thrilled to have you on board, and we believe in your journey towards success. Consistency is the key, and motivation fuels your progress.
                                            Embrace each day with determination, and you'll achieve great things.
                                            <br><br>
                                            Your registration details:<br>
                                            Unique ID: $result[unique_id]<br>
                                            Email: $email<br>
                                            Password: $password<br><br>
                                            Explore our latest resources and tips on staying focused and motivated on your path to success.
                                            Visit our website at <a href='https://www.ekilie.com' style='color: #6FB98F; text-decoration: none;'>www.ekilie.com</a>.
                                        </p>
                                        <div style='margin-top: 30px; border-top: 2px solid #6FB98F; padding-top: 20px; text-align: center;'>
                                            <img src='https://ekilie.com/assets/img/favicon.jpeg' alt='Ekilie Logo' style='width: 100px; height: 100px; border-radius: 50%; background-color: #9FE2BF; padding: 10px;'>
                                            <p style='color: #6FB98F; margin-top: 10px;'>ekilie </p>
                                        </div>
                                    </body>
                                    </html>";
                                    
                                    $headers = "MIME-Version: 1.0" . "\r\n";
                                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                                    $headers .= "From: ekilie  <support@ekilie.com>";
                                    
                                    // Sending email using mail() function
                                    if (mail($to, $subject, $message, $headers)) {
                                        echo "success";
                                    } else {
                                        echo "Email sending failed!";
                                    }
                   }
                        } else {
                            echo "Something went wrong. Please try again!";
                        }
                    }
                } else {
                    echo "$fname $lname, please upload an image file - jpeg, png, jpg, JPG";
                }
            } else {
                echo "$fname $lname, please upload an image file - jpeg, png, jpg, JPG";
            }
        }
    }
} else {
    echo "All input fields are required!";
}
?>