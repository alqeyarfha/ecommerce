@extends('layouts.admin')

@section('title', 'Daftar Produk')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800">Daftar Produk</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-center" width="260">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr data-product-id="{{ $product->id }}">
                            <td>
                                <img src="{{ $product->primaryImage?->image_url ?? asset('img/no-image.png') }}"
                                     class="rounded" width="60" height="60" style="object-fit: cover;">
                            </td>
                            <td class="fw-medium">{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>Rp {{ number_format($product->price) }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.products.show', $product) }}"
                                   class="btn btn-sm btn-info me-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="btn btn-sm btn-warning me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $product->id }}"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Data produk kosong
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    @foreach($products as $product)
        <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i> Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin <strong>menghapus permanen</strong> produk ini?</p>
                        <div class="alert alert-light border">
                            <strong>{{ $product->name }}</strong><br>
                            Kategori: {{ $product->category->name }}<br>
                            Harga: Rp {{ number_format($product->price) }}
                        </div>
                        <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">
                            <i class="bi bi-trash"></i> Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

{{-- Script untuk hapus produk via AJAX --}}
@push('scripts')
<script>
    function deleteProduct(id) {
        // Konfirmasi tambahan (fallback kalau modal sudah cukup)
        if (!confirm('Yakin ingin menghapus produk ini secara permanen?')) {
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!token) {
            alert('CSRF token tidak ditemukan. Refresh halaman dan coba lagi.');
            return;
        }

        fetch(`/admin/products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                // Hapus baris dari tabel
                const row = document.querySelector(`tr[data-product-id="${id}"]`);
                if (row) row.remove();

                // Tutup modal
                const modalEl = document.getElementById(`deleteModal${id}`);
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }

                alert('Produk berhasil dihapus!');
            } else {
                // Coba ambil pesan error dari server
                response.json().then(data => {
                    alert(data.message || 'Gagal menghapus produk.');
                }).catch(() => {
                    alert('Gagal menghapus produk (error server).');
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
        });
    }
</script>
@endpush
