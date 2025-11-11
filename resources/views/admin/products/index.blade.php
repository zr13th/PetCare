@extends('layouts.admin')

@section('title', 'Sản phẩm')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">Sản phẩm</h4>

        <!-- Bộ lọc -->
        <form action="{{ route('admin.products.index') }}" method="GET"
            class="d-flex align-items-center gap-2 flex-wrap" style="max-width: 650px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Tìm theo tên hoặc slug...">

            <select name="category_id" class="form-select form-select-sm" style="width:150px;">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>

            <select name="brand_id" class="form-select form-select-sm" style="width:150px;">
                <option value="">Tất cả thương hiệu</option>
                @foreach($brands as $b)
                <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>
                    {{ $b->name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-outline-primary">Lọc</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">Hủy</a>
        </form>

        <!-- Nhóm nút hành động -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-warning" title="Nhập Excel" data-bs-toggle="modal"
                data-bs-target="#importModal">
                <i class="fa fa-upload"></i>
            </button>

            <a href="{{ route('admin.products.export') }}" class="btn btn-sm btn-outline-success" title="Xuất Excel">
                <i class="fa fa-file-excel"></i>
            </a>

            <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                data-bs-target="#createModal">+ Thêm</button>
        </div>
    </div>

    {{ $products->links('pagination::bootstrap-5') }}

    <!-- Bảng sản phẩm -->
    <table class="table table-bordered table-hover align-middle table-sm">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Thương hiệu</th>
                <th>Giá</th>
                <th>Tồn</th>
                <th>T.thái</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
            <tr>
                <td>{{ $products->firstItem() + $loop->index }}</td>
                <td class="text-center">
                    @if($p->image)
                    <img src="{{ asset('storage/app/private/' . $p->image) }}" width="64"
                        class="object-fit-contain rounded">
                    @else
                    <span class="text-muted small">—</span>
                    @endif
                </td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category?->name ?? '—' }}</td>
                <td>{{ $p->brand?->name ?? '—' }}</td>
                <td>{{ number_format($p->price, 0, ',', '.') }} đ</td>
                <td>{{ $p->stock }}</td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-flex justify-content-center">
                        <input class="form-check-input toggle-status" type="checkbox" data-id="{{ $p->id }}"
                            {{ $p->status ? 'checked' : '' }}>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary editBtn" data-bs-toggle="modal"
                        data-bs-target="#editModal" data-id="{{ $p->id }}" data-name="{{ $p->name }}"
                        data-category="{{ $p->category_id }}" data-brand="{{ $p->brand_id }}"
                        data-price="{{ $p->price }}" data-stock="{{ $p->stock }}"
                        data-description="{{ $p->description }}" data-image="{{ $p->image }}">
                        <i class="fa fa-edit"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-bs-toggle="modal"
                        data-bs-target="#deleteModal" data-id="{{ $p->id }}" data-name="{{ $p->name }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted">Không có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links('pagination::bootstrap-5') }}
</div>

<!-- =============== MODALS =============== -->

<!-- Thêm -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-plus-circle me-1"></i> Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.products._form', ['mode' => 'create'])
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sửa -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="fa fa-edit me-1"></i> Sửa sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.products._form', ['mode' => 'edit'])
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn xóa sản phẩm <strong id="deleteName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa fa-upload me-1"></i> Nhập sản phẩm từ Excel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Chọn file Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv"
                        class="form-control @error('file') is-invalid @enderror" required>
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Hỗ trợ: .xlsx, .xls, .csv</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Nhập</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Toggle trạng thái
        document.querySelectorAll('.toggle-status').forEach(input => {
            input.addEventListener('change', async () => {
                const id = input.dataset.id;
                try {
                    const res = await fetch(`{{ url('admin/products') }}/${id}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error();
                } catch {
                    alert('Lỗi khi cập nhật trạng thái');
                    input.checked = !input.checked;
                }
            });
        });

        // Xóa
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            deleteModal.querySelector('#deleteName').textContent = btn.dataset.name;
            deleteModal.querySelector('#deleteForm').action =
                `{{ url('admin/products') }}/${btn.dataset.id}`;
        });

        // Sửa
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            const form = editModal.querySelector('#editForm');
            form.action = `{{ url('admin/products') }}/${btn.dataset.id}`;
            form.querySelector('[name="name"]').value = btn.dataset.name;
            form.querySelector('[name="category_id"]').value = btn.dataset.category;
            form.querySelector('[name="brand_id"]').value = btn.dataset.brand;
            form.querySelector('[name="price"]').value = btn.dataset.price;
            form.querySelector('[name="stock"]').value = btn.dataset.stock;
            form.querySelector('[name="description"]').value = btn.dataset.description || '';

            const preview = editModal.querySelector('#editPreviewImage');
            const text = editModal.querySelector('#editNoImageText');
            if (btn.dataset.image) {
                preview.src = `{{ asset('storage/app/private') }}/${btn.dataset.image}`;
                preview.classList.remove('d-none');
                text.classList.add('d-none');
            } else {
                preview.classList.add('d-none');
                text.classList.remove('d-none');
            }
        });

        // Reset preview khi đóng modal Thêm
        const createModal = document.getElementById('createModal');
        createModal.addEventListener('hidden.bs.modal', () => {
            const input = document.getElementById('createImageInput');
            const preview = document.getElementById('createPreviewImage');
            const text = document.getElementById('createNoImageText');
            if (input) input.value = '';
            if (preview) preview.classList.add('d-none');
            if (text) text.classList.remove('d-none');
        });
    });
</script>

@error('file')
<script>
    new bootstrap.Modal(document.getElementById('importModal')).show();
</script>
@enderror
@endpush