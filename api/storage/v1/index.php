<?php
require_once("../api.php");
Api::Header("Access-Control-Allow-Origin: *");
Api::Header("Access-Control-Allow-Methods: POST");
Api::Header("Content-Type: application/json");

$uploadDir = __DIR__ . '/uploads/';
$maxFileSize = 100 * 1024 * 1024; // 100MB
$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];