(function () {
    var form = document.getElementById('form-unggah');

    if (!form) {
        return;
    }

    var input = document.getElementById('file_dokumen');
    var drop = form.querySelector('[data-drop]');
    var fileBox = form.querySelector('[data-file]');
    var fileName = form.querySelector('[data-file-name]');
    var fileMeta = form.querySelector('[data-file-meta]');
    var fileRemove = form.querySelector('[data-file-remove]');
    var fileError = form.querySelector('[data-file-error]');
    var tanggal = document.getElementById('tanggal_dokumen');
    var tahun = form.querySelector('[data-tahun]');
    var submit = form.querySelector('[data-submit]');
    var submitText = form.querySelector('[data-submit-text]');
    var spinner = form.querySelector('[data-spinner]');
    var progress = form.querySelector('[data-progress]');
    var allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    var maxBytes = 20 * 1024 * 1024;
    var sending = false;

    function formatSize(bytes) {
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(1).replace('.', ',') + ' MB';
        }

        if (bytes >= 1024) {
            return Math.round(bytes / 1024) + ' KB';
        }

        return bytes + ' B';
    }

    function showError(message) {
        fileError.textContent = message;
        fileError.hidden = false;
    }

    function clearError() {
        fileError.textContent = '';
        fileError.hidden = true;
    }

    function resetFile() {
        input.value = '';
        fileBox.hidden = true;
        drop.hidden = false;
    }

    function handleFile(file) {
        var ext = file.name.split('.').pop().toLowerCase();

        clearError();

        if (allowed.indexOf(ext) === -1) {
            resetFile();
            showError('Format berkas harus PDF, Word (DOC, DOCX), atau Excel (XLS, XLSX).');
            return;
        }

        if (file.size > maxBytes) {
            resetFile();
            showError('Ukuran berkas maksimal 20 MB.');
            return;
        }

        fileName.textContent = file.name;
        fileMeta.textContent = ext.toUpperCase() + ' · ' + formatSize(file.size);
        drop.hidden = true;
        fileBox.hidden = false;
    }

    function updateTahun() {
        var value = tanggal.value;

        tahun.textContent = /^\d{4}-\d{2}-\d{2}$/.test(value) ? value.slice(0, 4) : '-';
    }

    function resetSubmit() {
        sending = false;
        submit.disabled = false;
        submit.classList.remove('is-loading');
        spinner.hidden = true;
        progress.hidden = true;
        submitText.textContent = 'Kirim Dokumen';
    }

    input.addEventListener('change', function () {
        if (input.files && input.files.length) {
            handleFile(input.files[0]);
        } else {
            resetFile();
        }
    });

    ['dragenter', 'dragover'].forEach(function (name) {
        drop.addEventListener(name, function (event) {
            event.preventDefault();
            drop.classList.add('is-dragover');
        });
    });

    ['dragleave', 'drop'].forEach(function (name) {
        drop.addEventListener(name, function (event) {
            event.preventDefault();
            drop.classList.remove('is-dragover');
        });
    });

    drop.addEventListener('drop', function (event) {
        var files = event.dataTransfer && event.dataTransfer.files;

        if (files && files.length) {
            input.files = files;
            handleFile(files[0]);
        }
    });

    fileRemove.addEventListener('click', function () {
        resetFile();
        clearError();
    });

    tanggal.addEventListener('input', updateTahun);

    form.addEventListener('submit', function (event) {
        if (sending) {
            event.preventDefault();
            return;
        }

        sending = true;
        submit.classList.add('is-loading');
        spinner.hidden = false;
        progress.hidden = false;
        submitText.textContent = 'Mengirim dokumen...';

        window.setTimeout(function () {
            submit.disabled = true;
        }, 0);
    });

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            resetSubmit();
        }
    });

    updateTahun();
})();