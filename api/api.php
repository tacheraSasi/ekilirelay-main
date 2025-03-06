<?php
// include_once '../config.php';

class Api {
    public static string $uploadDir = "../../../bucket/";

    public static function Header(string $config) {
        header($config);
    }

    public static function storageUploadDir(){
        return self::$uploadDir;
    }

    public static function Response($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    public static function ValidateRequestMethod($allowedMethods) {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!in_array($method, $allowedMethods)) {
            self::Response([
                'status' => 'error',
                'message' => "Method $method not allowed"
            ], 405);
        }
    }

    public static function GetBearerToken() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        return str_replace('Bearer ', '', $authHeader);
    }

    public static function ValidateParameters($params) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $missing = array_diff($params, array_keys($data));
        
        if (!empty($missing)) {
            self::Response([
                'status' => 'error',
                'message' => 'Missing parameters: ' . implode(', ', $missing)
            ], 400);
        }
        return $data;
    }

    public static function logRequest($conn, $userId="", $statusCode, $data = []) {
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

    public static function validateUpload($file, $maxSize, $allowedTypes, $allowedExtensions, $detectedType, $extension) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server size limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit',
            UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
    
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception($errorMessages[$file['error']] ?? 'Unknown upload error');
        }
    
        if ($file['size'] > $maxSize) {
            throw new Exception('File exceeds maximum size of ' . round($maxSize / 1024 / 1024) . 'MB');
        }
    
        if (!in_array($detectedType, $allowedTypes)) {
            throw new Exception('Unsupported file type: ' . $detectedType);
        }
    
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Unsupported file extension: ' . $extension);
        }
    
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Invalid file source');
        }
    }
    

    // public function getUserByApiKey($apiKey) {
    //     $stmt = $conn->prepare("SELECT user FROM data WHERE api_key = ?");
    //     $stmt->bind_param("s", $apiKey);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     return $result->fetch_assoc();
    // }

    // public function getUserDetails($userId) {
    //     $stmt = $conn->prepare("SELECT name, email FROM users WHERE unique_id = ?");
    //     $stmt->bind_param("i", $userId);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     return $result->fetch_assoc();
    // }
}

class Database {
    private $conn;

    // public function __construct() {
    //     $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    //     if ($this->conn->connect_error) {
    //         Api::Response([
    //             'status' => 'error',
    //             'message' => 'Database connection failed'
    //         ], 500);
    //     }
    // }

    public function getUserByApiKey($apiKey) {
        $stmt = $this->conn->prepare("SELECT user FROM data WHERE api_key = ?");
        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function incrementCounter($userId, $column) {
        $stmt = $this->conn->prepare("UPDATE data SET $column = $column + 1 WHERE user = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    public function getUserDetails($userId) {
        $stmt = $this->conn->prepare("SELECT name, email FROM users WHERE unique_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

class EmailService {
    public static function send($to, $subject, $message, $headers) {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }

        if (!mail($to, $subject, $message, $headers)) {
            throw new Exception('Failed to send email');
        }
        return true;
    }

    public static function buildHeaders($fromName, $fromEmail) {
        return "MIME-Version: 1.0\r\n" .
               "Content-type:text/html;charset=UTF-8\r\n" .
               "From: $fromName <$fromEmail>";
    }
}
class Config{
    public static function defaultTemplate(string $message):string{
        $template = '
            <!DOCTYPE html>
            <html lang="en"><head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email Template</title>
                <style>
                    body {
                        font-family: system-ui, -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    }
    
                    pre {
                        font-family: inherit;
                    }
    
                    small {
                        opacity: .7;
                    }
    
                    a {
                        color: rgb(4, 189, 96);
                    }
                </style>
            </head>
            <body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
                <pre style="font-family: inherit;">
                    '.$message.'
                </pre>
                <small style="opacity: .7;">Sent with <a href="http://relay.ekilie.com" target="_blank" style="color:rgb(4, 189, 96)">ekiliRelay</a></small>
            </body>
            </html>
        ';
        return $template;
    
    }
}
class Method{
    public static function POST(){
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }
    
    public static function GET(){
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }
}