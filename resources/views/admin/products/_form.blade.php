@php
$isEdit = $mode === 'edit';
@endphp

<div class="row">
    <!-- Cột trái -->
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select">
                    <option value="">— Không chọn —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Thương hiệu</label>
                <select name="brand_id" class="form-select">
                    <option value="">— Không chọn —</option>
                    @foreach($brands as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Giá (VNĐ)</label>
                <input type="number" name="price" min="0" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tồn kho</label>
                <input type="number" name="stock" min="0" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" rows="3" class="form-control"></textarea>
        </div>
    </div>

    <!-- Cột phải -->
    <div class="col-md-4 d-flex flex-column">
        <label class="form-label">Ảnh sản phẩm</label>
        <input type="file" name="image" accept="image/*" id="{{ $mode }}ImageInput" class="form-control mb-2">

        <div class="border rounded p-2 text-center flex-grow-1 d-flex align-items-center justify-content-center">
            <img id="{{ $mode }}PreviewImage" src="#" alt="Xem trước ảnh"
                class="img-fluid {{ $isEdit && isset($product->image) ? '' : 'd-none' }}"
                style="max-height: 250px; object-fit: contain;">
            <p class="text-muted small mb-0 {{ $isEdit && isset($product->image) ? 'd-none' : '' }}"
                id="{{ $mode }}NoImageText">Chưa chọn ảnh</p>
        </div>
    </div>
</div>

<!-- Row mới chứa trạng thái -->
<div class="row mt-2">
    <div class="col form-check form-switch mb-2 ms-2">
        <input class="form-check-input" type="checkbox" role="switch" name="status" id="{{ $mode }}StatusSwitch"
            checked>
        <label class="form-check-label" for="{{ $mode }}StatusSwitch">Hiển thị sản phẩm</label>
    </div>
</div>

<div class="text-end mt-3">
    <button type="submit" class="btn btn-primary px-4">
        <i class="fa fa-save me-1"></i>
        {{ $mode === 'create' ? 'Lưu' : 'Cập nhật' }}
    </button>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('{{ $mode }}ImageInput');
        const preview = document.getElementById('{{ $mode }}PreviewImage');
        const text = document.getElementById('{{ $mode }}NoImageText');

        if (input) {
            input.addEventListener('change', e => {
                const file = e.target.files[0];
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
        }
    });
</script>
@endpush