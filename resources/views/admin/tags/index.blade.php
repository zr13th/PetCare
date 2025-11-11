@extends('layouts.admin')

@section('title', 'Thẻ sản phẩm')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">Thẻ sản phẩm</h4>

        <!-- Tìm kiếm -->
        <form action="{{ route('admin.tags.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap"
            style="max-width: 400px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Tìm theo tên hoặc slug...">
            <button type="submit" class="btn btn-sm btn-outline-primary">Tìm</button>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-sm btn-outline-secondary">Hủy</a>
        </form>

        <!-- Nút hành động -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Import -->
            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                data-bs-target="#importModal" title="Nhập Excel">
                <i class="fa fa-upload"></i>
            </button>

            <!-- Export -->
            <a href="{{ route('admin.tags.export') }}" class="btn btn-sm btn-outline-success" title="Xuất Excel">
                <i class="fa fa-file-excel"></i>
            </a>

            <!-- Thêm -->
            <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                data-bs-target="#createModal">+ Thêm</button>
        </div>
    </div>

    {{ $tags->links('pagination::bootstrap-5') }}

    <!-- Bảng dữ liệu -->
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%">#</th>
                <th width="25%">Tên thẻ</th>
                <th width="25%">Slug</th>
                <th>Mô tả</th>
                <th width="10%" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tags as $tag)
            <tr>
                <td>{{ $tags->firstItem() + $loop->index }}</td>
                <td>{{ $tag->name }}</td>
                <td>{{ $tag->slug }}</td>
                <td>{{ Str::limit($tag->description, 80) }}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary editBtn" data-id="{{ $tag->id }}"
                        data-name="{{ $tag->name }}" data-description="{{ $tag->description }}" data-bs-toggle="modal"
                        data-bs-target="#editModal">
                        <i class="fa fa-edit"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-id="{{ $tag->id }}"
                        data-name="{{ $tag->name }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
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

    {{ $tags->links('pagination::bootstrap-5') }}
</div>

<!-- ================= MODALS ================= -->

<!-- Modal: Thêm -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.tags.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-plus-circle me-1"></i> Thêm thẻ mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thẻ <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" novalidate>
                @csrf @method('PUT')
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="fa fa-edit me-1"></i> Sửa thẻ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thẻ</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn xóa thẻ <strong id="deleteName"></strong>?</p>
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

<!-- Modal: Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.tags.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa fa-upload me-1"></i> Nhập thẻ từ Excel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn file Excel</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv"
                            class="form-control @error('file') is-invalid @enderror" required>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
    document.addEventListener('DOMContentLoaded', () => {
        // Sửa
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            editModal.querySelector('#editForm').action = `/admin/tags/${btn.dataset.id}`;
            editModal.querySelector('#editName').value = btn.dataset.name;
            editModal.querySelector('#editDescription').value = btn.dataset.description || '';
        });

        // Xóa
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            deleteModal.querySelector('#deleteName').textContent = btn.dataset.name;
            deleteModal.querySelector('#deleteForm').action = `/admin/tags/${btn.dataset.id}`;
        });
    });
</script>

{{-- Tự bật modal khi lỗi --}}
@error('name')
<script>
    new bootstrap.Modal(document.getElementById('createModal')).show();
</script>
@enderror

@error('file')
<script>
    new bootstrap.Modal(document.getElementById('importModal')).show();
</script>
@enderror
@endpush