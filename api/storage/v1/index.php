<?php
include_once '../../../config.php';
include_once "../../api.php";

Api::Header("Access-Control-Allow-Origin: *");
Api::Header("Access-Control-Allow-Methods: POST");
Api::Header("Content-Type: application/json");
Api::Header("X-Content-Type-Options: nosniff");
Api::Header("X-Frame-Options: DENY");


$uploadDir = API::storageUploadDir();
$maxFileSize = 100 * 1024 * 1024;
$allowedExtensions = ['jpg','jpeg','png','gif','webp','svg','pdf','txt',
                    'doc','docx','xls','xlsx','ppt','pptx','zip','rar',
                    'tar','gz','json','xml'];
$allowedTypes = ['image/jpeg','image/png','image/gif','image/webp',
                'image/svg+xml','application/pdf','text/plain',
                'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip','application/x-rar-compressed','application/x-tar',
                'application/gzip','application/json','application/xml','text/xml'];

if (Method::POST()) {
    try {
        if (empty($_FILES['file']) || empty($_POST['apikey'])) {
            throw new Exception('Missing required parameters');
        }

        $file = $_FILES['file'];
        $apiKey = $_POST['apikey'];
        $conn->begin_transaction();

        // Validates API key and get user
        $stmt = $conn->prepare("SELECT u.unique_id, u.email 
                              FROM data d 
                              JOIN users u ON d.user = u.unique_id 
                              WHERE d.api_key = ?");
        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        # user info
        $user_id = $user['unique_id'];
        $user_email = $user['email'];

        if (!$user) {
            throw new Exception('Invalid API key');
        }

        // File metadata
        $originalName = basename($file['name']);
        $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $fileSize = (int)$file['size'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $safeFilename = $fileNameWithoutExt."_".bin2hex(random_bytes(16)) . '.' . $extension;
        $userDir = $uploadDir . $user_email . '/';
        $targetPath = $userDir . $safeFilename;
        $publicUrl = "https://relay.ekilie.com/bucket/{$user_email}/" . rawurlencode($safeFilename);

        // Validates upload
        API::validateUpload($file, $maxFileSize, $allowedTypes, $allowedExtensions, $mimeType, $extension);

        // Creates user directory
        if (!is_dir($userDir) && !mkdir($userDir, 0755, true)) {
            throw new Exception("Failed to create user directory");
        }

        // Moves uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("File storage failed");
        }

        // Stores metadata
        $stmt = $conn->prepare("INSERT INTO uploads 
                              (user_id, original_name, stored_name, file_type, 
                               file_size, extension, upload_time, url)
                              VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param("isssiss", 
            $user['unique_id'],
            $originalName,
            $safeFilename,
            $mimeType,
            $fileSize,
            $extension,
            $publicUrl
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to save file metadata");
        }

        $conn->commit();
        
        Api::Response([
            'status' => 'success',
            'url' => $publicUrl,
            'metadata' => [
                'original_name' => $originalName,
                'file_type' => $mimeType,
                'file_size' => $fileSize,
                'upload_time' => date('c')
            ]
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Upload Error: {$e->getMessage()} - IP: {$_SERVER['REMOTE_ADDR']}");
        http_response_code(400);
        Api::Response([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    Api::Response([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}


