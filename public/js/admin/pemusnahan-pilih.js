(function () {
    var cekSemua = document.getElementById('cek-semua');
    var tombol = document.getElementById('btn-ajukan');
    var jumlah = document.getElementById('jumlah-pilih');
    var kotak = Array.prototype.slice.call(document.querySelectorAll('.cek-item'));

    if (!tombol || !jumlah) {
        return;
    }

    function segarkan() {
        var n = kotak.filter(function (k) { return k.checked; }).length;
        jumlah.textContent = n;
        tombol.disabled = n === 0;
        if (cekSemua) {
            cekSemua.checked = n > 0 && n === kotak.length;
            cekSemua.indeterminate = n > 0 && n < kotak.length;
        }
    }

    kotak.forEach(function (k) {
        k.addEventListener('change', segarkan);
    });

    if (cekSemua) {
        cekSemua.addEventListener('change', function () {
            kotak.forEach(function (k) {
                if (!k.disabled) {
                    k.checked = cekSemua.checked;
                }
            });
            segarkan();
        });
    }

    segarkan();
})();