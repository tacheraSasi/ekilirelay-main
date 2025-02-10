<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$uploadDir = __DIR__ . '/uploads/';
$maxFileSize = 100 * 1024 * 1024; // 100MB
$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];