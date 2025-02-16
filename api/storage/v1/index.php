<?php
include_once '../../../config.php';
include_once "../../api.php";

# Security headers
Api::Header("Access-Control-Allow-Origin: *");
Api::Header("Access-Control-Allow-Methods: POST");
Api::Header("Content-Type: application/json");
Api::Header("X-Content-Type-Options: nosniff");
Api::Header("X-Frame-Options: DENY");

# Configuration
$uploadDir = API::storageUploadDir();
$maxFileSize = 100 * 1024 * 1024; # 100MB
$allowedExtensions = [
    'jpg','jpeg','png','gif','webp','svg','pdf','txt','doc','docx','xls','xlsx','ppt','pptx','zip','rar','tar','gz','json','xml'
];

$allowedTypes = [
    # Images
    'image/jpeg','image/png','image/gif','image/webp','image/svg+xml',

    # Documents
    'application/pdf','text/plain','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation',

    # Archives
    'application/zip','application/x-rar-compressed','application/x-tar','application/gzip',

    # Data
    'application/json','application/xml','text/xml'
];

if (Method::POST()) {
    try {
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        if (!is_writable($uploadDir)) {
            if (!chmod($uploadDir, 0755)) {
                throw new Exception("Server storage unavailable: cannot set permissions on upload directory");
            }
        }

        if (empty($_FILES['file']) || empty($_POST['apikey'])) {
            throw new Exception('Missing required parameters');
        }

        $file = $_FILES["file"];
        $apikey = mysqli_real_escape_string($conn, $_POST["apikey"]);
        $query = "SELECT u.name, u.email FROM data d JOIN users u ON d.user = u.unique_id WHERE d.api_key = '$apikey'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {

            $user_info = mysqli_fetch_array($result);
            $user_name = $user_info['name'];
            $user_email = $user_info['email'];
            $userSpecificDir = "../../../bucket/$user_email/";
            if (!is_dir($userSpecificDir)) {
                if (!mkdir($userSpecificDir, 0755, true)) {
                    throw new Exception("Failed to create upload directory $userSpecificDir");
                }
            }

            if (!is_writable($userSpecificDir)) {
                if (!chmod($userSpecificDir, 0755)) {
                    throw new Exception("Server storage unavailable: cannot set permissions on upload directory $userSpecificDir");
                }
            }

            # Validate file upload errors
            if ($file["error"] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload failed". $file["error"]);
            }
            # Validate file size
            if ($file["size"] > $maxFileSize) {
                $maxSizeMB = round($maxFileSize / 1024 / 1024);
                throw new Exception("File exceeds maximum size ({$maxSizeMB}MB)");
            }

            $filename = basename($file["name"]);
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detectedType = $finfo->file($file["tmp_name"]);

            # Validates MIME type
            if (!in_array($detectedType, $allowedTypes)) {
                throw new Exception("Unsupported file type: ".$detectedType);
            }

            # Validates file extension
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("Unsupported file extension: ".$extension);
            }

            # Generates a safe filename and create the target path
            $safeFilename = $filename . "_" . md5(uniqid() . microtime(true)) . '.' . $extension;
            $targetPath = $userSpecificDir . $safeFilename;
            
            # Validates that the file was uploaded via HTTP POST
            if (!is_uploaded_file($file["tmp_name"])) {
                throw new Exception("Invalid file source");
            }

            # Moves the file to its destination
            if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
                throw new Exception("File storage failed");
            }

            # Success response
            $response = [
                "status"   => "success",
                "message"  => "File uploaded successfully",
                "filename" => $safeFilename,
                "url"      => "https://relay.ekilie.com/bucket/$user_email/" . rawurlencode($safeFilename)
            ];
            Api::Response($response);

        } else {
            $response = ['status' => 'error', 'message' => 'Invalid API key. Visit https://relay.ekilie.com to get the correct one.'];
            Api::Response($response);
        }

    } catch (Exception $e) {
        error_log("Upload Error: " . $e->getMessage() . " - " . $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        $response = [
            "status"  => "error",
            "message" => $e->getMessage()
        ];
        Api::Response($response);
    }
} else {
    http_response_code(405);
    $response = [
        "status"  => "error",
        "message" => "Invalid request method. Only POST is allowed."
    ];
    Api::Response($response);
}