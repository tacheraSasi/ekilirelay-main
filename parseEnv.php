<?php
function parseEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("The .env file does not exist.");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        # Ignore comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        # Skip if line is malformed
        if (!strpos($line, '=')) {
            continue;
        }

        # Separate the key and value
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        # Handle quoted values
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = str_replace('\n', "\n", $matches[1]);
        } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
            $value = $matches[1];
        }

        # Handle boolean and null values
        if (strtolower($value) === 'true') {
            $value = true;
        } elseif (strtolower($value) === 'false') {
            $value = false;
        } elseif (strtolower($value) === 'null') {
            $value = null;
        }

        # Handling nested variables
        $value = preg_replace_callback('/\$\{(\w+)\}/', function ($matches) use ($env) {
            return isset($env[$matches[1]]) ? $env[$matches[1]] : $matches[0];
        }, $value);

        # Storing in the environment and in the array for nested variables
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $env[$key] = $value;
    }
}

