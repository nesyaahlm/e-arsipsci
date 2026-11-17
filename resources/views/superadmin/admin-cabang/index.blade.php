<x-superadmin.layout title="Kelola Admin Cabang">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-primary mb-1">Kelola Admin Cabang</h1>
            <p class="text-secondary mb-0 small">Manajemen akun administrator dan penetapan penugasan kantor cabang nasional.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
            + Tambah Admin Cabang
        </button>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('superadmin.admin-cabang.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-sm rounded-3" placeholder="Cari nama atau email administrator...">
                </div>
                <div class="col-12 col-md-3">
                    <select name="cabang_id" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Kantor Cabang</option>
                        @foreach ($daftarCabang as $c)
                            <option value="{{ $c->id }}" {{ $cabangId == $c->id ? 'selected' : '' }}>{{ $c->nama }} ({{ $c->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select name="status" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Filter</button>
                    @if ($cari || $cabangId || $status)
                        <a href="{{ route('superadmin.admin-cabang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tabel-jenis">
                <thead>
                    <tr>
                        <th class="py-2.5 ps-3 align-middle text-center" style="width: 50px;">NO</th>
                        <th class="py-2.5 px-3 align-middle">NAMA ADMINISTRATOR</th>
                        <th class="py-2.5 px-3 align-middle">EMAIL AKUN</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 180px;">KANTOR CABANG</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 120px;">STATUS</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 130px;">TERDAFTAR</th>
                        <th class="py-2.5 pe-3 align-middle text-center" style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarAdmin as $admin)
                        <tr>
                            <td class="ps-3 text-center text-secondary small">{{ $loop->iteration + ($daftarAdmin->currentPage() - 1) * $daftarAdmin->perPage() }}</td>
                            <td class="px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width: 32px; height: 32px; background-color: #277a72; font-size: 0.8rem;">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $admin->name }}</div>
                                        <div class="text-muted small" style="font-size: 0.73rem;">Role: Admin Cabang</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 text-dark small">{{ $admin->email }}</td>
                            <td class="px-3">
                                <span class="badge rounded-pill bg-light text-primary border border-primary-subtle px-2.5 py-1">
                                    {{ $admin->cabang->nama ?? 'Cabang Cilacap' }}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                @if ($admin->status === 'aktif')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1">Aktif</span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 text-center text-secondary small">
                                {{ $admin->created_at ? $admin->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditAdmin"
                                            data-id="{{ $admin->id }}"
                                            data-name="{{ $admin->name }}"
                                            data-email="{{ $admin->email }}"
                                            data-cabang-id="{{ $admin->cabang_id }}"
                                            data-status="{{ $admin->status }}"
                                            title="Edit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </button>
                                    <form action="{{ route('superadmin.admin-cabang.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus atau menonaktifkan admin {{ $admin->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px;"
                                                title="Hapus / Nonaktifkan">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">Tidak ada data administrator cabang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($daftarAdmin->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Menampilkan {{ $daftarAdmin->firstItem() }}–{{ $daftarAdmin->lastItem() }} dari {{ $daftarAdmin->total() }} admin cabang
                </div>
                <div>
                    {{ $daftarAdmin->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-labelledby="modalTambahAdminLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('superadmin.admin-cabang.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-3 px-3">
                        <h5 class="modal-title fw-bold text-primary" id="modalTambahAdminLabel">Tambah Admin Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap administrator" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Email Akun (@sucofindo.co.id) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="contoh: admin.semarang@sucofindo.co.id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Kantor Cabang Penugasan <span class="text-danger">*</span></label>
                            <select name="cabang_id" class="form-select" required>
                                <option value="" disabled selected>Pilih Kantor Cabang</option>
                                @foreach ($daftarCabang as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->kode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" minlength="8" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-3">
                        <button type="button" class="btn btn-light btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-labelledby="modalEditAdminLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form id="formEditAdmin" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0 pt-3 px-3">
                        <h5 class="modal-title fw-bold text-primary" id="modalEditAdminLabel">Ubah Admin Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Email Akun <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Kantor Cabang Penugasan <span class="text-danger">*</span></label>
                            <select id="edit_cabang_id" name="cabang_id" class="form-select" required>
                                <option value="" disabled>Pilih Kantor Cabang</option>
                                @foreach ($daftarCabang as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->kode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Status Akun <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label small fw-semibold text-secondary">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="8">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-3">
                        <button type="button" class="btn btn-light btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Perbarui Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEdit = document.getElementById('modalEditAdmin');
                if (modalEdit) {
                    modalEdit.addEventListener('show.bs.modal', function (event) {
                        var button = event.relatedTarget;
                        var id = button.getAttribute('data-id');
                        var name = button.getAttribute('data-name');
                        var email = button.getAttribute('data-email');
                        var cabangId = button.getAttribute('data-cabang-id');
                        var status = button.getAttribute('data-status');

                        var form = document.getElementById('formEditAdmin');
                        form.action = '/superadmin/admin-cabang/' + id;

                        document.getElementById('edit_name').value = name;
                        document.getElementById('edit_email').value = email;
                        document.getElementById('edit_cabang_id').value = cabangId || '';
                        document.getElementById('edit_status').value = status;
                    });
                }
            });
        </script>
    @endpush
</x-superadmin.layout>