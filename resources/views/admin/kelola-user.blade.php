<x-admin.layout title="Kelola User">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin/kelola-user.css') }}">
    @endpush

    <div class="mb-3">
        <h1 class="h4 fw-bold text-primary mb-0">Kelola User</h1>
        <p class="text-secondary small mb-0">Atur akun, role, dan layanan. Akun non-aktif tidak dapat digunakan.</p>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show alert-otomatis py-2 small" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('peringatan'))
        <div class="alert alert-warning alert-dismissible fade show py-2 small" role="alert">
            {{ session('peringatan') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('gagal'))
        <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
            {{ session('gagal') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row gap-3 mb-3">
        <form action="{{ route('users.index') }}" method="GET" class="ku-search">
            <button type="submit" aria-label="Cari">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau email....">
        </form>

        <button type="button" class="btn ku-add" data-bs-toggle="modal" data-bs-target="#modalUser" data-mode="create">+ Tambah User</button>
    </div>

    <div class="ku-card">
        <div class="table-responsive">
            <table class="table ku-table align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Layanan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        @php $self = $u->id === auth()->id(); @endphp
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="ku-role">{{ $u->role }}</span></td>
                            <td>{{ $u->layanan?->nama ?? '-' }}</td>
                            <td>
                                <span class="ku-status {{ $u->status }}">{{ $u->status === 'aktif' ? 'Aktif' : 'Non - Aktif' }}</span>
                            </td>
                            <td class="text-nowrap">
                                <button type="button" class="ku-icon" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#modalUser"
                                    data-mode="edit"
                                    data-id="{{ $u->id }}"
                                    data-name="{{ $u->name }}"
                                    data-email="{{ $u->email }}"
                                    data-role="{{ $u->role }}"
                                    data-layanan="{{ $u->layanan_id }}"
                                    data-status="{{ $u->status }}"
                                    data-self="{{ $self ? 1 : 0 }}">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </button>

                                <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline ms-1"
                                    data-konfirmasi="Hapus user {{ $u->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ku-icon danger" title="{{ $self ? 'Tidak bisa menghapus akun sendiri' : 'Hapus' }}" @disabled($self)>
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Tidak ada user yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($users->hasPages())
        <div class="mt-3 d-flex justify-content-end">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4">
                <form id="formUser" action="{{ route('users.store') }}" method="POST"
                    data-store-url="{{ route('users.store') }}"
                    data-update-url="{{ route('users.update', '__ID__') }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" id="inputMethod" disabled>
                    <input type="hidden" name="_mode" value="{{ old('_mode', 'create') }}" id="inputMode">
                    <input type="hidden" name="_user_id" value="{{ old('_user_id') }}" id="inputUserId">

                    <div class="modal-body p-4">
                        <h5 class="fw-bold mb-3" id="modalUserTitle">Tambah User</h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="u-name" class="ku-label">Nama Lengkap</label>
                                <input type="text" id="u-name" name="name" class="form-control ku-field @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="u-email" class="ku-label">Email</label>
                                <input type="email" id="u-email" name="email" class="form-control ku-field @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="u-role" class="ku-label">Role</label>
                                <select id="u-role" name="role" class="form-select ku-field @error('role') is-invalid @enderror" required>
                                    <option value="user" @selected(old('role', 'user') === 'user')>User</option>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="u-layanan" class="ku-label">Layanan</label>
                                <select id="u-layanan" name="layanan_id" class="form-select ku-field @error('layanan_id') is-invalid @enderror">
                                    <option value="">- Tanpa layanan -</option>
                                    @foreach ($layanan as $l)
                                        <option value="{{ $l->id }}" @selected((string) old('layanan_id') === (string) $l->id)>{{ $l->nama }}</option>
                                    @endforeach
                                </select>
                                @error('layanan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6 d-none" id="wrapStatus">
                                <label for="u-status" class="ku-label">Status</label>
                                <select id="u-status" name="status" class="form-select ku-field @error('status') is-invalid @enderror">
                                    <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                                    <option value="nonaktif" @selected(old('status') === 'nonaktif')>Non - Aktif</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <p class="small text-success mt-3 mb-0" id="helperCreate">Password sementara akan otomatis dibuat sistem dan dikirim ke email ini.</p>
                        <p class="small text-secondary mt-3 mb-0 d-none" id="helperSelf">Ini akunmu sendiri, jadi role dan status tidak dapat diubah.</p>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn ku-btn-primary">Simpan User</button>
                            <button type="button" class="btn ku-btn-outline" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any() && old('_mode'))
        <script>
            window.kuReopen = {
                mode: @json(old('_mode')),
                id: @json(old('_user_id')),
                self: @json((int) old('_user_id') === auth()->id())
            };
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalUser = document.getElementById('modalUser');
            var form = document.getElementById('formUser');
            var inputMethod = document.getElementById('inputMethod');
            var inputMode = document.getElementById('inputMode');
            var inputUserId = document.getElementById('inputUserId');
            var title = document.getElementById('modalUserTitle');
            var wrapStatus = document.getElementById('wrapStatus');
            var helperCreate = document.getElementById('helperCreate');
            var helperSelf = document.getElementById('helperSelf');
            var fName = document.getElementById('u-name');
            var fEmail = document.getElementById('u-email');
            var fRole = document.getElementById('u-role');
            var fLayanan = document.getElementById('u-layanan');
            var fStatus = document.getElementById('u-status');

            function setMode(mode, id, self) {
                var edit = mode === 'edit';
                inputMode.value = mode;
                inputUserId.value = edit ? id : '';
                inputMethod.disabled = !edit;
                form.action = edit ? form.dataset.updateUrl.replace('__ID__', id) : form.dataset.storeUrl;
                title.textContent = edit ? 'Edit User' : 'Tambah User';
                wrapStatus.classList.toggle('d-none', !edit);
                helperCreate.classList.toggle('d-none', edit);
                helperSelf.classList.toggle('d-none', !(edit && self));
                fRole.disabled = edit && self;
                fStatus.disabled = edit && self;
            }

            modalUser.addEventListener('show.bs.modal', function (event) {
                var btn = event.relatedTarget;
                if (!btn) return;

                modalUser.querySelectorAll('.is-invalid').forEach(function (el) {
                    el.classList.remove('is-invalid');
                });

                if (btn.dataset.mode === 'edit') {
                    var self = btn.dataset.self === '1';
                    setMode('edit', btn.dataset.id, self);
                    fName.value = btn.dataset.name;
                    fEmail.value = btn.dataset.email;
                    fRole.value = btn.dataset.role;
                    fLayanan.value = btn.dataset.layanan || '';
                    fStatus.value = btn.dataset.status;
                } else {
                    setMode('create', null, false);
                    fName.value = '';
                    fEmail.value = '';
                    fRole.value = 'user';
                    fLayanan.value = '';
                    fStatus.value = 'aktif';
                }
            });

            if (window.kuReopen) {
                setMode(window.kuReopen.mode, window.kuReopen.id, window.kuReopen.self);
                bootstrap.Modal.getOrCreateInstance(modalUser).show();
            }
        });
    </script>
</x-admin.layout>