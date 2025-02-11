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
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf',
                     'txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
                     'zip', 'rar', 'tar', 'gz', 'json', 'xml'];

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
        # Validates upload directory
        if (!is_dir($uploadDir) {
            // mkdir($uploadDir, 0755, true);
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Server configuration error");
            }
        }

        if (!is_writable($uploadDir)) {
            throw new Exception("Server storage unavailable");
        }

        # Validates file upload
        if (empty($_FILES["file"]) {
            throw new Exception("No file uploaded");
        }

        $file = $_FILES["file"];

        # Validates upload errors
        if ($file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed");
        }

        # Validates file size
        if ($file["size"] > $maxFileSize) {
            $maxSizeMB = round($maxFileSize / 1024 / 1024);
            throw new Exception("File exceeds maximum size ({$maxSizeMB}MB)");
        }

        # Validates file name
        $filename = basename($file["name"]);
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            throw new Exception("Invalid filename");
        }

        # Get real MIME type
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

        # Generate safe filename
        $safeFilename = md5(uniqid() . microtime(true)) . '.' . $extension;
        $targetPath = $uploadDir . $safeFilename;

        # Validates temporary file
        if (!is_uploaded_file($file["tmp_name"])) {
            throw new Exception("Invalid file source");
        }

        # Moves the file
        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            throw new Exception("File storage failed");
        }

        # Success response
        echo json_encode([
            "success" => true,
            "message" => "File uploaded successfully",
            "filename" => $safeFilename,
            "url" => "/uploads/" . rawurlencode($safeFilename)
        ]);

    } catch (Exception $e) {
        error_log("Upload Error: " . $e->getMessage() . " - " . $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "File processing failed"
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
}
