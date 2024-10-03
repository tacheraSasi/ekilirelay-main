<?php
require './EkiliRelay.php';
use EkiliRelay;
if(isset($_POST["send"])){
    $ekiliRelay = new EkiliRelay("relay-6087f8c42d70f0650b9f023adc");
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $response = $ekiliRelay->sendEmail(
        $to,
        $subject,
        $message,
        "From: tach <tacherasasi@gmail.com>"
    );
    print_r($response);
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EkiliRelay test</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="to" placeholder="enter the recipient">
        <input type="text" name="subject" placeholder="enter the subject">
        <input type="text" name="message"  placeholder="enter the message">
        <input type="submit" name="send" value="send">
        <pre>
            <?=isset($reponse)?$response:"opps";?>
        </pre>
    </form>
</body>
</html>