<?php

require_once __DIR__ . '/config/app.php';

$host = 'localhost';
$port = $_ENV['PORT'] ?? 8000;
$public_dir = __DIR__ . '/public';

echo "Starting server on http://{$host}:{$port}\n";

passthru("php -S {$host}:{$port} -t {$public_dir}");

