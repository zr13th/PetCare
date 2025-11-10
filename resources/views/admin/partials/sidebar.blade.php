{{-- Offcanvas sidebar for mobile & fixed on desktop --}}
<div class="offcanvas-lg offcanvas-start bg-white border-end" tabindex="-1" id="sidebar">
    <div class="offcanvas-header d-flex d-lg-none">
        <h5 class="offcanvas-title fw-bold">📋 Quản lý</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <nav class="nav flex-column">
            <a class="nav-link py-2 px-3 {{ request()->routeIs('admin.dashboard') ? 'active bg-peach text-dark fw-bold' : 'text-secondary' }}"
                href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-chart-line me-2"></i> Tổng quan
            </a>
            <a class="nav-link py-2 px-3 {{ request()->is('admin/categories*') ? 'active bg-peach text-dark fw-bold' : 'text-secondary' }}"
                href="{{ route('admin.categories.index') }}">
                <i class="fa-solid fa-layer-group me-2"></i> Danh mục
            </a>
            <a class="nav-link py-2 px-3 {{ request()->is('admin/brands*') ? 'active bg-peach text-dark fw-bold' : 'text-secondary' }}"
                href="{{ route('admin.brands.index') }}">
                <i class="fa-solid fa-tag me-2"></i> Thương hiệu
            </a>
            <a class="nav-link py-2 px-3 {{ request()->is('admin/products*') ? 'active bg-peach text-dark fw-bold' : 'text-secondary' }}"
                href="{{ route('admin.products.index') }}">
                <i class="fa-solid fa-box-open me-2"></i> Sản phẩm
            </a>
            <a class="nav-link py-2 px-3 {{ request()->is('admin/tags*') ? 'active bg-peach text-dark fw-bold' : 'text-secondary' }}"
                href="{{ route('admin.tags.index') }}">
                <i class="fa-solid fa-tags me-2"></i> Tag
            </a>
        </nav>
    </div>
</div>