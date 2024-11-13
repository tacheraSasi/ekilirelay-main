<?php
include_once '../config.php';
include_once "configurations.php";

# Allowing cross-origin requests (CORS)
# Headers to allow any domain to access this API, which is helpful for public APIs
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

# Handling preflight OPTIONS request
# If the request method is OPTIONS, it's likely a preflight request made by the browser, so I return 200 OK and stop further processing
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);  # Replying with 200 OK for the preflight check
    exit();  # Exiting early since no further processing is needed
}

# Setting the content type to JSON
# I ensure that the response will be sent as JSON
header('Content-Type: application/json');

# Function for validating an email address
# This function checks if the provided email is in a valid format
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

# Function for logging messages to a file
function logMessage($message)
{
    file_put_contents('../logs/email.log', $message . PHP_EOL, FILE_APPEND);
}

# Checking if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    # Decoding the incoming JSON payload into an associative array
    # I read the raw input (JSON) and convert it into an array for easier handling
    $data = json_decode(file_get_contents('php://input'), true);

    # Checking if the required parameters are present
    # Here, I check if the necessary fields (to, subject, message, apikey) are provided
    if (isset($data['to'], $data['subject'], $data['message'], $data['apikey'])) {

        # Escaping the API key to prevent SQL injection
        # I sanitize the API key to protect the database from injection attacks
        $apikey = mysqli_real_escape_string($conn, $data['apikey']);

        # Querying the user associated with the provided API key
        # I check if the API key exists and if it matches any user in the database
        $select = mysqli_query($conn, "SELECT user FROM data WHERE api_key = '$apikey';");

        # Verifying if the API key is valid
        # If the API key is found, I proceed; otherwise, I reject the request
        if ($select && mysqli_num_rows($select) > 0) {
            # Retrieving the user ID from the query result
            # I grab the user's unique ID for further queries
            $user_id = mysqli_fetch_array($select)['user'];

            # Retrieve the current number of requests for the user
            # I check how many requests the user has already made
            $select_num_req = mysqli_query($conn, "SELECT requests FROM data WHERE user = '$user_id'");

            if ($select_num_req) {
                # Fetch the current number of requests
                $num_req = mysqli_fetch_assoc($select_num_req)['requests'];

                # Increment the number of requests
                # I increment the request count by one for this user
                $num_req++;

                # Update the number of requests in the database
                # Then, I update the database with the new count
                $update_req = mysqli_query($conn, "UPDATE data SET requests = $num_req WHERE user = '$user_id'");
            }

            # Querying the user's information based on the unique ID
            # I fetch the user's details like name and email for sending the email
            $select_user = mysqli_query($conn, "SELECT name, email FROM users WHERE unique_id = '$user_id';");
            $user_info = mysqli_fetch_array($select_user);

            # Assigning user's name and email to variables
            $user_name = $user_info['name'];
            $user_email = $user_info['email'];

            # Setting the email fields from the input data
            # These fields come from the incoming POST request
            $to = $data['to'];
            $subject = $data['subject'];
            $message = $data['message'];

            # Adding the necessary email headers
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= isset($data['headers']) ? $data['headers'] : "From: $user_name <$user_email>";
            // echo json_encode($headers);  # For debugging purposes

            # Validating the recipient's email address
            if (!validateEmail($to)) {
                $response = ['status' => 'error', 'message' => 'Invalid email address.'];
                echo json_encode($response);
                logMessage(json_encode($response));
                exit;
            }

            # Attempting to send the email
            if (mail($to, $subject, $message, $headers)) {
                # If the email is sent, I update the emails_sent count
                $select_num_sent = mysqli_query($conn, "SELECT emails_sent FROM data WHERE user = '$user_id'");

                if ($select_num_sent) {
                    # Fetch the current number of emails_sent
                    $num_sent = mysqli_fetch_assoc($select_num_sent)['emails_sent'];

                    # Increment the number of emails_sent
                    $num_sent++;

                    # Update the number of emails_sent in the database
                    $update_req = mysqli_query($conn, "UPDATE data SET emails_sent = $num_sent WHERE user = '$user_id'");
                }

                # Responding with a success message
                $response = ['status' => 'success', 'message' => 'Email sent successfully.'];
                echo json_encode($response);
                logMessage(json_encode($response));
            } else {
                # Responding with an error if email fails to send
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
        # This part ensures that all required fields are present
        $response = ['status' => 'error', 'message' => 'Missing parameters (to, subject, message, or apikey).'];
        echo json_encode($response);
        logMessage(json_encode($response));
    }

} else {
    # Responding with an error if the request method is not POST
    # This block handles cases where the request is not POST
    $response = ['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.'];
    echo json_encode($response);
    logMessage(json_encode($response));
}
?>
