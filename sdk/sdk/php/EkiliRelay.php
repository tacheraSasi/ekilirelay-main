<?php
// SDK/PHP/EkiliRelay.php

class EkiliRelay
{
    private $apiUrl;

    public function __construct($apiUrl)
    {
        $this->apiUrl = "https://relay.ekilie.com/api/index.php";
    }

    public function sendEmail($to, $subject, $message, $headers = '')
    {
        $data = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'headers' => $headers
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
?>
