(function () {
    var cekSemua = document.getElementById('cek-semua');
    var tombol = document.getElementById('btn-ajukan');
    var jumlah = document.getElementById('jumlah-pilih');
    var kotak = Array.prototype.slice.call(document.querySelectorAll('.cek-item'));

    if (!tombol || !jumlah) {
        return;
    }

    function kotakAktif() {
        return kotak.filter(function (k) { return !k.disabled; });
    }

    function segarkan() {
        var aktif = kotakAktif();
        var n = aktif.filter(function (k) { return k.checked; }).length;

        jumlah.textContent = n;
        tombol.disabled = n === 0;

        if (cekSemua) {
            cekSemua.checked = aktif.length > 0 && n === aktif.length;
            cekSemua.indeterminate = n > 0 && n < aktif.length;
            cekSemua.disabled = aktif.length === 0;
        }
    }

    kotak.forEach(function (k) {
        k.addEventListener('change', segarkan);
    });

    if (cekSemua) {
        cekSemua.addEventListener('change', function () {
            var status = cekSemua.checked;

            kotakAktif().forEach(function (k) {
                k.checked = status;
            });

            segarkan();
        });
    }

    window.addEventListener('pageshow', segarkan);

    segarkan();
})();