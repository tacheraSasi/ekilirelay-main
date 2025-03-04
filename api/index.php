<?php
include_once '../config.php';
include_once "api.php";

# Allowing cross-origin requests (CORS)
Api::Header("Access-Control-Allow-Origin: *");
Api::Header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
Api::Header("Access-Control-Allow-Headers: Content-Type, Authorization");

# Handling preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);  
    exit();  
}

# Setting the content type to JSON
header('Content-Type: application/json');

# Function for validating an email address
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

# Function to log requests to the database
function logRequest($conn, $userId, $statusCode, $data = []) {
    $endpoint = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $parameters = json_encode($data);

    $userId = $userId ? intval($userId) : 'NULL';
    $endpoint = mysqli_real_escape_string($conn, $endpoint);
    $method = mysqli_real_escape_string($conn, $method);
    $ip = mysqli_real_escape_string($conn, $ip);
    $parameters = mysqli_real_escape_string($conn, $parameters);
    $statusCode = intval($statusCode);

    $query = "INSERT INTO api_requests (user_id, endpoint, method, ip_address, status_code, parameters)
              VALUES ($userId, '$endpoint', '$method', '$ip', $statusCode, '$parameters')";
    mysqli_query($conn, $query);
}

# Checking if the request method is POST
if (Method::POST()) {

    # Decoding the incoming JSON payload
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $apikey = $data['apikey'] ?? '';

    # Validate required parameters
    if (!isset($data['to'], $data['subject'], $data['message'], $data['apikey'])) {
        $response = ['status' => 'error', 'message' => 'Missing required parameters'];
        logRequest($conn, null, 400, $data);
        Api::Response($response, 400);
    }

    # Validate API Key
    $apikey = mysqli_real_escape_string($conn, $apikey);
    $select = mysqli_query($conn, "SELECT user FROM data WHERE api_key = '$apikey'");
    
    if (!$select || mysqli_num_rows($select) === 0) {
        $response = ['status' => 'error', 'message' => 'Invalid API key'];
        logRequest($conn, null, 401, $data);
        Api::Response($response, 401);
    }

    $user_id = mysqli_fetch_assoc($select)['user'];

    # Retrieve user details
    $select_user = mysqli_query($conn, "SELECT name, email FROM users WHERE unique_id = '$user_id'");
    $user_info = mysqli_fetch_assoc($select_user);

    if (!$user_info) {
        $response = ['status' => 'error', 'message' => 'User not found'];
        logRequest($conn, $user_id, 404, $data);
        Api::Response($response, 404);
    }

    # Prepare email
    $to = $data['to'];
    $subject = $data['subject'];
    $message = Config::defaultTemplate($data['message']);
    $headers = EmailService::buildHeaders($user_info['name'], $user_info['email']);

    # Validate email
    if (!validateEmail($to)) {
        $response = ['status' => 'error', 'message' => 'Invalid recipient email'];
        logRequest($conn, $user_id, 400, $data);
        Api::Response($response, 400);
    }

    # Send email
    try {
        EmailService::send($to, $subject, $message, $headers);
        
        # Update emails_sent count
        mysqli_query($conn, "UPDATE data SET emails_sent = emails_sent + 1 WHERE user = '$user_id'");
        
        $response = ['status' => 'success', 'message' => 'Email sent'];
        logRequest($conn, $user_id, 200, $data);
        Api::Response($response);
    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => $e->getMessage()];
        logRequest($conn, $user_id, 500, $data);
        Api::Response($response, 500);
    }

} else {
    $response = ['status' => 'error', 'message' => 'Method not allowed'];
    logRequest($conn, null, 405, []);
    Api::Response($response, 405);
}