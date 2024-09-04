<?php

/**
 * The EkiliRelay class is designed to handle email sending functionality
 * using a provided API key. It connects to a remote API endpoint and sends
 * email requests based on the given parameters.
 * 
 * This class should be initialized with an API key which is used for
 * authenticating requests to the email service.
 */
class EkiliRelay {
    private $apikey; // API key for authentication
    private $apiUrl; // URL of the API endpoint

    /**
     * Constructs an instance of the EkiliRelay class.
     * 
     * @param string $apikey The API key required for authenticating
     * requests to the email service.
     */
    public function __construct($apikey) {
        // Store the API key for later use in API requests
        $this->apikey = $apikey;
        // Define the URL of the API endpoint where email requests will be sent
        $this->apiUrl = "https://relay.ekilie.com/api/index.php";
        // Log a message indicating the successful connection
        // echo "EkiliRelay connected\n";
    }

    /**
     * Sends an email using the provided details.
     * 
     * @param string $to The recipient's email address.
     * @param string $subject The subject of the email.
     * @param string $message The body of the email.
     * @param string $headers Optional additional headers for the email.
     * @return array The result of the email sending operation.
     */
    public function sendEmail($to, $subject, $message, $headers = '') {
        // Construct the payload to be sent to the API
        $data = [
            'to' => $to,               // Recipient's email address
            'subject' => $subject,     // Subject line of the email
            'message' => $message,     // Body of the email
            'headers' => $headers,     // Optional additional headers
            'apikey' => $this->apikey  // API key for authentication
        ];

        // Initialize a cURL session
        $ch = curl_init($this->apiUrl);
        
        // Set the necessary options for the POST request
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json' // Specifying that we are sending JSON data
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Converting the data array to a JSON string

        // Executes the request and capture the response
        $response = curl_exec($ch);

        // Checks for cURL errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            // Returns an error array if something goes wrong
            return ['status' => 'error', 'message' => $error];
        }

        // Closes the cURL session
        curl_close($ch);

        // Decodes the JSON response from the server
        $result = json_decode($response, true);

        // Returns the result of the email sending operation
        return $result;
    }
}
