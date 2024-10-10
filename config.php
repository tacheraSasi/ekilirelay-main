<?php
require 'parseEnv.php';
parseEnv(__DIR__ . '/.env');

// Database connection
$conn = new mysqli(
    "localhost", 
    getenv("DB_USERNAME"), 
    getenv("DB_PASSWORD"), 
    getenv("DB_NAME")
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
