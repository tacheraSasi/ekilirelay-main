<?php
require_once "../../api.php";

# Security headers
Api::Header("Access-Control-Allow-Origin: *");
Api::Header("Access-Control-Allow-Methods: POST");
Api::Header("Content-Type: application/json");
Api::Header("X-Content-Type-Options: nosniff");
Api::Header("X-Frame-Options: DENY");

# Configuration
$uploadDir = "../../../bucket/";
$maxFileSize = 100 * 1024 * 1024; # 100MB
$allowedExtensions = [
    'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf',
    'txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
    'zip', 'rar', 'tar', 'gz', 'json', 'xml'
];

$allowedTypes = [
    # Images
    'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',

    # Documents
    'application/pdf', 'text/plain',
    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',

    # Archives
    'application/zip', 'application/x-rar-compressed', 'application/x-tar', 'application/gzip',

    # Data
    'application/json', 'application/xml', 'text/xml'
];

if (Method::POST()) {
    try {
        # Ensures the upload directory exists; if not, create it.
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        # Checks if the directory is writable; if not, try to set permissions.
        if (!is_writable($uploadDir)) {
            if (!chmod($uploadDir, 0755)) {
                throw new Exception("Server storage unavailable: cannot set permissions on upload directory");
            }
        }

        # Validates that a file was uploaded
        if (empty($_FILES["file"])) {
            throw new Exception("No file uploaded");
        }

        $file = $_FILES["file"];

        # Validate file upload errors
        if ($file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed");
        }

        # Validate file size
        if ($file["size"] > $maxFileSize) {
            $maxSizeMB = round($maxFileSize / 1024 / 1024);
            throw new Exception("File exceeds maximum size ({$maxSizeMB}MB)");
        }

        # Validates the filename (only allow alphanumeric, underscores, hyphens, and dots)
        $filename = basename($file["name"]);
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            throw new Exception("Invalid filename");
        }

        # Gets the real MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detectedType = $finfo->file($file["tmp_name"]);

        # Validates MIME type
        if (!in_array($detectedType, $allowedTypes)) {
            throw new Exception("Unsupported file type");
        }

        # Validates file extension
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Unsupported file extension");
        }

        # Generates a safe filename and create the target path
        $safeFilename = md5(uniqid() . microtime(true)) . '.' . $extension;
        $targetPath = $uploadDir . $safeFilename;

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
            "url"      => "/uploads/" . rawurlencode($safeFilename)
        ];
        echo json_encode($response);
    } catch (Exception $e) {
        # Log the error for server-side debugging
        error_log("Upload Error: " . $e->getMessage() . " - " . $_SERVER['REMOTE_ADDR']);

        # Return the error as a JSON response
        http_response_code(400);
        $response = [
            "status"  => "error",
            "message" => $e->getMessage()
        ];
        echo json_encode($response);
    }
} else {
    # Handles non-POST requests
    http_response_code(405);
    $response = [
        "status"  => "error",
        "message" => "Invalid request method. Only POST is allowed."
    ];
    echo json_encode($response);
}
