<x-admin.layout title="Ubah Data Sampel">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h5 fw-bold text-primary mb-1">Ubah Data Sampel</h1>
            <p class="text-muted mb-0 small" style="font-size: 0.8rem;">Perbarui identitas, ruang simpan, atau foto fisik sampel.</p>
        </div>
        <a href="{{ route('admin.sampel.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-1.5 d-flex align-items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    @include('admin.sampel._form', [
        'aksi' => route('admin.sampel.update', $sampel->id),
        'metode' => 'PUT',
        'sampel' => $sampel,
        'kembali' => route('admin.sampel.index'),
        'layananList' => $layananList,
    ])
</x-admin.layout>