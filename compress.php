<?php

// Validasi upload
if (!isset($_FILES['pdf_file'])) {
    die('File PDF belum dipilih.');
}

if ($_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    die('Terjadi kesalahan saat upload file.');
}

$file = $_FILES['pdf_file'];

// Validasi extension
$extension = strtolower(
    pathinfo($file['name'], PATHINFO_EXTENSION)
);

if ($extension !== 'pdf') {
    die('File yang diperbolehkan hanya PDF.');
}

// Validasi MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);

$mimeType = finfo_file(
    $finfo,
    $file['tmp_name']
);

finfo_close($finfo);

if ($mimeType !== 'application/pdf') {
    die('File yang diupload bukan PDF yang valid.');
}

// Tentukan level kompresi
$level = $_POST['compression'] ?? 'medium';

$settings = [
    'low'    => '/printer',
    'medium' => '/ebook',
    'high'   => '/screen'
];

$pdfSetting = $settings[$level] ?? '/ebook';

// Cari Ghostscript
$ghostscript = null;
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

if ($ghostscript === null || !file_exists($ghostscript)) {
    die(
        'Ghostscript tidak ditemukan oleh PHP.<br><br>' .
        'Pastikan Ghostscript sudah terinstall.'
    );
}

// Buat file temporary
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

// Pindahkan file upload
if (!move_uploaded_file(
    $file['tmp_name'],
    $inputFile
)) {
    @unlink($inputFile);
    @unlink($outputFile);

    die('Gagal menyimpan file PDF sementara.');
}

// Susun command Ghostscript
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

// Jalankan Ghostscript
$output = [];
$returnCode = 0;

exec(
    $command . ' 2>&1',
    $output,
    $returnCode
);

// Cek hasil kompresi
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

// Tentukan nama file download
$originalName = pathinfo(
    $file['name'],
    PATHINFO_FILENAME
);

$downloadName = $originalName . '_compressed.pdf';

// Download file
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

readfile($outputFile);

// Hapus file temporary
@unlink($inputFile);
@unlink($outputFile);

exit;