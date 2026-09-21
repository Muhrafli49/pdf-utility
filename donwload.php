<?php

$uploadDir = __DIR__ . '/uploads/';


// ============================
// VALIDASI FILE
// ============================

if (!isset($_GET['file'])) {

    die('File tidak ditemukan.');

}


$fileName = basename($_GET['file']);

$filePath = $uploadDir . $fileName;


// ============================
// CEK FILE
// ============================

if (!file_exists($filePath)) {

    die('File tidak ditemukan.');

}


// ============================
// DOWNLOAD
// ============================

header('Content-Type: application/pdf');

header(
    'Content-Disposition: attachment; filename="' .
    $fileName .
    '"'
);

header(
    'Content-Length: ' .
    filesize($filePath)
);

header('Cache-Control: no-cache, must-revalidate');


// Kirim file

readfile($filePath);


// ============================
// HAPUS FILE SETELAH DOWNLOAD
// ============================

unlink($filePath);

exit;

?>