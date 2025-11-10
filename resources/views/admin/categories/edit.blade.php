@extends('layouts.admin')

@section('title', 'Sửa danh mục')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Sửa danh mục</h4>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
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

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Danh mục cha</label>
            <select name="parent_id" class="form-select">
                <option value="">— Không có —</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
        </div>
    </form>
</div>
@endsection