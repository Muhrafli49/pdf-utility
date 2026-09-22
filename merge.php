php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Validasi file
if (!isset($_FILES['pdf_files']) || empty($_FILES['pdf_files']['name'])) {
    http_response_code(400);
    echo 'Tidak ada file PDF yang dipilih.';
    exit;
}

$files = $_FILES['pdf_files'];

// Nama output berdasarkan file pertama
$originalName = $files['name'][0];
$baseName = pathinfo($originalName, PATHINFO_FILENAME);
$outputFile = $baseName . '_merge.pdf';

$pdf = new Fpdi();
$pdf->SetAutoPageBreak(false);

// Proses setiap file
for ($i = 0; $i < count($files['name']); $i++) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        continue;
    }

    $fileName = $files['name'][$i];
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($extension !== 'pdf') {
        continue;
    }

    $pageCount = $pdf->setSourceFile($files['tmp_name'][$i]);

    // Proses setiap halaman
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($templateId);

        $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

        $pdf->AddPage($orientation, [
            $size['width'],
            $size['height']
        ]);

        $pdf->useTemplate($templateId);
    }
}

// Bersihkan output buffer
if (ob_get_length()) {
    ob_end_clean();
}

// Kirim PDF ke browser
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $outputFile . '"');
header('Cache-Control: no-cache, must-revalidate');

$pdf->Output('I', $outputFile);
exit;

