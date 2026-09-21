<?php

require_once __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;

// Fungsi bantu
function cleanText($text)
{
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function getColumnWeight($text)
{
    $length = mb_strlen(cleanText($text));

    if ($length < 4) {
        return 4;
    }

    if ($length > 35) {
        $length = 35;
    }

    return $length;
}

function calculateColumnWidths($rows, $availableWidth)
{
    if (empty($rows)) {
        return [];
    }

    $columnCount = 0;

    foreach ($rows as $row) {
        $columnCount = max($columnCount, count($row));
    }

    if ($columnCount === 0) {
        return [];
    }

    $weights = array_fill(0, $columnCount, 0);

    foreach ($rows as $row) {
        foreach ($row as $index => $value) {
            $weight = getColumnWeight($value);

            if ($weight > $weights[$index]) {
                $weights[$index] = $weight;
            }
        }
    }

    $minimumWidth = 700;

    $maximumWidth = (int) (
        $availableWidth * 0.35
    );

    $minimumTotal = $minimumWidth * $columnCount;

    if ($minimumTotal > $availableWidth) {
        $minimumWidth = (int) floor(
            $availableWidth / $columnCount
        );

        $minimumWidth = max(350, $minimumWidth);
    }

    $totalWeight = array_sum($weights);

    if ($totalWeight <= 0) {
        $equalWidth = (int) floor(
            $availableWidth / $columnCount
        );

        return array_fill(
            0,
            $columnCount,
            $equalWidth
        );
    }

    $widths = [];

    foreach ($weights as $weight) {
        $width = (int) round(
            ($weight / $totalWeight) * $availableWidth
        );

        $width = max($minimumWidth, $width);
        $width = min($maximumWidth, $width);

        $widths[] = $width;
    }

    $currentTotal = array_sum($widths);

    if ($currentTotal > $availableWidth) {
        $excess = $currentTotal - $availableWidth;

        while ($excess > 0) {
            $largestIndex = null;
            $largestWidth = 0;

            foreach ($widths as $index => $width) {
                if (
                    $width > $largestWidth &&
                    $width > $minimumWidth
                ) {
                    $largestWidth = $width;
                    $largestIndex = $index;
                }
            }

            if ($largestIndex === null) {
                break;
            }

            $reduce = min(
                50,
                $excess,
                $widths[$largestIndex] - $minimumWidth
            );

            $widths[$largestIndex] -= $reduce;
            $excess -= $reduce;
        }
    } elseif ($currentTotal < $availableWidth) {
        $remaining = $availableWidth - $currentTotal;

        while ($remaining > 0) {
            $largestWeightIndex = 0;

            foreach ($weights as $index => $weight) {
                if (
                    $weight >
                    $weights[$largestWeightIndex]
                ) {
                    $largestWeightIndex = $index;
                }
            }

            $add = min(
                50,
                $remaining,
                $maximumWidth - $widths[$largestWeightIndex]
            );

            if ($add <= 0) {
                break;
            }

            $widths[$largestWeightIndex] += $add;
            $remaining -= $add;
        }
    }

    return $widths;
}

// Cek request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: convert-word.php');
    exit;
}

// Cek file
if (!isset($_FILES['pdf_file'])) {
    header('Location: convert-word.php?error=no_file');
    exit;
}

$file = $_FILES['pdf_file'];

// Cek upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Location: convert-word.php?error=upload');
    exit;
}

// Cek extension
$extension = strtolower(
    pathinfo(
        $file['name'],
        PATHINFO_EXTENSION
    )
);

if ($extension !== 'pdf') {
    header('Location: convert-word.php?error=invalid_type');
    exit;
}

// Cek MIME
$finfo = new finfo(FILEINFO_MIME_TYPE);

$mimeType = $finfo->file(
    $file['tmp_name']
);

if ($mimeType !== 'application/pdf') {
    header('Location: convert-word.php?error=invalid_type');
    exit;
}

// Parse PDF
try {
    $parser = new Parser();

    $pdf = $parser->parseFile(
        $file['tmp_name']
    );

    $pages = $pdf->getPages();

} catch (Exception $e) {
    header('Location: convert-word.php?error=parse');
    exit;
}

