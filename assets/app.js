// ============================
// ELEMENT
// ============================

const fileInput =
    document.getElementById('pdf_files');

const selectedFilesContainer =
    document.getElementById('selectedFiles');

const addFileButton =
    document.getElementById('addFileButton');

const addFileWrapper =
    document.getElementById('addFileWrapper');

const mergeForm =
    document.getElementById('mergeForm');

const mergeButton =
    document.getElementById('mergeButton');


// ============================
// MENYIMPAN SELURUH FILE
// ============================

let allFiles = [];


// ============================
// PILIH FILE
// ============================

fileInput.addEventListener('change', function () {

    const newFiles =
        Array.from(this.files);


    newFiles.forEach(file => {

        // ============================
        // VALIDASI PDF
        // ============================

        if (
            file.type !== 'application/pdf' &&
            !file.name.toLowerCase().endsWith('.pdf')
        ) {
            alert(
                `${file.name} bukan file PDF.`
            );

            return;
        }


        // ============================
        // CEK DUPLIKAT
        // ============================

        const alreadyExists =
            allFiles.some(existingFile =>

                existingFile.name === file.name &&
                existingFile.size === file.size &&
                existingFile.lastModified === file.lastModified

            );


        if (!alreadyExists) {

            allFiles.push(file);

        }

    });


    // ============================
    // RESET INPUT
    // ============================

    this.value = '';


    // ============================
    // RENDER
    // ============================

    renderFiles();

});


// ============================
// TAMBAH FILE
// ============================

addFileButton.addEventListener(
    'click',
    function () {

        fileInput.click();

    }
);


// ============================
// RENDER FILE
// ============================

function renderFiles() {

    selectedFilesContainer.innerHTML = '';


    // ============================
    // JIKA TIDAK ADA FILE
    // ============================

    if (allFiles.length === 0) {

        addFileWrapper.style.display =
            'none';

        return;

    }


    // ============================
    // HEADER
    // ============================

    const titleWrapper =
        document.createElement('div');

    titleWrapper.className =
        'selected-files-title';


    const title =
        document.createElement('h6');

    title.innerText =
        'File yang dipilih';


    const count =
        document.createElement('span');

    count.className =
        'file-count';

    count.innerText =
        allFiles.length + ' FILE';


    titleWrapper.appendChild(title);

    titleWrapper.appendChild(count);

    selectedFilesContainer.appendChild(
        titleWrapper
    );


    // ============================
    // FILE LIST
    // ============================

    allFiles.forEach((file, index) => {

        const fileItem =
            document.createElement('div');

        fileItem.className =
            'file-item d-flex justify-content-between align-items-center';


        fileItem.innerHTML = `

            <div class="file-name">

                <div class="pdf-icon">
                    PDF
                </div>

                <div
                    class="file-name-text"
                    title="${escapeHtml(file.name)}"
                >
                    ${escapeHtml(file.name)}
                </div>

            </div>


            <div class="d-flex align-items-center gap-3">

                <div class="file-size">
                    ${formatFileSize(file.size)}
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger remove-file"
                    data-index="${index}"
                    title="Hapus file"
                >
                    ×
                </button>

            </div>

        `;


        selectedFilesContainer.appendChild(
            fileItem
        );

    });


    // ============================
    // TAMPILKAN TOMBOL TAMBAH
    // ============================

    addFileWrapper.style.display =
        'block';


    // ============================
    // EVENT HAPUS
    // ============================

    document
        .querySelectorAll('.remove-file')
        .forEach(button => {

            button.addEventListener(
                'click',
                function () {

                    const index =
                        parseInt(
                            this.dataset.index
                        );


                    allFiles.splice(
                        index,
                        1
                    );


                    renderFiles();

                }
            );

        });

}


// ============================
// FORMAT UKURAN FILE
// ============================

function formatFileSize(bytes) {

    if (bytes === 0) {

        return '0 Bytes';

    }


    const units = [
        'Bytes',
        'KB',
        'MB',
        'GB'
    ];


    const index =
        Math.floor(
            Math.log(bytes) /
            Math.log(1024)
        );


    return (
        parseFloat(
            (
                bytes /
                Math.pow(1024, index)
            ).toFixed(2)
        )
        + ' '
        + units[index]
    );

}


