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

            <select name="parent_id" class="form-select form-select-sm" style="width:150px;">
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
            <!-- Nút mở Modal Import -->
            <a type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" title="Nhập Excel"
                data-bs-target="#importModal">
                <i class="fa fa-upload"></i>
            </a>

            <!-- Export Excel -->
            <a href="{{ route('admin.categories.export') }}" class="btn btn-sm btn-outline-success" title="Xuất Excel">
                <i class="fa fa-file-excel"></i>
            </a>

            <!-- Thêm mới -->
            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary px-3">
                + Thêm
            </a>
        </div>

    </div>


    {{-- Thông báo --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

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
                    <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-edit"></i>
                    </a>

                    <!-- Nút mở modal -->
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteModal" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}">
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

<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
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

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.categories.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel"><i class="fa fa-upload"></i> Nhập danh mục từ Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">Chọn file Excel</label>
                        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv"
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
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            console.log(name)
            deleteModal.querySelector('#deleteName').textContent = name;

            const form = deleteModal.querySelector('#deleteForm');
            form.action = `/admin/categories/${id}`;
        });
    });
</script>

@error('file')
<script>
    const importModal = new bootstrap.Modal(document.getElementById('importModal'))
    importModal.show();
</script>
@enderror

@endpush