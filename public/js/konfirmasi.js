(function () {
    const overlay = document.getElementById('kf-overlay');
    if (!overlay) {
        return;
    }

    const pesan = document.getElementById('kf-pesan');
    const tombolYa = document.getElementById('kf-ya');
    const tombolBatal = document.getElementById('kf-batal');
    let formTertunda = null;
    let fokusSebelumnya = null;

    function buka(form) {
        formTertunda = form;
        fokusSebelumnya = document.activeElement;
        pesan.textContent = form.dataset.konfirmasi;
        tombolYa.textContent = form.dataset.konfirmasiTombol || 'Ya, Hapus';
        overlay.hidden = false;
        document.body.classList.add('kf-terkunci');
        tombolBatal.focus();
    }

    function tutup() {
        overlay.hidden = true;
        document.body.classList.remove('kf-terkunci');
        formTertunda = null;
        if (fokusSebelumnya && fokusSebelumnya.isConnected) {
            fokusSebelumnya.focus();
        }
    }

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.konfirmasi) {
            return;
        }
        event.preventDefault();
        buka(form);
    });

    tombolYa.addEventListener('click', function () {
        const form = formTertunda;
        if (!form) {
            return;
        }
        tombolYa.disabled = true;
        form.submit();
    });

    tombolBatal.addEventListener('click', tutup);

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            tutup();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (overlay.hidden) {
            return;
        }
        if (event.key === 'Escape') {
            tutup();
        }
        if (event.key === 'Tab') {
            const fokusable = [tombolBatal, tombolYa];
            const indeks = fokusable.indexOf(document.activeElement);
            event.preventDefault();
            const berikutnya = event.shiftKey
                ? fokusable[(indeks - 1 + fokusable.length) % fokusable.length]
                : fokusable[(indeks + 1) % fokusable.length];
            berikutnya.focus();
        }
    });
})();