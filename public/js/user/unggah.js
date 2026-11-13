(function () {
    var form = document.getElementById('form-unggah');

    if (!form) {
        return;
    }

    var input = document.getElementById('file_dokumen');
    var fileError = form.querySelector('[data-file-error]');
    var fileHint = form.querySelector('[data-file-hint]');
    var submit = form.querySelector('[data-submit]');
    var popup = document.querySelector('[data-popup]');
    var popupNama = document.querySelector('[data-popup-nama]');
    var popupUkuran = document.querySelector('[data-popup-ukuran]');
    var popupClose = document.querySelector('[data-popup-close]');
    var allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg'];
    var maxBytes = 20 * 1024 * 1024;
    var sending = false;

    function showError(message) {
        input.classList.add('is-invalid');
        fileError.textContent = message;
        fileError.hidden = false;
        fileHint.hidden = true;
    }

    function clearError() {
        input.classList.remove('is-invalid');
        fileError.textContent = '';
        fileError.hidden = true;
        fileHint.hidden = false;
    }

    function openPopup(name, size) {
        popupNama.textContent = '"' + name + '"';
        popupUkuran.textContent = size;
        popup.hidden = false;
        popupClose.focus();
    }

    function closePopup() {
        popup.hidden = true;
        input.focus();
    }

    function resetSubmit() {
        sending = false;
        submit.disabled = false;
        submit.textContent = 'Unggah & Simpan';
    }

    input.addEventListener('change', function () {
        if (!input.files || !input.files.length) {
            clearError();
            return;
        }

        var file = input.files[0];
        var ext = file.name.split('.').pop().toLowerCase();
        var sizeMb = (file.size / 1048576).toFixed(2);

        if (allowed.indexOf(ext) === -1) {
            input.value = '';
            showError('Format berkas harus berupa PDF, DOC, DOCX, XLS, XLSX, PNG, atau JPG.');
            return;
        }

        if (file.size > maxBytes) {
            input.value = '';
            showError('Ukuran berkas terlalu besar (' + sizeMb + ' MB). Maksimal ukuran yang diizinkan adalah 20 MB.');
            openPopup(file.name, sizeMb + ' MB');
            return;
        }

        clearError();
    });

    popupClose.addEventListener('click', closePopup);

    popup.addEventListener('click', function (event) {
        if (event.target === popup) {
            closePopup();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !popup.hidden) {
            closePopup();
        }
    });

    document.querySelectorAll('[data-alert-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            button.closest('.ug-alert').remove();
        });
    });

    form.addEventListener('submit', function (event) {
        if (sending) {
            event.preventDefault();
            return;
        }

        sending = true;
        submit.innerHTML = '<span class="ug-spinner" role="status" aria-hidden="true"></span>Mengunggah...';

        window.setTimeout(function () {
            submit.disabled = true;
        }, 0);
    });

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            resetSubmit();
        }
    });
})();

(function () {
    var modal = document.querySelector('[data-modal]');

    if (!modal) {
        return;
    }

    function open() {
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function close() {
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-modal-open]').forEach(function (button) {
        button.addEventListener('click', open);
    });

    modal.querySelectorAll('[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', close);
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            close();
        }
    });

    document.addEventListener('keydown', function (event) {
        var popup = document.querySelector('[data-popup]');

        if (event.key === 'Escape' && !modal.hidden && (!popup || popup.hidden)) {
            close();
        }
    });

    if (!modal.hidden || window.location.hash === '#unggah') {
        open();
    }
})();