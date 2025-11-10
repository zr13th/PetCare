@extends('layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Thêm danh mục mới</h4>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf

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

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">Lưu</button>
        </div>
    </form>
</div>
@endsection