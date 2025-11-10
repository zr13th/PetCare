@extends('layouts.admin')

@section('title', 'Thương hiệu')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">Thương hiệu</h4>

        <!-- Thanh tìm kiếm -->
        <form action="{{ route('admin.brands.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap"
            style="max-width: 400px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Tìm theo tên hoặc slug...">

            <button type="submit" class="btn btn-sm btn-outline-primary">Tìm</button>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-secondary">Hủy</a>
        </form>

        <!-- Nhóm nút hành động -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Import Excel -->
            <a type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" title="Nhập Excel"
                data-bs-target="#importModal">
                <i class="fa fa-upload"></i>
            </a>

            <!-- Export Excel -->
            <a href="{{ route('admin.brands.export') }}" class="btn btn-sm btn-outline-success" title="Xuất Excel">
                <i class="fa fa-file-excel"></i>
            </a>

            <!-- Thêm mới -->
            <a href="{{ route('admin.brands.create') }}" class="btn btn-sm btn-primary px-3">
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

    {{ $brands->links('pagination::bootstrap-5') }}

    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%">#</th>
                <th width="15%">Logo</th>
                <th width="20%">Tên thương hiệu</th>
                <th width="20%">Slug</th>
                <th width="30%">Mô tả</th>
                <th width="10%" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
            <tr>
                <td>{{ $brands->firstItem() + $loop->index }}</td>
                <td class="text-center">
                    @if($brand->logo)
                    <img src="{{ asset('storage/app/private/' . $brand->logo) }}" alt="Logo" width="100"
                        class="object-fit-contain rounded">
                    @else
                    <span class="text-muted small">—</span>
                    @endif
                </td>
                <td>{{ $brand->name }}</td>
                <td>{{ $brand->slug }}</td>
                <td>{{ Str::limit($brand->description, 60) }}</td>
                <td class="text-center">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteModal" data-id="{{ $brand->id }}" data-name="{{ $brand->name }}">
                        <i class="fa fa-trash"></i>
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

<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn xóa thương hiệu <strong id="deleteName"></strong>?</p>
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
            <form action="{{ route('admin.brands.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel"><i class="fa fa-upload"></i> Nhập thương hiệu từ Excel
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

            deleteModal.querySelector('#deleteName').textContent = name;
            const form = deleteModal.querySelector('#deleteForm');
            form.action = `/admin/brands/${id}`;
        });
    });
</script>

@error('file')
<script>
    const importModal = new bootstrap.Modal(document.getElementById('importModal'));
    importModal.show();
</script>
@enderror
@endpush