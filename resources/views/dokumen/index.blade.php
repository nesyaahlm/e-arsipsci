@extends('layouts.app')

@section('judul', 'Dokumen')

@section('konten')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 fw-bold text-primary mb-1">Dokumen</h1>
        <p class="text-muted mb-0">Cari, ubah, dan kelola seluruh dokumen arsip.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 px-3 py-2 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUnggah">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span class="fw-semibold">Unggah Dokumen</span>
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 shadow-sm border-0 d-flex align-items-center justify-content-between" style="background-color: #e8f5e9; color: #1e4620;" role="alert">
        <div class="d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div>
                <strong>Sukses!</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-4 shadow-sm border-0 d-flex align-items-center justify-content-between" style="background-color: #fde8e8; color: #9b1c1c;" role="alert">
        <div class="d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div>
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-4 shadow-sm border-0" style="background-color: #fde8e8; color: #9b1c1c;" role="alert">
        <div class="d-flex align-items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <strong>Gagal Memproses Dokumen:</strong>
        </div>
        <ul class="mb-0 ps-4">
            @foreach($errors->all() as $pesanError)
                <li>{{ $pesanError }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm rounded-4 mb-4" style="border: 1.5px solid #cfdedb;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('dokumen.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <label class="form-label small text-muted fw-semibold mb-1">Cari</label>
                <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}" class="form-control ps-5 rounded-3" placeholder="Nomor atau judul dokumen">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small text-muted fw-semibold mb-1">Layanan</label>
                <select name="layanan_id" class="form-select rounded-3" onchange="this.form.submit()">
                    <option value="">Semua Layanan</option>
                    @foreach($layananList as $lay)
                        <option value="{{ $lay->id }}" {{ request('layanan_id') == $lay->id ? 'selected' : '' }}>{{ $lay->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small text-muted fw-semibold mb-1">Jenis Dokumen</label>
                <select name="jenis_dokumen_id" class="form-select rounded-3" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisList as $jns)
                        <option value="{{ $jns->id }}" {{ request('jenis_dokumen_id') == $jns->id ? 'selected' : '' }}>{{ $jns->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-1 d-flex align-items-end">
                <div class="w-100">
                    <label class="form-label small text-white d-none d-md-block mb-1">&nbsp;</label>
                    <a href="{{ route('dokumen.index') }}" class="btn btn-outline-secondary rounded-3 w-100" title="Reset Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                            <path d="M3 3v5h5"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 align-items-start">
    <div id="kolomTabel" class="col-12 transition-all">
        <div class="card shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap" style="min-width: 880px;">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th class="ps-4 py-3" style="min-width: 170px;">Nomor</th>
                            <th class="py-3" style="min-width: 240px; white-space: normal;">Judul Dokumen</th>
                            <th class="py-3" style="min-width: 160px;">Layanan / Jenis</th>
                            <th class="py-3" style="min-width: 140px;">Tanggal / Retensi</th>
                            <th class="py-3" style="min-width: 180px;">Lokasi Fisik</th>
                            <th class="text-center pe-4 py-3" style="min-width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumen as $item)
                            @php
                                $ekstensi = strtolower(pathinfo($item->file_path, PATHINFO_EXTENSION));
                            @endphp
                            <tr class="baris-dokumen" id="baris-{{ $item->id }}">
                                <td class="ps-4 fw-semibold text-primary font-monospace" style="font-size: 0.88rem;">
                                    {{ $item->nomor ?? '-' }}
                                </td>

                                <td style="min-width: 240px; white-space: normal;">
                                    <a href="javascript:void(0)" 
                                       class="fw-semibold text-decoration-none text-dark d-block btn-buka-preview"
                                       data-id="{{ $item->id }}"
                                       data-nomor="{{ $item->nomor }}"
                                       data-judul="{{ $item->judul }}"
                                       data-ekstensi="{{ $ekstensi }}"
                                       data-download="{{ route('dokumen.download', $item) }}"
                                       data-preview="{{ route('dokumen.preview', $item) }}">
                                        {{ $item->judul }}
                                    </a>
                                    @if($item->unit)
                                        <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.72rem;">Unit: {{ $item->unit }}</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold text-dark" style="font-size: 0.88rem;">{{ $item->layanan->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $item->jenisDokumen->nama ?? '-' }}</small>
                                </td>

                                <td>
                                    <div class="small text-dark">{{ $item->tanggal_dokumen ? $item->tanggal_dokumen->format('d/m/Y') : '-' }}</div>
                                    @if($item->retensi_sampai)
                                        <small class="text-muted">Retensi: {{ $item->retensi_sampai->format('d/m/Y') }}</small>
                                    @endif
                                </td>

                                <td>
                                    @if($item->lokasi_arsip_fisik)
                                        <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.78rem;">
                                            {{ $item->lokasi_arsip_fisik }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary border-0 btn-buka-preview" 
                                                data-id="{{ $item->id }}"
                                                data-nomor="{{ $item->nomor }}"
                                                data-judul="{{ $item->judul }}"
                                                data-ekstensi="{{ $ekstensi }}"
                                                data-download="{{ route('dokumen.download', $item) }}"
                                                data-preview="{{ route('dokumen.preview', $item) }}"
                                                title="Pratinjau Dokumen">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>

                                        <a href="{{ route('dokumen.download', $item) }}" class="btn btn-sm btn-outline-secondary border-0" title="Unduh Berkas">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-secondary border-0 btn-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalUbah"
                                            data-id="{{ $item->id }}"
                                            data-nomor="{{ $item->nomor }}"
                                            data-judul="{{ $item->judul }}"
                                            data-unit="{{ $item->unit }}"
                                            data-lokasi="{{ $item->lokasi_arsip_fisik }}"
                                            data-deskripsi="{{ $item->deskripsi }}"
                                            data-file="{{ $item->file_nama_asli ?: basename($item->file_path) }}"
                                            title="Ubah Metadata">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalHapus"
                                                data-id="{{ $item->id }}"
                                                data-nomor="{{ $item->nomor }}"
                                                title="Hapus Dokumen">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <p class="mb-0">Belum ada dokumen yang terdaftar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($dokumen->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $dokumen->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="panelDetail" class="col-12 col-lg-5 d-none">
        <div class="card shadow-sm rounded-4 position-sticky" style="top: 1rem; border: 1.5px solid #a3cfbb; background-color: #f7fcfa;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div id="previewNomor" class="fw-bold text-primary font-monospace" style="font-size: 0.95rem;"></div>
                        <div id="previewJudul" class="fw-semibold text-dark text-truncate" style="max-width: 250px; font-size: 0.9rem;"></div>
                    </div>
                    <a href="javascript:void(0)" id="btnTutupDetail" class="small text-primary text-decoration-none fw-semibold">
                        Tutup
                    </a>
                </div>

                <div class="mb-3 position-relative" style="height: 520px; background-color: #e9ecef; border-radius: 0.5rem; overflow: hidden; border: 1px solid #d1d5db;">
                    <iframe id="framePreview" src="" class="w-100 h-100 border-0" style="background-color: #fff; display: none;"></iframe>
                    
                    <div id="noticeNonPreview" class="w-100 h-100 flex-column align-items-center justify-content-center p-4 text-center bg-white" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2a7f72" stroke-width="1.5" class="mb-3">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        <h6 id="noticeJudulEkstensi" class="fw-bold text-dark mb-1"></h6>
                        <p class="text-muted small mb-3">Format ini tidak mendukung pratinjau langsung di dalam peramban web. Silakan unduh berkas untuk melihat isinya.</p>
                        <a id="btnNoticeDownload" href="#" class="btn btn-sm btn-primary rounded-3 px-3 py-2 fw-semibold">
                            Unduh Berkas Sekarang
                        </a>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a id="btnDetailDownload" href="#" class="btn btn-primary rounded-3 px-3 py-2 flex-grow-1 text-center fw-semibold">
                        Unduh
                    </a>
                    <button type="button" id="btnDetailUbah" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                        Ubah
                    </button>
                    <button type="button" id="btnDetailHapus" class="btn btn-outline-danger rounded-3 px-3 py-2">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUnggah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-primary">Unggah Dokumen Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formUnggah" action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-light border rounded-3 p-3 small mb-3 text-secondary d-flex gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2a7f72" stroke-width="2" class="flex-shrink-0 mt-1">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span><strong>Nomor Dokumen</strong> dan <strong>Masa Retensi</strong> akan dihasilkan secara otomatis oleh sistem berdasarkan jenis dokumen dan tanggal dokumen yang dipilih.</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Judul Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control rounded-3" placeholder="Contoh: Laporan Hasil Uji Batubara Lot 2610-C" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Layanan <span class="text-danger">*</span></label>
                            <select name="layanan_id" id="modalLayanan" class="form-select rounded-3" required>
                                <option value="" disabled selected>Pilih Layanan</option>
                                @foreach($layananList as $lay)
                                    <option value="{{ $lay->id }}" {{ old('layanan_id') == $lay->id ? 'selected' : '' }}>{{ $lay->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Jenis Dokumen <span class="text-danger">*</span></label>
                            <select name="jenis_dokumen_id" id="modalJenis" class="form-select rounded-3" required>
                                <option value="" disabled selected>Pilih Layanan Terlebih Dahulu</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Tanggal Dokumen <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_dokumen" value="{{ old('tanggal_dokumen', date('Y-m-d')) }}" class="form-control rounded-3" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Unit Kerja</label>
                            <input type="text" name="unit" value="{{ old('unit') }}" class="form-control rounded-3" placeholder="Contoh: Cilacap">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Berkas File <span class="text-danger">*</span></label>
                            <input type="file" name="file_dokumen" id="inputBerkasUnggah" class="form-control rounded-3" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg" required>
                            <div id="errorBerkasUnggah" class="text-danger small mt-1 fw-semibold" style="display: none;"></div>
                            <small id="hintBerkasUnggah" class="text-muted" style="font-size: 0.72rem;">Format: PDF, DOCX, XLSX (Maks. 20MB)</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Nomor Rak</label>
                            <input type="text" name="nomor_rak" value="{{ old('nomor_rak') }}" class="form-control rounded-3" placeholder="Contoh: 01">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">No. Tingkat</label>
                            <input type="text" name="no_tingkat" value="{{ old('no_tingkat') }}" class="form-control rounded-3" placeholder="Contoh: 2">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-secondary">No. Box File</label>
                            <input type="text" name="no_box" value="{{ old('no_box') }}" class="form-control rounded-3" placeholder="Contoh: 04">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Deskripsi / Isi Dokumen</label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="2" placeholder="Contoh: - Kontrak Pengadaan">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitUnggah" class="btn btn-primary rounded-3 px-4">Unggah & Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUbah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-primary mb-1">Ubah Metadata Dokumen</h5>
                    <span id="editNomorDokumen" class="badge bg-light text-primary border font-monospace" style="font-size: 0.8rem;"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formUbah" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Judul Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="editJudul" class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Unit Kerja</label>
                        <input type="text" name="unit" id="editUnit" class="form-control rounded-3" placeholder="Contoh: Cilacap">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-1">Lokasi Arsip Fisik</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label text-muted" style="font-size: 0.72rem;">Nomor Rak</label>
                                <input type="text" name="nomor_rak" id="editNomorRak" class="form-control rounded-3" placeholder="Contoh: 01">
                            </div>
                            <div class="col-4">
                                <label class="form-label text-muted" style="font-size: 0.72rem;">No. Tingkat</label>
                                <input type="text" name="no_tingkat" id="editNoTingkat" class="form-control rounded-3" placeholder="Contoh: 2">
                            </div>
                            <div class="col-4">
                                <label class="form-label text-muted" style="font-size: 0.72rem;">No. Box File</label>
                                <input type="text" name="no_box" id="editNoBox" class="form-control rounded-3" placeholder="Contoh: 04">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-1">Berkas Dokumen</label>
                        
                        <div id="boxFileSaatIni" class="p-3 mb-2 bg-light border rounded-3 align-items-center justify-content-between" style="display: none;">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2a7f72" stroke-width="2" class="flex-shrink-0">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <div class="text-truncate">
                                    <div class="text-muted" style="font-size: 0.72rem;">Berkas saat ini:</div>
                                    <div id="editNamaFile" class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;"></div>
                                </div>
                            </div>
                            <span class="badge bg-white text-secondary border px-2 py-1" style="font-size: 0.72rem;">Tersimpan</span>
                        </div>

                        <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">Ganti Berkas Dokumen (Opsional)</label>
                        <input type="file" id="editFileInput" name="file_dokumen" class="form-control rounded-3" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg">
                        <div id="errorBerkasUbah" class="text-danger small mt-1 fw-semibold" style="display: none;"></div>
                        <small id="hintBerkasUbah" class="text-muted" style="font-size: 0.72rem;">Format: PDF, DOCX, XLSX (Maks. 20MB). Kosongkan jika tidak ingin mengganti berkas.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Deskripsi / Isi Dokumen</label>
                        <textarea name="deskripsi" id="editDeskripsi" class="form-control rounded-3" rows="2" placeholder="Contoh: - Kontrak Pengadaan"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-danger-subtle text-danger mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </div>
                <h5 class="fw-bold mb-2">Hapus Dokumen?</h5>
                <p class="text-muted small mb-3">Dokumen <strong id="hapusNomor" class="text-dark"></strong> akan dipindahkan ke arsip sampah.</p>
                
                <form id="formHapus" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-3 w-50" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitHapus" class="btn btn-danger rounded-3 w-50">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="popupPeringatanUkuran" class="position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center" style="background-color: rgba(0, 0, 0, 0.55); z-index: 9999;">
    <div class="card border-0 shadow-lg rounded-4 p-4 text-center mx-3" style="max-width: 420px; width: 100%; background: #ffffff;">
        <div class="d-inline-flex align-items-center justify-content-center mx-auto mb-3 rounded-circle" style="width: 60px; height: 60px; background-color: #fee2e2; color: #dc2626;">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h5 class="fw-bold text-dark mb-2">Ukuran Berkas Terlalu Besar</h5>
        <p class="text-muted small mb-3">
            Berkas <strong id="namaBerkasPopup" class="text-dark"></strong> melebihi batas maksimal yang diizinkan sistem.
        </p>
        <div class="p-2 mb-3 rounded-3 bg-light border small text-secondary">
            <div>Ukuran berkas Anda: <strong id="ukuranBerkasPopup" class="text-danger"></strong></div>
            <div>Batas maksimal berkas: <strong>20 MB</strong></div>
        </div>
        <p class="text-muted small mb-3">Silakan pilih kembali berkas lain yang berukuran lebih kecil.</p>
        <button type="button" id="btnTutupPopupUkuran" class="btn btn-primary rounded-3 w-100 py-2 fw-semibold">
            Saya Mengerti
        </button>
    </div>
</div>
@endsection

@push('skrip')
<script>
    const masterLayanan = @json($layananList);

    const modalLayanan = document.getElementById('modalLayanan');
    const modalJenis = document.getElementById('modalJenis');

    if (modalLayanan && modalJenis) {
        modalLayanan.addEventListener('change', function () {
            const layananId = parseInt(this.value);
            const selectedLayanan = masterLayanan.find(l => l.id === layananId);

            modalJenis.innerHTML = '<option value="" disabled selected>Pilih Jenis Dokumen</option>';

            if (selectedLayanan && selectedLayanan.jenis_dokumen && selectedLayanan.jenis_dokumen.length > 0) {
                selectedLayanan.jenis_dokumen.forEach(jenis => {
                    const opt = document.createElement('option');
                    opt.value = jenis.id;
                    opt.textContent = `${jenis.nama} (${jenis.kode})`;
                    modalJenis.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.textContent = 'Tidak ada jenis dokumen untuk layanan ini';
                modalJenis.appendChild(opt);
            }
        });
    }

    const kolomTabel = document.getElementById('kolomTabel');
    const panelDetail = document.getElementById('panelDetail');
    const btnTutupDetail = document.getElementById('btnTutupDetail');
    const framePreview = document.getElementById('framePreview');
    const noticeNonPreview = document.getElementById('noticeNonPreview');
    const noticeJudulEkstensi = document.getElementById('noticeJudulEkstensi');
    const btnNoticeDownload = document.getElementById('btnNoticeDownload');
    const previewNomor = document.getElementById('previewNomor');
    const previewJudul = document.getElementById('previewJudul');
    const btnDetailDownload = document.getElementById('btnDetailDownload');
    const btnDetailUbah = document.getElementById('btnDetailUbah');
    const btnDetailHapus = document.getElementById('btnDetailHapus');

    document.querySelectorAll('.btn-buka-preview').forEach(elem => {
        elem.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nomor = this.getAttribute('data-nomor');
            const judul = this.getAttribute('data-judul');
            const ekstensi = (this.getAttribute('data-ekstensi') || '').toLowerCase();
            const downloadUrl = this.getAttribute('data-download');
            const previewUrl = this.getAttribute('data-preview');

            kolomTabel.classList.remove('col-12');
            kolomTabel.classList.add('col-12', 'col-lg-7');
            panelDetail.classList.remove('d-none');

            previewNomor.textContent = nomor || '-';
            previewJudul.textContent = judul;
            btnDetailDownload.href = downloadUrl;

            if (ekstensi === 'pdf' || ['png', 'jpg', 'jpeg'].includes(ekstensi)) {
                framePreview.style.display = 'block';
                noticeNonPreview.style.display = 'none';
                framePreview.src = previewUrl;
            } else {
                framePreview.src = '';
                framePreview.style.display = 'none';
                noticeNonPreview.style.display = 'flex';
                noticeJudulEkstensi.textContent = 'Berkas .' + ekstensi.toUpperCase();
                btnNoticeDownload.href = downloadUrl;
            }

            btnDetailUbah.onclick = function () {
                const triggerBtn = document.querySelector(`.btn-edit[data-id="${id}"]`);
                if (triggerBtn) triggerBtn.click();
            };

            btnDetailHapus.onclick = function () {
                const triggerBtn = document.querySelector(`.btn-delete[data-id="${id}"]`);
                if (triggerBtn) triggerBtn.click();
            };

            document.querySelectorAll('.baris-dokumen').forEach(row => row.classList.remove('table-success'));
            const barisAktif = document.getElementById(`baris-${id}`);
            if (barisAktif) barisAktif.classList.add('table-success');
        });
    });

    btnTutupDetail.addEventListener('click', function () {
        framePreview.src = '';
        framePreview.style.display = 'none';
        noticeNonPreview.style.display = 'none';
        panelDetail.classList.add('d-none');
        kolomTabel.classList.remove('col-lg-7');
        kolomTabel.classList.add('col-12');

        document.querySelectorAll('.baris-dokumen').forEach(row => row.classList.remove('table-success'));
    });

    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nomor = this.getAttribute('data-nomor');
            const judul = this.getAttribute('data-judul');
            const unit = this.getAttribute('data-unit');
            const lokasi = this.getAttribute('data-lokasi') || '';
            const deskripsi = this.getAttribute('data-deskripsi');
            const file = this.getAttribute('data-file');

            document.getElementById('formUbah').action = `/dokumen/${id}`;
            const editNomorDokumen = document.getElementById('editNomorDokumen');
            if (editNomorDokumen) editNomorDokumen.textContent = nomor || '';

            document.getElementById('editJudul').value = judul || '';
            document.getElementById('editUnit').value = unit || '';
            document.getElementById('editDeskripsi').value = deskripsi || '';

            const matchRak = lokasi.match(/Rak\s*([^/]+)/i);
            const matchTingkat = lokasi.match(/Tingkat\s*([^/]+)/i);
            const matchBox = lokasi.match(/Box\s*([^/]+)/i);

            document.getElementById('editNomorRak').value = matchRak ? matchRak[1].trim() : (lokasi ? lokasi : '');
            document.getElementById('editNoTingkat').value = matchTingkat ? matchTingkat[1].trim() : '';
            document.getElementById('editNoBox').value = matchBox ? matchBox[1].trim() : '';

            const editFileInput = document.getElementById('editFileInput');
            if (editFileInput) {
                editFileInput.value = '';
                editFileInput.classList.remove('is-invalid');
            }

            const errorBerkasUbah = document.getElementById('errorBerkasUbah');
            if (errorBerkasUbah) errorBerkasUbah.style.display = 'none';

            const hintBerkasUbah = document.getElementById('hintBerkasUbah');
            if (hintBerkasUbah) hintBerkasUbah.style.display = 'block';

            const boxFileSaatIni = document.getElementById('boxFileSaatIni');
            const editNamaFile = document.getElementById('editNamaFile');

            if (file) {
                editNamaFile.textContent = file;
                boxFileSaatIni.style.display = 'flex';
            } else {
                boxFileSaatIni.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nomor = this.getAttribute('data-nomor');

            document.getElementById('formHapus').action = `/dokumen/${id}`;
            document.getElementById('hapusNomor').textContent = nomor || 'ini';
        });
    });

    const formHapus = document.getElementById('formHapus');
    const btnSubmitHapus = document.getElementById('btnSubmitHapus');
    const modalHapus = document.getElementById('modalHapus');

    if (formHapus && btnSubmitHapus) {
        formHapus.addEventListener('submit', function () {
            btnSubmitHapus.disabled = true;
            btnSubmitHapus.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Menghapus...';
        });
    }

    if (modalHapus && btnSubmitHapus) {
        modalHapus.addEventListener('hidden.bs.modal', function () {
            btnSubmitHapus.disabled = false;
            btnSubmitHapus.innerHTML = 'Hapus';
        });
    }

    const formUnggah = document.getElementById('formUnggah');
    const btnSubmitUnggah = document.getElementById('btnSubmitUnggah');
    const modalUnggah = document.getElementById('modalUnggah');

    if (formUnggah && btnSubmitUnggah) {
        formUnggah.addEventListener('submit', function () {
            btnSubmitUnggah.disabled = true;
            btnSubmitUnggah.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengunggah...';
        });
    }

    if (modalUnggah) {
        modalUnggah.addEventListener('hidden.bs.modal', function () {
            if (formUnggah) {
                formUnggah.reset();
                const modalJenis = document.getElementById('modalJenis');
                if (modalJenis) {
                    modalJenis.innerHTML = '<option value="" disabled selected>Pilih Layanan Terlebih Dahulu</option>';
                }
                const inputTanggal = formUnggah.querySelector('input[name="tanggal_dokumen"]');
                if (inputTanggal) {
                    inputTanggal.value = new Date().toISOString().split('T')[0];
                }
                if (btnSubmitUnggah) {
                    btnSubmitUnggah.disabled = false;
                    btnSubmitUnggah.innerHTML = 'Unggah & Simpan';
                }

                const errorBerkasUnggah = document.getElementById('errorBerkasUnggah');
                const hintBerkasUnggah = document.getElementById('hintBerkasUnggah');
                const inputBerkasUnggah = document.getElementById('inputBerkasUnggah');
                if (errorBerkasUnggah) errorBerkasUnggah.style.display = 'none';
                if (hintBerkasUnggah) hintBerkasUnggah.style.display = 'block';
                if (inputBerkasUnggah) inputBerkasUnggah.classList.remove('is-invalid');
            }
        });
    }

    const popupPeringatanUkuran = document.getElementById('popupPeringatanUkuran');
    const btnTutupPopupUkuran = document.getElementById('btnTutupPopupUkuran');
    const namaBerkasPopup = document.getElementById('namaBerkasPopup');
    const ukuranBerkasPopup = document.getElementById('ukuranBerkasPopup');

    if (btnTutupPopupUkuran && popupPeringatanUkuran) {
        btnTutupPopupUkuran.addEventListener('click', function () {
            popupPeringatanUkuran.classList.remove('d-flex');
            popupPeringatanUkuran.classList.add('d-none');
        });

        popupPeringatanUkuran.addEventListener('click', function (e) {
            if (e.target === popupPeringatanUkuran) {
                popupPeringatanUkuran.classList.remove('d-flex');
                popupPeringatanUkuran.classList.add('d-none');
            }
        });
    }

    function cekUkuranBerkas(inputEl, errorEl, hintEl) {
        if (!inputEl) return;
        inputEl.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxBytes = 20 * 1024 * 1024;
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);

                if (file.size > maxBytes) {
                    this.value = '';
                    this.classList.add('is-invalid');
                    if (errorEl) {
                        errorEl.textContent = `Ukuran berkas terlalu besar (${sizeMB} MB). Maksimal ukuran yang diizinkan adalah 20 MB.`;
                        errorEl.style.display = 'block';
                    }
                    if (hintEl) hintEl.style.display = 'none';

                    if (popupPeringatanUkuran && namaBerkasPopup && ukuranBerkasPopup) {
                        namaBerkasPopup.textContent = `"${file.name}"`;
                        ukuranBerkasPopup.textContent = `${sizeMB} MB`;
                        popupPeringatanUkuran.classList.remove('d-none');
                        popupPeringatanUkuran.classList.add('d-flex');
                    }
                } else {
                    this.classList.remove('is-invalid');
                    if (errorEl) {
                        errorEl.textContent = '';
                        errorEl.style.display = 'none';
                    }
                    if (hintEl) hintEl.style.display = 'block';
                }
            }
        });
    }

    cekUkuranBerkas(
        document.getElementById('inputBerkasUnggah'),
        document.getElementById('errorBerkasUnggah'),
        document.getElementById('hintBerkasUnggah')
    );

    cekUkuranBerkas(
        document.getElementById('editFileInput'),
        document.getElementById('errorBerkasUbah'),
        document.getElementById('hintBerkasUbah')
    );
</script>
@endpush