// ============================
// ESCAPE HTML
// ============================

function escapeHtml(text) {

    const div =
        document.createElement('div');


    div.textContent =
        text;


    return div.innerHTML;

}


// ============================
// SUBMIT / MERGE PDF
// ============================

mergeForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        // ============================
        // VALIDASI
        // ============================

        if (allFiles.length === 0) {

            alert(
                'Silakan pilih minimal satu file PDF.'
            );

            return;

        }


        // ============================
        // LOADING
        // ============================

        mergeButton.disabled =
            true;


        mergeButton.innerHTML =
            '⏳ &nbsp; Sedang Memproses...';


        try {

            // ============================
            // SIAPKAN FORM DATA
            // ============================

            const formData =
                new FormData();


            allFiles.forEach(file => {

                formData.append(
                    'pdf_files[]',
                    file
                );

            });


            // ============================
            // KIRIM KE MERGE.PHP
            // ============================

            const response =
                await fetch(
                    'merge.php',
                    {
                        method: 'POST',
                        body: formData
                    }
                );


            // ============================
            // CEK RESPONSE
            // ============================

            if (!response.ok) {

                throw new Error(
                    'Gagal memproses PDF.'
                );

            }


            // ============================
            // AMBIL PDF
            // ============================

            const blob =
                await response.blob();


            // ============================
            // AMBIL NAMA FILE
            // ============================

            let fileName =
                'merged.pdf';


            const disposition =
                response.headers.get(
                    'Content-Disposition'
                );


            if (disposition) {

                const match =
                    disposition.match(
                        /filename="?([^"]+)"?/i
                    );


                if (
                    match &&
                    match[1]
                ) {

                    fileName =
                        match[1];

                }

            }


            // ============================
            // DOWNLOAD PDF
            // ============================

            const url =
                window.URL.createObjectURL(
                    blob
                );


            const link =
                document.createElement('a');


            link.href =
                url;


            link.download =
                fileName;


            document.body.appendChild(
                link
            );


            link.click();

            link.remove();

            setTimeout(() => {
                window.URL.revokeObjectURL(url);
            }, 1000);


            // ============================
            // RESET FILE SETELAH BERHASIL
            // ============================

            allFiles = [];

            renderFiles();


            // ============================
            // KEMBALIKAN TOMBOL
            // ============================

            mergeButton.disabled = false;

            mergeButton.innerHTML =
                '🔗 &nbsp; Merge PDF';


            mergeButton.innerHTML =
                '🔗 &nbsp; Merge PDF';


        } catch (error) {

            console.error(error);


            alert(
                'Terjadi kesalahan saat menggabungkan PDF.'
            );


            // ============================
            // RESET BUTTON
            // ============================

            mergeButton.disabled =
                false;


            mergeButton.innerHTML =
                '🔗 &nbsp; Merge PDF';

        }

    }
);

// =========================================================
// PDF TO WORD
// =========================================================

const pdfFileInput = document.getElementById('pdf_file');
const convertButton = document.getElementById('convertButton');
const selectedFile = document.getElementById('selectedFile');

if (pdfFileInput) {

    pdfFileInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {

            convertButton.disabled = true;

            selectedFile.innerHTML = '';

            return;
        }


        // Tampilkan informasi file
        const fileSize = (
            file.size / 1024 / 1024
        ).toFixed(2);


        selectedFile.innerHTML = `
            <div class="selected-files-title">
                <h6>File yang dipilih</h6>

                <span class="file-count">
                    1 File
                </span>
            </div>

            <div class="file-item">

                <div class="pdf-icon">
                    PDF
                </div>

                <div class="file-info">

                    <div class="file-name-text">
                        ${file.name}
                    </div>

                    <div class="file-size">
                        ${fileSize} MB
                    </div>

                </div>

            </div>
        `;


        // Aktifkan tombol
        convertButton.disabled = false;

    });

}