// Buat Word
$phpWord = new PhpWord();

$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(10);

// Style tabel
$phpWord->addTableStyle(
    'PDFTable',
    [
        'borderSize' => 5,
        'borderColor' => 'B7B7B7',
        'cellMarginTop' => 35,
        'cellMarginBottom' => 35,
        'cellMarginLeft' => 55,
        'cellMarginRight' => 55,
        'alignment' => JcTable::CENTER,
    ]
);

// Style teks
$titleStyle = [
    'bold' => true,
    'size' => 16,
    'name' => 'Arial',
];

$subtitleStyle = [
    'bold' => true,
    'size' => 11,
    'name' => 'Arial',
];

$headerTextStyle = [
    'bold' => true,
    'color' => 'FFFFFF',
    'size' => 9,
    'name' => 'Arial',
    'spaceBefore' => 0,
    'spaceAfter' => 0,
];

$bodyTextStyle = [
    'size' => 9,
    'name' => 'Arial',
    'spaceBefore' => 0,
    'spaceAfter' => 0,
    'lineHeight' => 1,
];

$noteStyle = [
    'italic' => true,
    'size' => 9,
    'color' => '666666',
    'name' => 'Arial',
    'spaceBefore' => 0,
    'spaceAfter' => 0,
];

// Ukuran tabel
$pageWidth = 11906;
$pageHeight = 16838;

$marginTop = 650;
$marginBottom = 650;
$marginLeft = 650;
$marginRight = 650;

$tableSideSpace = 600;

$availableTableWidth =
    $pageWidth
    - $marginLeft
    - $marginRight
    - ($tableSideSpace * 2);

