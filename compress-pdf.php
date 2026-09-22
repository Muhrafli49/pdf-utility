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

    <title>Compress PDF</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="assets/style.css"
    >
</head>

<body>

    <!-- Navbar -->
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
                    Compress PDF
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
                    class="navbar-menu-link"
                >
                    Convert to Word
                </a>

                <a
                    href="compress-pdf.php"
                    class="navbar-menu-link active"
                >
                    Compress PDF
                </a>

            </div>
        </div>
    </nav>

    <!-- Main -->
    <main class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-8 col-xl-7">

                <!-- Header -->
                <div class="text-center mb-4">

                    <div class="fs-1 mb-2">
                        🗜️
                    </div>

                    <h2 class="fw-bold">
                        Compress PDF
                    </h2>

                    <p class="text-muted mb-0">
                        Kurangi ukuran file PDF dengan tetap menjaga kualitas dokumen.
                    </p>

                </div>

                <!-- Card -->
                <div class="compress-card">

                    <div class="compress-card-top"></div>

                    <div class="compress-card-body">

                        <form
                            action="compress.php"
                            method="POST"
                            enctype="multipart/form-data"
                            id="compressForm"
                        >

                            <!-- Upload -->
                            <div class="mb-4">

                                <label
                                    for="pdf_file"
                                    class="form-label fw-semibold"
                                >
                                    Pilih File PDF
                                </label>

                                <input
                                    type="file"
                                    class="form-control"
                                    id="pdf_file"
                                    name="pdf_file"
                                    accept=".pdf,application/pdf"
                                    required
                                >

                                <div class="form-text">
                                    Maksimal 1 file PDF.
                                </div>

                            </div>

                            <!-- Compression Level -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Tingkat Kompresi
                                </label>

                                <div class="row g-3">

                                    <!-- Rendah -->
                                    <div class="col-md-4">

                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="compression"
                                            id="compressionLow"
                                            value="low"
                                        >

                                        <label
                                            class="btn btn-outline-primary w-100 text-start p-3 compression-option"
                                            for="compressionLow"
                                        >
                                            <div class="fw-bold">
                                                Rendah
                                            </div>

                                            <small class="text-muted">
                                                Kualitas lebih baik
                                            </small>
                                        </label>

                                    </div>

                                    <!-- Sedang -->
                                    <div class="col-md-4">

                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="compression"
                                            id="compressionMedium"
                                            value="medium"
                                            checked
                                        >

                                        <label
                                            class="btn btn-outline-primary w-100 text-start p-3 compression-option"
                                            for="compressionMedium"
                                        >
                                            <div class="fw-bold">
                                                Sedang
                                            </div>

                                            <small class="text-muted">
                                                Seimbang
                                            </small>
                                        </label>

                                    </div>

                                    <!-- Tinggi -->
                                    <div class="col-md-4">

                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="compression"
                                            id="compressionHigh"
                                            value="high"
                                        >

                                        <label
                                            class="btn btn-outline-primary w-100 text-start p-3 compression-option"
                                            for="compressionHigh"
                                        >
                                            <div class="fw-bold">
                                                Tinggi
                                            </div>

                                            <small class="text-muted">
                                                Ukuran lebih kecil
                                            </small>
                                        </label>

                                    </div>

                                </div>
                            </div>

                            <!-- Info -->
                            <div class="alert alert-light border mb-4">

                                <div class="d-flex gap-2">

                                    <div>
                                        💡
                                    </div>

                                    <div>
                                        <strong>
                                            Tips:
                                        </strong>

                                        Pilih tingkat
                                        <strong>
                                            Sedang
                                        </strong>
                                        untuk hasil yang seimbang antara ukuran file
                                        dan kualitas dokumen.
                                    </div>

                                </div>

                            </div>

                            <!-- Submit -->
                            <div class="d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                    id="compressButton"
                                >
                                    🗜️ &nbsp; Compress PDF
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

                <!-- Footer Info -->
                <div class="text-center text-muted small mt-4">
                    File diproses secara lokal menggunakan Ghostscript.
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-muted py-4">

        <small>
            Developed by <strong>Raplh</strong>
        </small>

    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script -->
    <script>
        document
            .getElementById('compressForm')
            .addEventListener('submit', function () {

                const button =
                    document.getElementById('compressButton');

                button.disabled = true;

                button.innerHTML =
                    '⏳ &nbsp; Memproses PDF...';
            });
    </script>

</body>

</html>