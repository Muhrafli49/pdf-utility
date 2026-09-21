<?php

require_once __DIR__ . '/vendor/autoload.php';

use setasign\Fpdi\Fpdi;


// ============================
// VALIDASI FILE
// ============================

if (
    !isset($_FILES['pdf_files']) ||
    empty($_FILES['pdf_files']['name'])
) {
    http_response_code(400);

    echo 'Tidak ada file PDF yang dipilih.';

    exit;
}


$files = $_FILES['pdf_files'];


// ============================
// AMBIL NAMA FILE PERTAMA
// ============================

$originalName = $files['name'][0];

$baseName = pathinfo(
    $originalName,
    PATHINFO_FILENAME
);


// ============================
// NAMA FILE OUTPUT
// ============================

$outputFile = $baseName . '_merge.pdf';


// ============================
// BUAT PDF BARU
// ============================

$pdf = new Fpdi();

$pdf->SetAutoPageBreak(false);


// ============================
// PROSES SETIAP FILE
// ============================

for (
    $i = 0;
    $i < count($files['name']);
    $i++
) {

    // ============================
    // CEK ERROR UPLOAD
    // ============================

    if (
        $files['error'][$i] !==
        UPLOAD_ERR_OK
    ) {
        continue;
    }


    // ============================
    // CEK EXTENSION
    // ============================

    $fileName =
        $files['name'][$i];

    $extension =
        strtolower(
            pathinfo(
                $fileName,
                PATHINFO_EXTENSION
            )
        );


    if ($extension !== 'pdf') {
        continue;
    }


    // ============================
    // TEMPORARY FILE
    // ============================

    $tmpFile =
        $files['tmp_name'][$i];


    // ============================
    // AMBIL JUMLAH HALAMAN
    // ============================

    $pageCount =
        $pdf->setSourceFile(
            $tmpFile
        );


    // ============================
    // MASUKKAN SETIAP HALAMAN
    // ============================

    for (
        $pageNo = 1;
        $pageNo <= $pageCount;
        $pageNo++
    ) {

        $templateId =
            $pdf->importPage(
                $pageNo
            );


        $size =
            $pdf->getTemplateSize(
                $templateId
            );


        // ============================
        // TENTUKAN ORIENTASI
        // ============================

        $orientation =
            ($size['width'] > $size['height'])
                ? 'L'
                : 'P';


        // ============================
        // TAMBAHKAN HALAMAN
        // ============================

        $pdf->AddPage(
            $orientation,
            [
                $size['width'],
                $size['height']
            ]
        );


        // ============================
        // TEMPEL HALAMAN
        // ============================

        $pdf->useTemplate(
            $templateId
        );

    }

}


// ============================
// BERSIHKAN OUTPUT BUFFER
// ============================

if (ob_get_length()) {

    ob_end_clean();

}


// ============================
// HEADER RESPONSE
// ============================

header(
    'Content-Type: application/pdf'
);

header(
    'Content-Disposition: attachment; filename="' .
    $outputFile .
    '"'
);

header(
    'Cache-Control: no-cache, must-revalidate'
);


// ============================
// KIRIM PDF KE BROWSER
// ============================

$pdf->Output(
    'I',
    $outputFile
);

exit;

?>