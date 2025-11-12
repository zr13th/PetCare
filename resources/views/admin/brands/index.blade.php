@extends('layouts.admin')

@section('title', 'Thương hiệu')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">Thương hiệu</h4>

        <form action="{{ route('admin.brands.index') }}" method="GET" class="d-flex align-items-center gap-2"
            style="max-width: 400px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Tìm theo tên hoặc slug...">
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fa-duotone fa-solid fa-filter-list"></i>
            </button>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-duotone fa-solid fa-rotate-reverse"></i>
            </a>
        </form>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                data-bs-target="#importModal" title="Nhập Excel">
                <i class="fa-duotone fa-solid fa-upload"></i>
            </button>

            <a href="{{ route('admin.brands.export') }}" class="btn btn-sm btn-outline-success" title="Xuất Excel">
                <i class="fa-duotone fa-solid fa-file-excel"></i>
            </a>

            <button type="button" class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                data-bs-target="#createModal">
                <i class="fa-duotone fa-light fa-plus-large"></i>

            </button>
        </div>
    </div>

    {{ $brands->links('pagination::bootstrap-5') }}

    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Logo</th>
                <th>Tên thương hiệu</th>
                <th>Slug</th>
                <th>Mô tả</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
            <tr>
                <td>{{ $brands->firstItem() + $loop->index }}</td>
                <td class="text-center">
                    @if($brand->logo)
                    <img src="{{ asset('storage/app/private/' . $brand->logo) }}" width="80"
                        class="object-fit-contain rounded">
                    @else
                    <span class="text-muted small">—</span>
                    @endif
                </td>
                <td>{{ $brand->name }}</td>
                <td>{{ $brand->slug }}</td>
                <td>{{ Str::limit($brand->description, 60) }}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary editBtn" data-id="{{ $brand->id }}"
                        data-name="{{ $brand->name }}" data-description="{{ $brand->description }}"
                        data-logo="{{ $brand->logo }}" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="fa-duotone fa-edit"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-id="{{ $brand->id }}"
                        data-name="{{ $brand->name }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fa-duotone fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $brands->links('pagination::bootstrap-5') }}
</div>

<!-- ========== MODALS ========== -->

<!-- Thêm -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-plus-circle me-1"></i> Thêm thương hiệu mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" id="createLogoInput" accept="image/*" class="form-control mb-2">
                        <div class="border rounded p-2 text-center" style="min-height:180px;">
                            <img id="createPreviewLogo" src="#" alt="Xem trước logo" class="img-fluid d-none"
                                style="max-height:150px; object-fit:contain;">
                            <p id="createNoLogoText" class="text-muted small mb-0">Chưa chọn logo</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sửa -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="fa fa-edit me-1"></i> Sửa thương hiệu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thương hiệu</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Logo (thay thế nếu muốn)</label>
                        <input type="file" name="logo" id="editLogoInput" accept="image/*" class="form-control mb-2">
                        <div class="border rounded p-2 text-center" style="min-height:180px;">
                            <img id="editPreviewLogo" src="#" alt="Xem trước logo" class="img-fluid d-none"
                                style="max-height:150px; object-fit:contain;">
                            <p id="editNoLogoText" class="text-muted small mb-0">Chưa chọn logo</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-secondary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn xóa thương hiệu <strong id="deleteName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Preview logo (create)
        const createInput = document.getElementById('createLogoInput');
        const createPreview = document.getElementById('createPreviewLogo');
        const createText = document.getElementById('createNoLogoText');

        createInput?.addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => {
                    createPreview.src = ev.target.result;
                    createPreview.classList.remove('d-none');
                    createText.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                createPreview.classList.add('d-none');
                createText.classList.remove('d-none');
            }
        });

        // Preview logo (edit)
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const desc = btn.dataset.description;
            const logo = btn.dataset.logo;

            editModal.querySelector('#editForm').action = `{{ url('admin/brands') }}/${id}`;
            editModal.querySelector('#editName').value = name;
            editModal.querySelector('#editDescription').value = desc || '';

            const preview = editModal.querySelector('#editPreviewLogo');
            const text = editModal.querySelector('#editNoLogoText');

            if (logo) {
                preview.src = `{{ asset('storage/app/private') }}/${logo}`;
                preview.classList.remove('d-none');
                text.classList.add('d-none');
            } else {
                preview.classList.add('d-none');
                text.classList.remove('d-none');
            }
        });

        // Preview logo change (edit)
        const editInput = document.getElementById('editLogoInput');
        editInput?.addEventListener('change', e => {
            const file = e.target.files[0];
            const preview = document.getElementById('editPreviewLogo');
            const text = document.getElementById('editNoLogoText');
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => {
                    preview.src = ev.target.result;
                    preview.classList.remove('d-none');
                    text.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('d-none');
                text.classList.remove('d-none');
            }
        });

        // Delete
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            deleteModal.querySelector('#deleteName').textContent = btn.dataset.name;
            deleteModal.querySelector('#deleteForm').action = `{{ url('admin/brands') }}/${btn.dataset.id}`;
        });
    });
</script>
@endpush