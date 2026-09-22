<?php

$uploadDir = __DIR__ . '/uploads/';

// Validasi file
if (!isset($_GET['file'])) {
    die('File tidak ditemukan.');
}

$fileName = basename($_GET['file']);
$filePath = $uploadDir . $fileName;

// Cek file
if (!file_exists($filePath)) {
    die('File tidak ditemukan.');
}

// Download file
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');

readfile($filePath);

// Hapus file setelah download
unlink($filePath);

exit;
?>