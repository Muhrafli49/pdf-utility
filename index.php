<?php
?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Merge PDF</title>


    <!-- =========================
         CUSTOM CSS
    ========================= -->

   <link
        rel="stylesheet"
        href="assets/style.css"
    >

    <!-- =========================
         BOOTSTRAP CSS
    ========================= -->

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

            <!-- BRAND -->
            <a
                class="navbar-brand"
                href="index.php"
            >
                <div class="brand-icon">
                    📄
                </div>

                <div class="brand-text">
                    Merge PDF
                    <small>
                        PDF Utility Tool
                    </small>
                </div>
            </a>


            <!-- MENU -->
            <div class="navbar-menu">

                <a
                    href="index.php"
                    class="navbar-menu-link active"
                >
                    Merge PDF
                </a>

                <a
                    href="convert-word.php"
                    class="navbar-menu-link"
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


            <!-- =========================
                 PAGE HEADER
            ========================= -->

            <div class="page-header">

                <div class="page-badge">
                    ⚡ Simple &nbsp;•&nbsp; Fast &nbsp;•&nbsp; Free
                </div>

                <h1>
                    Gabungkan PDF
                </h1>

                <p>
                    Gabungkan beberapa file PDF menjadi satu dokumen dengan mudah.
                </p>

            </div>


            <!-- =========================
                 MERGE CARD
            ========================= -->

            <div class="row justify-content-center">

                <div class="col-lg-8 col-xl-7">

                    <div class="merge-card">


                        <!-- =========================
                             FORM
                        ========================= -->

                        <form
                            action="merge.php"
                            method="POST"
                            enctype="multipart/form-data"
                            id="mergeForm"
                        >


                            <!-- =========================
                                 UPLOAD AREA
                            ========================= -->

                            <div class="upload-box">

                                <div class="upload-icon-wrapper">

                                    <div class="upload-icon">
                                        📄
                                    </div>

                                </div>


                                <h4 class="fw-bold">
                                    Pilih File PDF
                                </h4>


                                <p>
                                    Pilih satu atau beberapa file PDF untuk digabungkan
                                </p>


                                <div class="file-input-wrapper">

                                    <input
                                        type="file"
                                        id="pdf_files"
                                        class="form-control"
                                        accept=".pdf,application/pdf"
                                        multiple
                                    >

                                </div>

                            </div>


                            <!-- =========================
                                 ADD FILE
                            ========================= -->

                            <div
                                id="addFileWrapper"
                                class="add-file-wrapper"
                                style="display: none;"
                            >

                                <button
                                    type="button"
                                    class="add-file-button"
                                    id="addFileButton"
                                >
                                    + Tambah File
                                </button>

                            </div>


                            <!-- =========================
                                 SELECTED FILES
                            ========================= -->

                            <div
                                id="selectedFiles"
                                class="selected-files"
                            ></div>


                            <!-- =========================
                                 MERGE BUTTON
                            ========================= -->

                            <div class="merge-button-wrapper">

                                <button
                                    type="submit"
                                    class="merge-button"
                                    id="mergeButton"
                                >
                                    🔗 &nbsp; Merge PDF
                                </button>

                            </div>


                        </form>

                    </div>


                    <!-- =========================
                         FOOTER
                    ========================= -->

                    <div class="footer-text">
                        Merge PDF Tool
                        <span>•</span>
                        Local Processing
                        <span>•</span>
                        Developed by <strong>Raplh</strong>
                    </div>


                </div>

            </div>

        </div>

    </main>


    <!-- =========================
         BOOTSTRAP JS
    ========================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>


    <!-- =========================
         CUSTOM JS
    ========================= -->

    <script src="assets/app.js"></script>


</body>

</html>