// Proses halaman
foreach ($pages as $pageIndex => $page) {

    $section = $phpWord->addSection([
        'pageSizeW' => 11906,
        'pageSizeH' => 16838,
        'marginTop' => 650,
        'marginBottom' => 650,
        'marginLeft' => $marginLeft,
        'marginRight' => $marginRight,
        'headerDistance' => 300,
        'footerDistance' => 300,
    ]);

    $text = trim(
        $page->getText()
    );

    if ($text === '') {
        continue;
    }

    $lines = preg_split(
        '/\r\n|\r|\n/',
        $text
    );

    $lines = array_map(
        'trim',
        $lines
    );

    $lines = array_values(
        array_filter(
            $lines,
            function ($line) {
                return $line !== '';
            }
        )
    );

    // Cari tabel
    $tableStart = null;

    foreach ($lines as $index => $line) {

        if (
            stripos($line, 'ID Pelapor') !== false &&
            stripos($line, 'Nama Bank') !== false &&
            stripos($line, 'Jenis Produk') !== false &&
            stripos($line, 'Nominal') !== false &&
            stripos($line, 'Status') !== false
        ) {
            $tableStart = $index;
            break;
        }
    }

    // Jika tidak ada tabel
    if ($tableStart === null) {

        foreach ($lines as $index => $line) {

            if ($index === 0) {

                $section->addText(
                    $line,
                    $titleStyle,
                    [
                        'alignment' => Jc::CENTER,
                        'spaceBefore' => 0,
                        'spaceAfter' => 100,
                    ]
                );

            } else {

                $section->addText(
                    $line,
                    $bodyTextStyle,
                    [
                        'spaceBefore' => 0,
                        'spaceAfter' => 60,
                    ]
                );
            }
        }

        continue;
    }

    // Teks sebelum tabel
    for (
        $i = 0;
        $i < $tableStart;
        $i++
    ) {

        $line = trim($lines[$i]);

        if ($line === '') {
            continue;
        }

        if ($i === 0) {

            $section->addText(
                $line,
                $titleStyle,
                [
                    'alignment' => Jc::CENTER,
                    'spaceBefore' => 0,
                    'spaceAfter' => 70,
                ]
            );

        } else {

            $section->addText(
                $line,
                $subtitleStyle,
                [
                    'alignment' => Jc::CENTER,
                    'spaceBefore' => 0,
                    'spaceAfter' => 90,
                ]
            );
        }
    }

    // Cari akhir tabel
    $tableEnd = $tableStart;

    for (
        $i = $tableStart + 1;
        $i < count($lines);
        $i++
    ) {

        if (
            preg_match(
                '/^\d+\s+\d{6,}/',
                $lines[$i]
            )
        ) {
            $tableEnd = $i;
        } else {
            break;
        }
    }

    // Data tabel
    $headers = [
        'No',
        'ID Pelapor',
        'Nama Bank',
        'Jenis Produk',
        'Nominal (Rp)',
        'Status',
    ];

    $tableRows = [];

    $tableRows[] = $headers;

    for (
        $i = $tableStart + 1;
        $i <= $tableEnd;
        $i++
    ) {

        $line = trim($lines[$i]);

        if (
            preg_match(
                '/^(\d+)\s+(\d{6,})\s+(.+?)\s+(Giro|Tabungan|Deposito|Kredit|Surat Berharga)\s+([\d.,]+)\s+(\w+)$/i',
                $line,
                $matches
            )
        ) {

            $tableRows[] = [
                $matches[1],
                $matches[2],
                $matches[3],
                $matches[4],
                $matches[5],
                $matches[6],
            ];
        }
    }

    // Lebar kolom
    $columnWidths = calculateColumnWidths(
        $tableRows,
        $availableTableWidth
    );

    // Buat tabel
    $table = $section->addTable(
        'PDFTable'
    );

    // Header
    $table->addRow(
        420,
        [
            'tblHeader' => true,
        ]
    );

    foreach (
        $headers as $index => $header
    ) {

        $cell = $table->addCell(
            $columnWidths[$index],
            [
                'bgColor' => '00529B',
                'valign' => 'center',
            ]
        );

        $cell->addText(
            $header,
            $headerTextStyle,
            [
                'alignment' => Jc::CENTER,
                'spaceBefore' => 0,
                'spaceAfter' => 0,
            ]
        );
    }

    // Isi tabel
    for (
        $rowIndex = 1;
        $rowIndex < count($tableRows);
        $rowIndex++
    ) {

        $row = $tableRows[$rowIndex];

        $table->addRow(360);

        foreach (
            $row as $columnIndex => $value
        ) {

            if ($columnIndex === 0) {

                $alignment = Jc::CENTER;

            } elseif (
                preg_match(
                    '/^[\d.,]+$/',
                    trim($value)
                )
            ) {

                $alignment = Jc::RIGHT;

            } else {

                $alignment = Jc::LEFT;
            }

            if (
                $columnIndex === 1 ||
                $columnIndex === 5
            ) {
                $alignment = Jc::CENTER;
            }

            $cell = $table->addCell(
                $columnWidths[$columnIndex],
                [
                    'valign' => 'center',
                ]
            );

            $cell->addText(
                cleanText($value),
                $bodyTextStyle,
                [
                    'alignment' => $alignment,
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                ]
            );
        }
    }

    // Teks setelah tabel
    if (
        $tableEnd + 1 < count($lines)
    ) {

        $section->addTextBreak(1);

        for (
            $i = $tableEnd + 1;
            $i < count($lines);
            $i++
        ) {

            $line = trim($lines[$i]);

            if ($line === '') {
                continue;
            }

            if (
                stripos($line, 'Catatan:') === 0
            ) {

                $section->addText(
                    $line,
                    $noteStyle,
                    [
                        'spaceBefore' => 0,
                        'spaceAfter' => 40,
                    ]
                );

            } else {

                $section->addText(
                    $line,
                    $bodyTextStyle,
                    [
                        'spaceBefore' => 0,
                        'spaceAfter' => 40,
                    ]
                );
            }
        }
    }
}

// Nama file
$originalName = pathinfo(
    $file['name'],
    PATHINFO_FILENAME
);

$originalName = preg_replace(
    '/[^A-Za-z0-9_\-]/',
    '_',
    $originalName
);

$outputName = $originalName . '.docx';

// Download
header(
    'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'
);

header(
    'Content-Disposition: attachment; filename="' .
    $outputName .
    '"'
);

header(
    'Cache-Control: max-age=0'
);

header(
    'Pragma: public'
);

// Simpan
$writer = IOFactory::createWriter(
    $phpWord,
    'Word2007'
);

$writer->save(
    'php://output'
);

exit;