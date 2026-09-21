<?php
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Convert PDF to Word</title>

    <link rel="stylesheet" href="assets/style.css">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

    <!-- =========================
         NAVBAR
    ========================= -->
    <nav class="navbar navbar-custom">
        <div class="container">

            <a
                class="navbar-brand"
                href="index.php"
            >
                <div class="brand-icon">
                    📄
                </div>

                <div class="brand-text">
                    Convert PDF
                    <small>
                        PDF Utility Tool
                    </small>
                </div>
            </a>


            <div class="navbar-menu">

                <a
                    href="index.php"
                    class="navbar-menu-link"
                >
                    Merge PDF
                </a>

                <a
                    href="convert-word.php"
                    class="navbar-menu-link active"
                >
                    Convert to Word
                </a>

                <a
                    href="compress-pdf.php"
                    class="navbar-menu-link"
                >
                    Compress PDF
                </a>

            </div>

        </div>
    </nav>


    <!-- =========================
         MAIN
    ========================= -->
    <main class="main-wrapper">

        <div class="container">

            <div class="page-header">

                <div class="page-badge">
                    📝 &nbsp; PDF to Word
                </div>

                <h1>
                    Convert PDF ke Word
                </h1>

                <p>
                    Ubah file PDF menjadi dokumen Word dengan mudah.
                </p>

            </div>


            <div class="row justify-content-center">

                <div class="col-lg-8 col-xl-7">

                    <div class="merge-card">

                        <form
                            action="convert.php"
                            method="POST"
                            enctype="multipart/form-data"
                            id="convertForm"
                        >
                            <div class="upload-box">

                                <div class="upload-icon-wrapper">

                                    <div class="upload-icon">
                                        📝
                                    </div>

                                </div>


                                <h4 class="fw-bold">
                                    Pilih File PDF
                                </h4>

                                <p>
                                    Pilih file PDF yang ingin dikonversi menjadi Word
                                </p>


                                <div class="file-input-wrapper">

                                    <input
                                        type="file"
                                        id="pdf_file"
                                        name="pdf_file"
                                        class="form-control"
                                        accept=".pdf,application/pdf"
                                    >

                                </div>

                            </div>


                            <!-- FILE YANG DIPILIH -->
                            <div
                                id="selectedFile"
                                class="selected-files"
                            ></div>


                            <!-- BUTTON -->
                            <div class="merge-button-wrapper">

                                <button
                                    type="submit"
                                    class="merge-button"
                                    id="convertButton"
                                >
                                    📝 &nbsp; Convert to Word
                                </button>

                            </div>

                        </form>

                    </div>


                    <!-- FOOTER -->
                    <div class="footer-text">

                        PDF Utility Tool

                        <span>•</span>

                        Local Processing

                        <span>•</span>

                        Developed by <strong>Raplh</strong>

                    </div>

                </div>

            </div>

        </div>

    </main>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    <script src="assets/app.js"></script>

</body>

</html>