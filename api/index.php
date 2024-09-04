<?php
# Including the configuration file for the database connection
include_once 'config.php';

# Allowing cross-origin requests (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

# Handling preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    # This is a preflight request. Reply with 200 OK and return.
    http_response_code(200);
    exit();
}

# Setting the content type to JSON
header('Content-Type: application/json');

# Function for validating an email address
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

# Function for logging messages to a file
function logMessage($message) {
    file_put_contents('../logs/email.log', $message . PHP_EOL, FILE_APPEND);
}

# Checking if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    # Decoding the incoming JSON payload into an associative array
    $data = json_decode(file_get_contents('php://input'), true);

    # Checking if the required parameters are present
    if (isset($data['to'], $data['subject'], $data['message'], $data['apikey'])) {

        # Escaping the API key to prevent SQL injection
        $apikey = mysqli_real_escape_string($conn, $data['apikey']);
        
        # Querying the user associated with the provided API key
        $select = mysqli_query($conn, "SELECT user FROM data WHERE api_key = '$apikey';");
        
        # Verifying if the API key is valid
        if ($select && mysqli_num_rows($select) > 0) {
            # Retrieving the user ID from the query result
            $user_id = mysqli_fetch_array($select)['user'];

            # Retrieve the current number of requests for the user
            $select_num_req = mysqli_query($conn, "SELECT requests FROM data WHERE user = '$user_id'");

            if ($select_num_req) {
                # Fetch the current number of requests
                $num_req = mysqli_fetch_assoc($select_num_req)['requests'];
                
                # Increment the number of requests
                $num_req++;
                
                # Update the number of requests in the database
                $update_req = mysqli_query($conn, "UPDATE data SET requests = $num_req WHERE user = '$user_id'");
            }
            
            # Querying the user's information based on the unique ID
            $select_user = mysqli_query($conn, "SELECT name, email FROM users WHERE unique_id = '$user_id';");
            $user_info = mysqli_fetch_array($select_user);

            $user_name = $user_info['name'];
            $user_email = $user_info['email'];

            # Setting the email fields from the input data
            $to = $data['to'];
            $subject = $data['subject'];
            $message = $data['message'];
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= isset($data['headers']) ? $data['headers'] : "From: $user_name <$user_email>";

            # Validating the recipient's email address
            if (!validateEmail($to)) {
                $response = ['status' => 'error', 'message' => 'Invalid email address.'];
                echo json_encode($response);
                logMessage(json_encode($response));
                exit;
            }

            # Attempting to send the email
            if (mail($to, $subject, $message, $headers)) {
                # Retrieve the current number of emails_sent for the user
                $select_num_sent = mysqli_query($conn, "SELECT emails_sent FROM data WHERE user = '$user_id'");

                if ($select_num_sent) {
                    # Fetch the current number of emails_sent
                    $num_sent = mysqli_fetch_assoc($select_num_sent)['emails_sent'];
                    
                    # Increment the number of emails_sent
                    $num_sent++;
                    
                    # Update the number of emails_sent in the database
                    $update_req = mysqli_query($conn, "UPDATE data SET emails_sent = $num_sent WHERE user = '$user_id'");
                }
                
                $response = ['status' => 'success', 'message' => 'Email sent successfully.'];
                echo json_encode($response);
                logMessage(json_encode($response));
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to send email.'];
                echo json_encode($response);
                logMessage(json_encode($response));
            }

        } else {
            # Responding with an error if the API key is invalid
            $response = ['status' => 'error', 'message' => 'Invalid API key. Visit https://relay.ekilie.com to get the correct one.'];
            echo json_encode($response);
        }

    } else {
        # Responding with an error if required parameters are missing
        $response = ['status' => 'error', 'message' => 'Missing parameters (to, subject, message, or apikey).'];
        echo json_encode($response);
        logMessage(json_encode($response));
    }
    
} else {
    # Responding with an error if the request method is not POST
    $response = ['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.'];
    echo json_encode($response);
    logMessage(json_encode($response));
}
?>
