@extends('layouts.admin')

@section('title', 'Danh mục sản phẩm')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">Danh mục sản phẩm</h4>

        <!-- Thanh tìm kiếm -->
        <form action="{{ route('admin.categories.index') }}" method="GET"
            class="d-flex align-items-center gap-2 flex-wrap" style="max-width: 500px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Tìm theo tên hoặc slug...">

            <select name="parent_id" class="form-select form-select-sm" style="width:250px;">
                <option value="">Tất cả danh mục cha</option>
                @foreach($parents as $p)
                <option value="{{ $p->id }}" {{ request('parent_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-outline-primary">Tìm</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">Hủy</a>
        </form>

        <!-- Nhóm nút hành động -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Import -->
            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                data-bs-target="#importModal" title="Nhập Excel">
                <i class="fa fa-upload"></i>
            </button>

            <!-- Export -->
            <a href="{{ route('admin.categories.export') }}" class="btn btn-sm btn-outline-success" title="Xuất Excel">
                <i class="fa fa-file-excel"></i>
            </a>

            <!-- Thêm mới -->
            <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                data-bs-target="#createModal">
                + Thêm
            </button>
        </div>
    </div>

    {{-- Không cần alert vì Toast hiển thị ở layout --}}

    {{ $categories->links('pagination::bootstrap-5') }}

    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%">#</th>
                <th width="25%">Tên danh mục</th>
                <th width="25%">Slug</th>
                <th width="25%">Danh mục cha</th>
                <th width="20%" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td>{{ $categories->firstItem() + $loop->index }}</td>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->slug }}</td>
                <td>{{ $cat->parent?->name ?? '—' }}</td>
                <td class="text-center">
                    <!-- Sửa -->
                    <button type="button" class="btn btn-sm btn-outline-secondary editBtn" data-id="{{ $cat->id }}"
                        data-name="{{ $cat->name }}" data-parent="{{ $cat->parent_id ?? '' }}" data-bs-toggle="modal"
                        data-bs-target="#editModal">
                        <i class="fa fa-edit"></i>
                    </button>

                    <!-- Xóa -->
                    <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-id="{{ $cat->id }}"
                        data-name="{{ $cat->name }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links('pagination::bootstrap-5') }}
</div>

<!-- ================= MODALS ================= -->

<!-- Modal: Thêm -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.categories.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-plus-circle me-1"></i> Thêm danh mục mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục cha</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— Không có —</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

<!-- Modal: Sửa -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="fa fa-edit me-1"></i> Sửa danh mục</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName"
                            class="form-control @error('name') is-invalid @enderror" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục cha</label>
                        <select name="parent_id" id="editParent"
                            class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— Không có —</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
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

<!-- Modal: Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn xóa danh mục <strong id="deleteName"></strong>?</p>
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

<!-- Modal: Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.categories.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa fa-upload me-1"></i> Nhập danh mục từ Excel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">Chọn file Excel</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv"
                            class="form-control @error('file') is-invalid @enderror" required>
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Hỗ trợ: .xlsx, .xls, .csv</div>
                    </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Gán URL xóa
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', e => {
            const button = e.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            deleteModal.querySelector('#deleteName').textContent = name;
            deleteModal.querySelector('#deleteForm').action = `{{ url('admin/categories') }}/${id}`;
        });

        // Gán dữ liệu edit
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const parent = btn.getAttribute('data-parent');
            editModal.querySelector('#editName').value = name;
            editModal.querySelector('#editParent').value = parent || '';
            editModal.querySelector('#editForm').action = `{{ url('admin/categories') }}/${id}`;
        });
    });
</script>

{{-- Hiển thị lại modal khi có lỗi --}}
@error('file')
<script>
    new bootstrap.Modal(document.getElementById('importModal')).show();
</script>
@enderror

@error('name')
<script>
    new bootstrap.Modal(document.getElementById('createModal')).show();
</script>
@enderror
@endpush