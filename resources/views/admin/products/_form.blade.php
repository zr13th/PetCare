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

        <!-- Tag sản phẩm -->
        <div class="mb-3">
            <label class="form-label">Thẻ sản phẩm (Tags)</label>
            <select id="{{ $mode }}TagsSelect" name="tags[]" multiple placeholder="Chọn hoặc nhập thẻ...">
                @foreach($tags as $tag)
                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
            <div class="form-text">Bạn có thể gõ để thêm tag mới.</div>
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

<!-- Trạng thái -->
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Màu pastel cho chip tag */
    .ts-control .item {
        background-color: #ffd4e5 !important;
        color: #5c2a42 !important;
        border-radius: 12px !important;
        padding: 4px 10px !important;
        margin: 2px !important;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }

    .ts-control .item .remove {
        color: #5c2a42 !important;
        margin-left: 5px;
        font-weight: bold;
    }

    .ts-control .item:hover {
        background-color: #ffc2d4 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // === Khởi tạo Tom Select cho cả create và edit ===
        const selectors = ['#createTagsSelect', '#editTagsSelect'];
        const selects = {};

        selectors.forEach(id => {
            const el = document.querySelector(id);
            if (el) {
                selects[id] = new TomSelect(el, {
                    create: true,
                    plugins: ['remove_button'],
                    persist: false,
                    render: {
                        item: (data, escape) =>
                            `<div class="badge rounded-pill me-1" style="background-color:#ffd4e5;color:#5c2a42;">${escape(data.text)}</div>`,
                        option: (data, escape) => `<div>${escape(data.text)}</div>`
                    }
                });
            }
        });

        // === Preview ảnh ===
        const input = document.getElementById('{{ $mode }}ImageInput');
        const preview = document.getElementById('{{ $mode }}PreviewImage');
        const text = document.getElementById('{{ $mode }}NoImageText');
        input?.addEventListener('change', e => {
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

        // === Khi mở modal Edit: nạp tag hiện có ===
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', e => {
                const btn = e.relatedTarget;
                const tags = JSON.parse(btn.dataset.tags || '[]');
                const ts = selects['#editTagsSelect'];
                if (ts) {
                    ts.clear();
                    ts.setValue(tags);
                }
            });
        }
    });
</script>
@endpush