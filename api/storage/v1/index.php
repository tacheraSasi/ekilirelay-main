<?php
require_once "../../api.php";
Api::Header("Access-Control-Allow-Origin: *");
Api::Header("Access-Control-Allow-Methods: POST");
Api::Header("Content-Type: application/json");

$uploadDir = __DIR__ . "/uploads/";
$maxFileSize = 100 * 1024 * 1024; // 100MB
$allowedTypes = ["image/jpeg", "image/png", "application/pdf"];

if (Method::POST()) {
    try {
        // Checks if file was uploaded
        if (empty($_FILES["file"])) {
            throw new Exception("No file uploaded");
        }

        $file = $_FILES["file"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload error: " . $file["error"]);
        }

        if ($file["size"] > $maxFileSize) {
            throw new Exception("File too large (max 5MB)");
        }

        if (!in_array($file["type"], $allowedTypes)) {
            throw new Exception("Invalid file type");
        }

        // Creates upload directory if missing
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generates unique filename
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $extension;
        $targetPath = $uploadDir . $filename;

        // Moves the file
        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            throw new Exception("Failed to save file");
        }

        echo json_encode([
            "success" => true,
            "message" => "File uploaded successfully",
            "filename" => $filename,
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage(),
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
}
