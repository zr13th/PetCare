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

    @stack('styles')
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
                    <i class="fa-regular fa-box-isometric"></i> Sản phẩm
                </a>
                <a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <i class="fa-regular fa-layer-group"></i> Danh mục
                </a>
                <a class="nav-link {{ request()->is('admin/brands*') ? 'active' : '' }}"
                    href="{{ route('admin.brands.index') }}">
                    <i class="fa-regular fa-tags"></i> Thương hiệu
                </a>
                <a class="nav-link {{ request()->is('admin/tags*') ? 'active' : '' }}"
                    href="{{ route('admin.tags.index') }}">
                    <i class="fa-regular fa-send-backward"></i> Tag
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

        {{-- =================== TOAST MESSAGE =================== --}}
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">

            {{-- Toast thành công --}}
            @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                aria-atomic="true" id="toast-success">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
            @endif

            {{-- Toast lỗi --}}
            @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true" id="toast-error">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa fa-circle-exclamation me-2"></i> {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
            @endif
        </div>
        {{-- =================== /TOAST MESSAGE =================== --}}
    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script hiển thị Toast --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.forEach(toastEl => {
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 2000,
                    autohide: true
                });
                toast.show();
            });
        });
    </script>

    @stack('scripts')

</body>

</html>