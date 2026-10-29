<?php
header('Content-Type: application/json; charset=utf-8');

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error occurred.']);
    exit;
}

$file = $_FILES['file'];
$allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'png', 'jpg', 'jpeg', 'gif'];
$fileName = basename($file['name']);
$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'File type not allowed.']);
    exit;
}

$maxSize = 20 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'File exceeds maximum size of 20 MB.']);
    exit;
}

$sanitizedBase = preg_replace('/[^A-Za-z0-9._-]+/', '_', pathinfo($fileName, PATHINFO_FILENAME));
$targetName = $sanitizedBase . '-' . time() . '.' . $extension;
$targetFile = $uploadDir . '/' . $targetName;

if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file.']);
    exit;
}

$scheme = 'http';
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $scheme = 'https';
} elseif (isset($_SERVER['REQUEST_SCHEME'])) {
    $scheme = $_SERVER['REQUEST_SCHEME'];
}
$host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$publicUrl = $scheme . '://' . $host . $scriptDir . '/uploads/' . rawurlencode($targetName);

$response = [
    'success' => true,
    'url' => $publicUrl,
    'title' => $fileName,
    'number' => '',
    'year' => date('Y'),
];

echo json_encode($response);
