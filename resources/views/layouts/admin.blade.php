<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'ShopCare · Admin Dashboard')</title>


    {{-- Bootstrap + Font Awesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/vendor/font-awesome/css/all.min.css') }}" />

    {{-- Custom CSS pastel --}}
    <link href="{{ asset('public/asset/css/admin-style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="layout">
        {{-- SIDEBAR --}}
        <aside class="sidebar p-3 d-flex flex-column">
            <div class="d-flex align-items-center mb-4">
                <div class="stat-icon me-2"><i class="fa-solid fa-paw-simple"></i></div>
                <div class="brand">PetCare <span class="badge text-bg-light ms-1">Admin</span></div>
            </div>

            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-chart-line me-2"></i> Tổng quan
                </a>
                <a class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    <i class="fa-solid fa-box-open me-2"></i> Sản phẩm
                </a>
                <a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <i class="fa-regular fa-layer-group"></i></i> Danh mục
                </a>
                <a class="nav-link {{ request()->is('admin/brands*') ? 'active' : '' }}"
                    href="{{ route('admin.brands.index') }}">
                    <i class="fa-solid fa-tag me-2"></i> Thương hiệu
                </a>
                <a class="nav-link {{ request()->is('admin/tags*') ? 'active' : '' }}"
                    href="{{ route('admin.tags.index') }}">
                    <i class="fa-solid fa-tags me-2"></i> Tag
                </a>
            </nav>

            <div class="mt-auto small text-secondary text-center">
                © 2025 ShopCare
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="main">
            {{-- Topbar --}}
            <div class="topbar py-2 border-bottom">
                <div class="container-fluid d-flex justify-content-end align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown">
                            <img src="https://i.pravatar.cc/40?img=12" class="rounded-circle" width="32" height="32"
                                alt="admin" />
                            <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Hồ sơ</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nội dung chính --}}
            <div class="container-fluid py-4">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>