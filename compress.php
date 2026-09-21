<?php

// ==============================
// CEK UPLOAD
// ==============================

if (!isset($_FILES['pdf_file'])) {
    die('File PDF belum dipilih.');
}

if ($_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    die('Terjadi kesalahan saat upload file.');
}

$file = $_FILES['pdf_file'];


// ==============================
// VALIDASI EXTENSION
// ==============================

$extension = strtolower(
    pathinfo($file['name'], PATHINFO_EXTENSION)
);

if ($extension !== 'pdf') {
    die('File yang diperbolehkan hanya PDF.');
}


// ==============================
// VALIDASI MIME
// ==============================

$finfo = finfo_open(FILEINFO_MIME_TYPE);

$mimeType = finfo_file(
    $finfo,
    $file['tmp_name']
);

finfo_close($finfo);

if ($mimeType !== 'application/pdf') {
    die('File yang diupload bukan PDF yang valid.');
}


// ==============================
// LEVEL KOMPRESI
// ==============================

$level = $_POST['compression'] ?? 'medium';

$settings = [
    'low'    => '/printer',
    'medium' => '/ebook',
    'high'   => '/screen'
];

$pdfSetting = $settings[$level] ?? '/ebook';


// ==============================
// CARI GHOSTSCRIPT
// ==============================

$ghostscript = null;


// Coba dari PATH Windows
$pathResult = [];

exec(
    'where gswin64c 2>&1',
    $pathResult,
    $pathCode
);

if ($pathCode === 0 && !empty($pathResult)) {
    $ghostscript = trim($pathResult[0]);
}


// Coba lokasi umum Ghostscript
if ($ghostscript === null || !file_exists($ghostscript)) {

    $folders = glob(
        'C:/Program Files/gs/*/bin/gswin64c.exe'
    );

    if (!empty($folders)) {
        $ghostscript = end($folders);
    }
}


// Coba Program Files (x86)
if ($ghostscript === null || !file_exists($ghostscript)) {

    $folders = glob(
        'C:/Program Files (x86)/gs/*/bin/gswin32c.exe'
    );

    if (!empty($folders)) {
        $ghostscript = end($folders);
    }
}


// Kalau tidak ditemukan
if ($ghostscript === null || !file_exists($ghostscript)) {

    die(
        'Ghostscript tidak ditemukan oleh PHP.<br><br>' .
        'Pastikan Ghostscript sudah terinstall.'
    );
}


// ==============================
// FILE TEMPORARY
// ==============================

$inputFile = tempnam(
    sys_get_temp_dir(),
    'pdf_input_'
);

$outputFile = tempnam(
    sys_get_temp_dir(),
    'pdf_output_'
);

$inputFile .= '.pdf';
$outputFile .= '.pdf';


// ==============================
// PINDAHKAN FILE UPLOAD
// ==============================

if (!move_uploaded_file(
    $file['tmp_name'],
    $inputFile
)) {

    @unlink($inputFile);
    @unlink($outputFile);

    die('Gagal menyimpan file PDF sementara.');
}


// ==============================
// COMMAND GHOSTSCRIPT
// ==============================

$command =
    '"' . $ghostscript . '"' .
    ' -sDEVICE=pdfwrite' .
    ' -dCompatibilityLevel=1.4' .
    ' -dPDFSETTINGS=' . $pdfSetting .
    ' -dNOPAUSE' .
    ' -dQUIET' .
    ' -dBATCH' .
    ' -sOutputFile=' . escapeshellarg($outputFile) .
    ' ' . escapeshellarg($inputFile);


// ==============================
// JALANKAN
// ==============================

$output = [];

$returnCode = 0;

exec(
    $command . ' 2>&1',
    $output,
    $returnCode
);


// ==============================
// CEK HASIL
// ==============================

if (
    $returnCode !== 0 ||
    !file_exists($outputFile) ||
    filesize($outputFile) === 0
) {

    @unlink($inputFile);
    @unlink($outputFile);

    echo '<h3>Gagal melakukan kompresi PDF.</h3>';

    echo '<p>Ghostscript berhasil ditemukan, tetapi gagal memproses PDF.</p>';

    echo '<strong>Detail:</strong>';

    echo '<pre>';
    echo htmlspecialchars(
        implode("\n", $output)
    );
    echo '</pre>';

    exit;
}


// ==============================
// NAMA FILE DOWNLOAD
// ==============================

$originalName = pathinfo(
    $file['name'],
    PATHINFO_FILENAME
);

$downloadName = $originalName . '_compressed.pdf';


// ==============================
// DOWNLOAD
// ==============================

header('Content-Type: application/pdf');

header(
    'Content-Disposition: attachment; filename="' .
    $downloadName .
    '"'
);

header(
    'Content-Length: ' .
    filesize($outputFile)
);

header('Cache-Control: no-cache, no-store, must-revalidate');

header('Pragma: no-cache');

header('Expires: 0');


// Kirim file
readfile($outputFile);


// ==============================
// HAPUS TEMPORARY
// ==============================

@unlink($inputFile);
@unlink($outputFile);

exit;