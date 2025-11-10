@extends('layouts.admin')

@section('title', 'Sửa thương hiệu')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Sửa thương hiệu</h4>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data"
        class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <!-- Tên thương hiệu -->
        <div class="mb-3">
            <label class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $brand->name) }}"
                class="form-control @error('name') is-invalid @enderror" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mô tả -->
        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                placeholder="Nhập mô tả thương hiệu (nếu có)...">{{ old('description', $brand->description) }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Logo -->
        <div class="mb-3">
            <label class="form-label d-block">Logo hiện tại</label>
            @if($brand->logo)
            <img src="{{ asset('storage/app/private/' . $brand->logo) }}" alt="Logo" width="100"
                class="rounded border mb-2">
            @else
            <p class="text-muted">Chưa có logo</p>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Chọn logo mới (nếu muốn thay)</label>
            <input type="file" name="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
            @error('logo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Hỗ trợ: jpg, png, gif, svg (tối đa 2MB)</div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa fa-save me-1"></i> Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection