<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-white text-black selection:bg-black selection:text-white"
    x-data="{ cartOpen: false, categoryOpen: false }" @open-cart-sidebar.window="cartOpen = true">

    {{-- Overlay --}}
    <div x-show="cartOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        @click="cartOpen = false" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[60]"></div>

    {{-- Cart Sidebar --}}
    <div x-show="cartOpen" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl z-[70] flex flex-col">
        @livewire('cart-component')
    </div>

    <header class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

            <a href="/" class="flex items-center gap-2 group">
                <div
                    class="w-8 h-8 bg-black rounded-lg flex items-center justify-center transition-transform group-hover:scale-95">
                    <span class="text-white text-[10px] font-black">PC</span>
                </div>
                <span class="text-lg font-black tracking-tighter">PetCare</span>
            </a>

            <div class="flex items-center gap-2">

                {{-- Category dropdown --}}
                <div class="relative group" @mouseenter="categoryOpen = true" @mouseleave="categoryOpen = false">
                    <button class="p-3 text-gray-400 hover:text-black hover:bg-gray-50 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16m-7 6h7" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div x-show="categoryOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        class="absolute left-0 mt-1 w-64 bg-white border border-gray-100 shadow-2xl rounded-2xl p-4 overflow-hidden">
                        @foreach($categories as $parent)
                        <div class="mb-4 last:mb-0">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 px-2">
                                {{ $parent->name }}</h4>
                            <div class="space-y-1">
                                @foreach($parent->children as $child)
                                <a href="#"
                                    class="block px-2 py-2 text-sm font-semibold text-gray-600 hover:text-black hover:bg-gray-50 rounded-lg transition-colors">
                                    {{ $child->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Search --}}
                <button class="p-3 text-gray-400 hover:text-black hover:bg-gray-50 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                {{-- Cart --}}
                <button @click="cartOpen = true"
                    class="relative p-3 text-gray-400 hover:text-black hover:bg-gray-50 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    @livewire('cart-badge')
                </button>

                {{-- ★ USER DROPDOWN ★ --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-50 transition-all">
                        @auth
                        {{-- Avatar initials --}}
                        <div
                            class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center text-[11px] font-black tracking-tight select-none">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        @else
                        <div class="p-1.5 text-gray-400 hover:text-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>
                        @endauth
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-full mt-2 w-60 bg-white border border-gray-100 rounded-2xl shadow-2xl shadow-black/5 overflow-hidden z-50"
                        style="display: none;">

                        @auth
                        {{-- User info --}}
                        <div class="px-4 py-3.5 border-b border-gray-100 bg-gray-50/60">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center text-sm font-black flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-1.5">
                            <a href="{{ route('profile') }}" @click="open = false"
                                class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-black rounded-xl transition-all group">
                                <div
                                    class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-black group-hover:text-white flex items-center justify-center transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                                Trang cá nhân
                            </a>

                            <a href="{{ route('profile', ['tab' => 'orders']) }}" @click="open = false"
                                class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-black rounded-xl transition-all group">
                                <div
                                    class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-black group-hover:text-white flex items-center justify-center transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                            stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                                Đơn hàng của tôi
                                @php $pendingCount =
                                auth()->user()->invoices()->whereIn('status',['pending','confirmed','processing','shipped'])->count();
                                @endphp
                                @if($pendingCount > 0)
                                <span
                                    class="ml-auto text-[10px] font-black bg-black text-white px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                                @endif
                            </a>

                            <button @click="open = false; cartOpen = true"
                                class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-black rounded-xl transition-all group">
                                <div
                                    class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-black group-hover:text-white flex items-center justify-center transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                </div>
                                Giỏ hàng
                            </button>

                            <a href="{{ route('profile', ['tab' => 'address']) }}" @click="open = false"
                                class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-black rounded-xl transition-all group">
                                <div
                                    class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-black group-hover:text-white flex items-center justify-center transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            stroke-width="2" stroke-linecap="round" />
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                </div>
                                Địa chỉ của tôi
                            </a>

                            <div class="my-1 mx-2 border-t border-gray-100"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-semibold text-red-500 hover:bg-red-50 rounded-xl transition-all group">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-red-50 group-hover:bg-red-500 group-hover:text-white flex items-center justify-center transition-all text-red-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                                stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="p-2">
                            <a href="{{ route('login') }}"
                                class="block px-4 py-2.5 text-sm font-bold text-center bg-black text-white rounded-xl hover:bg-gray-800 transition-all">
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}"
                                class="block mt-1 px-4 py-2.5 text-sm font-semibold text-center text-gray-600 hover:bg-gray-50 rounded-xl transition-all">
                                Tạo tài khoản
                            </a>
                        </div>
                        @endauth
                    </div>
                </div>
                {{-- ★ END USER DROPDOWN ★ --}}

            </div>
        </div>
    </header>

    <main class="min-h-screen">{{ $slot }}</main>

    @livewireScripts
</body>

</html>