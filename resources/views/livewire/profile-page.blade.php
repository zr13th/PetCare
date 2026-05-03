<div>
    {{-- Toast notification --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
        @notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-[999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-sm font-bold"
        :class="type === 'success' ? 'bg-black text-white' : 'bg-red-500 text-white'" style="display:none">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span x-text="message"></span>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-10">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-black tracking-tight">Tài khoản</h1>
            <p class="text-gray-400 mt-1 text-sm">
                Xin chào trở lại, <span class="font-bold text-black">{{ $user->name }}</span> 👋
            </p>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 bg-gray-100 p-1 rounded-2xl mb-8 w-fit overflow-x-auto">
            @foreach([
            ['key' => 'profile', 'label' => 'Hồ sơ', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7
            7 0
            00-7-7z'],
            ['key' => 'orders', 'label' => 'Đơn hàng', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2
            2 0
            00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['key' => 'address', 'label' => 'Địa chỉ', 'icon' => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827
            0l-4.244-4.243a8
            8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
            ['key' => 'security', 'label' => 'Bảo mật', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
            00-2
            2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
            ] as $t)
            <button wire:click="switchTab('{{ $t['key'] }}')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all
                {{ $activeTab === $t['key'] ? 'bg-black text-white shadow-sm' : 'text-gray-500 hover:text-black' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="{{ $t['icon'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                {{ $t['label'] }}
                @if($t['key'] === 'orders' && $stats['active'] > 0)
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded-full
                    {{ $activeTab === 'orders' ? 'bg-white text-black' : 'bg-black text-white' }}">
                    {{ $stats['active'] }}
                </span>
                @endif
            </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── LEFT: User card ─────────────────────────────────── --}}
            <aside class="lg:col-span-1 space-y-4">

                {{-- Avatar + info --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm">
                    <div class="relative inline-block mb-4">
                        <div class="w-20 h-20 rounded-full overflow-hidden mx-auto ring-2 ring-gray-100 shadow">
                            @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover"
                                alt="Avatar" />
                            @else
                            <div
                                class="w-full h-full bg-black text-white flex items-center justify-center text-xl font-black">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            @endif
                        </div>
                        @if($activeTab === 'profile')
                        <label
                            class="absolute -bottom-0.5 -right-0.5 w-6 h-6 bg-black text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-gray-700 transition-all shadow">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                                    stroke-width="2" stroke-linecap="round" />
                                <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <input type="file" wire:model="avatar" class="hidden" accept="image/*" />
                        </label>
                        @endif
                    </div>

                    {{-- Avatar upload preview --}}
                    @if($avatar)
                    <div
                        class="mb-3 p-2 bg-gray-50 rounded-xl text-xs text-gray-500 flex items-center justify-between gap-2">
                        <span class="truncate">{{ $avatar->getClientOriginalName() }}</span>
                        <button wire:click="$set('avatar', null)"
                            class="text-red-400 hover:text-red-600 flex-shrink-0">✕</button>
                    </div>
                    @endif

                    <h3 class="font-black text-base leading-tight">{{ $user->name }}</h3>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $user->email }}</p>
                    @if($user->phone)
                    <p class="text-gray-400 text-xs">{{ $user->phone }}</p>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400 text-left">
                        Thành viên từ {{ $user->created_at->format('m/Y') }}
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-2 mt-3">
                        @foreach([
                        ['n' => $stats['total'], 'l' => 'Đơn hàng'],
                        ['n' => $stats['active'], 'l' => 'Đang xử lý'],
                        ['n' => $stats['delivered'], 'l' => 'Đã nhận'],
                        ] as $s)
                        <div class="bg-gray-50 rounded-xl py-2.5">
                            <div class="text-lg font-black leading-none">{{ $s['n'] }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $s['l'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Quick links --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-2 shadow-sm">
                    @foreach([
                    ['tab' => 'profile', 'label' => 'Hồ sơ cá nhân', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7
                    7 0
                    00-7 7h14a7 7 0 00-7-7z'],
                    ['tab' => 'orders', 'label' => 'Đơn hàng', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0
                    002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['tab' => 'address', 'label' => 'Địa chỉ', 'icon' => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827
                    0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['tab' => 'security', 'label' => 'Đổi mật khẩu', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0
                    00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ] as $link)
                    <button wire:click="switchTab('{{ $link['tab'] }}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-all
                        {{ $activeTab === $link['tab'] ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="{{ $link['icon'] }}" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        {{ $link['label'] }}
                    </button>
                    @endforeach

                    <div class="mx-2 my-1 border-t border-gray-100"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-semibold text-red-500 hover:bg-red-50 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    stroke-width="2" stroke-linecap="round" />
                            </svg>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ── RIGHT: Tab content ───────────────────────────────── --}}
            <div class="lg:col-span-2">

                {{-- ═══ TAB PROFILE ════════════════════════════════════ --}}
                @if($activeTab === 'profile')
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-base font-black mb-5">Thông tin cá nhân</h2>

                    <form wire:submit.prevent="saveProfile" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Họ
                                    và
                                    tên *</label>
                                <input wire:model="name" type="text" placeholder="Nhập họ và tên" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium
                                    focus:outline-none focus:border-black focus:bg-white transition-all
                                    @error('name') border-red-400 bg-red-50 @enderror" />
                                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Email
                                    *</label>
                                <input wire:model="email" type="email" placeholder="email@example.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium
                                    focus:outline-none focus:border-black focus:bg-white transition-all
                                    @error('email') border-red-400 bg-red-50 @enderror" />
                                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Số
                                    điện
                                    thoại</label>
                                <input wire:model="phone" type="tel" placeholder="0912 345 678"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-black focus:bg-white transition-all" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Giới
                                    tính</label>
                                <select wire:model="gender"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-black focus:bg-white transition-all">
                                    <option value="">-- Chọn --</option>
                                    <option value="male">Nam</option>
                                    <option value="female">Nữ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Ngày
                                sinh</label>
                            <input wire:model="birthday" type="date"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-black focus:bg-white transition-all" />
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Bio</label>
                            <textarea wire:model="bio" rows="3" placeholder="Viết gì đó về bản thân..."
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-black focus:bg-white transition-all resize-none"></textarea>
                            <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($bio) }}/500</p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-black text-white text-sm font-bold rounded-xl hover:bg-gray-800 active:scale-95 transition-all"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70">
                                <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                                </svg>
                                <span wire:loading.remove>Lưu thay đổi</span>
                                <span wire:loading>Đang lưu...</span>
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- ═══ TAB ORDERS ══════════════════════════════════════ --}}
                @if($activeTab === 'orders')
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-black">Đơn hàng của tôi</h2>
                        <span class="text-xs text-gray-400 font-medium">{{ $orders->total() }} đơn</span>
                    </div>

                    {{-- Filter chips --}}
                    <div class="flex gap-2 flex-wrap mb-5">
                        @foreach([
                        '' => 'Tất cả',
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'processing' => 'Đang xử lý',
                        'shipped' => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'cancelled' => 'Đã hủy',
                        ] as $val => $lbl)
                        <button wire:click="filterOrders('{{ $val }}')"
                            class="px-3 py-1 text-xs font-bold rounded-full border transition-all
                            {{ $orderStatus === $val ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-500 hover:border-gray-400 hover:text-black' }}">
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>

                    @php
                    $statusMap = [
                    'pending' => ['Chờ xác nhận', 'bg-amber-50 text-amber-700 border-amber-200'],
                    'confirmed' => ['Đã xác nhận', 'bg-blue-50 text-blue-700 border-blue-200'],
                    'processing' => ['Đang xử lý', 'bg-blue-50 text-blue-700 border-blue-200'],
                    'shipped' => ['Đang giao', 'bg-violet-50 text-violet-700 border-violet-200'],
                    'delivered' => ['Đã giao', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'cancelled' => ['Đã hủy', 'bg-red-50 text-red-600 border-red-200'],
                    ];
                    @endphp

                    <div class="space-y-3">
                        @forelse($orders as $order)
                        @php $s = $statusMap[$order->status] ?? [$order->status, 'bg-gray-50 text-gray-600
                        border-gray-200']; @endphp
                        <div
                            class="border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 hover:shadow-sm transition-all">
                            {{-- Order header --}}
                            <div
                                class="flex items-center justify-between px-4 py-3 bg-gray-50/60 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-black">{{ $order->invoice_number }}</span>
                                    <span
                                        class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <span
                                    class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $s[1] }}">{{ $s[0] }}</span>
                            </div>

                            {{-- Items --}}
                            <div class="px-4 py-3 space-y-2.5">
                                @foreach($order->items->take(3) as $item)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center text-base flex-shrink-0">
                                        🐾</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ $item->product_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->variant_sku }} · SL:
                                            {{ $item->quantity }}</p>
                                    </div>
                                    <span
                                        class="text-sm font-bold flex-shrink-0">{{ number_format($item->subtotal) }}đ</span>
                                </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                <p class="text-xs text-gray-400">+{{ $order->items->count() - 3 }} sản phẩm khác</p>
                                @endif
                            </div>

                            {{-- Tracking --}}
                            @if($order->shipment?->tracking_number)
                            <div class="px-4 pb-3">
                                <div
                                    class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-xl text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12h12l1-12"
                                            stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    Tracking: <span
                                        class="font-bold text-black ml-1">{{ $order->shipment->tracking_number }}</span>
                                </div>
                            </div>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                                <div class="text-xs text-gray-400 flex items-center gap-2">
                                    <span>{{ $order->items->sum('quantity') }} sản phẩm</span>
                                    @if($order->payment)
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span
                                        class="{{ $order->payment->status === 'completed' ? 'text-emerald-600 font-semibold' : '' }}">
                                        {{ $order->payment->status === 'completed' ? '✓ Đã thanh toán' : '⏳ Chờ TT' }}
                                    </span>
                                    @endif
                                </div>
                                <span class="text-base font-black">{{ number_format($order->total_amount) }}đ</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-16">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">
                                📦</div>
                            <p class="font-bold text-gray-600">Không có đơn hàng nào</p>
                            <p class="text-sm text-gray-400 mt-1">
                                {{ $orderStatus ? 'Thử bộ lọc khác' : 'Bạn chưa đặt hàng lần nào' }}</p>
                            @if(!$orderStatus)
                            <a href="/"
                                class="inline-block mt-4 px-5 py-2 bg-black text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all">
                                Mua sắm ngay
                            </a>
                            @endif
                        </div>
                        @endforelse
                    </div>

                    @if($orders->hasPages())
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        {{ $orders->links() }}
                    </div>
                    @endif
                </div>
                @endif

                {{-- ═══ TAB ADDRESS ════════════════════════════════════ --}}
                @if($activeTab === 'address')
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-black">Địa chỉ giao hàng</h2>
                        <button wire:click="openCreate"
                            class="flex items-center gap-2 px-4 py-2 bg-black text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" />
                            </svg>
                            Thêm địa chỉ
                        </button>
                    </div>

                    {{-- Danh sách --}}
                    @if($addresses->isEmpty())
                    <div class="text-center py-16 border-2 border-dashed border-gray-100 rounded-2xl">
                        <div
                            class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl">
                            📍</div>
                        <p class="font-black text-gray-700">Chưa có địa chỉ nào</p>
                        <p class="text-gray-400 text-sm mt-1">Thêm địa chỉ để giao hàng nhanh hơn.</p>
                        <button wire:click="openCreate"
                            class="mt-4 px-6 py-2.5 bg-black text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all">
                            + Thêm địa chỉ đầu tiên
                        </button>
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($addresses as $address)
                        <div class="border-2 rounded-2xl p-5 transition-all {{ $address->is_default ? 'border-black shadow-sm' : 'border-gray-100 hover:border-gray-300' }}"
                            wire:key="addr-{{ $address->id }}">
                            <div class="flex flex-col h-full justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <p class="font-black text-base">{{ $address->receiver_name }}</p>
                                        @if($address->is_default)
                                        <span
                                            class="text-[9px] font-black uppercase tracking-widest bg-black text-white px-2 py-0.5 rounded-full">Mặc
                                            định</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-bold text-gray-600 mb-2">{{ $address->receiver_phone }}</p>
                                    <p class="text-sm text-gray-400 leading-relaxed">
                                        {{ $address->address_line }}, {{ $address->ward }}, {{ $address->province }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                                    @if(!$address->is_default)
                                    <button wire:click="setDefault({{ $address->id }})"
                                        class="text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-black transition-colors">
                                        Đặt mặc định
                                    </button>
                                    @endif
                                    <div class="flex-1"></div>
                                    <button wire:click="openEdit({{ $address->id }})"
                                        class="h-8 px-3.5 rounded-lg bg-gray-50 text-gray-500 text-xs font-bold hover:bg-black hover:text-white transition-all">
                                        Sửa
                                    </button>
                                    <button wire:click="deleteAddress({{ $address->id }})"
                                        wire:confirm="Xóa địa chỉ này?"
                                        class="h-8 px-3.5 rounded-lg bg-red-50 text-red-400 text-xs font-bold hover:bg-red-500 hover:text-white transition-all">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                {{-- ═══ TAB SECURITY ═══════════════════════════════════ --}}
                @if($activeTab === 'security')
                <div class="space-y-4">
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <h2 class="text-base font-black mb-5">Đổi mật khẩu</h2>
                        <form wire:submit.prevent="changePassword" class="space-y-4 max-w-sm">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Mật
                                    khẩu
                                    hiện tại</label>
                                <input wire:model="current_password" type="password" autocomplete="current-password"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-black focus:bg-white transition-all
                                    @error('current_password') border-red-400 bg-red-50 @enderror" />
                                @error('current_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Mật
                                    khẩu
                                    mới</label>
                                <input wire:model="new_password" type="password" autocomplete="new-password" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-black focus:bg-white transition-all
                                    @error('new_password') border-red-400 bg-red-50 @enderror" />
                                @error('new_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Xác
                                    nhận
                                    mật khẩu mới</label>
                                <input wire:model="new_password_confirmation" type="password"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-black focus:bg-white transition-all" />
                            </div>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-black text-white text-sm font-bold rounded-xl hover:bg-gray-800 active:scale-95 transition-all"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70">
                                <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                        stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                                </svg>
                                <span wire:loading.remove>Đổi mật khẩu</span>
                                <span wire:loading>Đang xử lý...</span>
                            </button>
                        </form>
                    </div>

                    {{-- Danger zone --}}
                    <div class="bg-white border border-red-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-black text-red-600 mb-1">Vùng nguy hiểm</h3>
                        <p class="text-xs text-gray-400 mb-4">Những hành động này không thể hoàn tác.</p>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-red-200 text-red-500 text-sm font-bold rounded-xl hover:bg-red-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                        stroke-width="2" stroke-linecap="round" />
                                </svg>
                                Đăng xuất khỏi tài khoản
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══ ADDRESS MODAL ══════════════════════════════════ --}}
    @if($showAddressModal)
    <div x-data="{
        provinces: [],
        wards: [],
        selectedProvinceCode: @js($provinceCode),
        selectedWardCode: @js($wardCode),
        provinceSearch: @js($provinceName),
        wardSearch: @js($wardName),
        showProvinceList: false,
        showWardList: false,
        loadingProvinces: false,
        loadingWards: false,
        apiUrl: 'https://provinces.open-api.vn/api/v2',

        get filteredProvinces() {
            if (!this.provinceSearch) return this.provinces;
            const q = this.provinceSearch.toLowerCase();
            return this.provinces.filter(p => p.name.toLowerCase().includes(q));
        },
        get filteredWards() {
            if (!this.wardSearch) return this.wards;
            const q = this.wardSearch.toLowerCase();
            return this.wards.filter(w => w.name.toLowerCase().includes(q));
        },
        async init() {
            await this.loadProvinces();
            if (this.selectedProvinceCode) await this.loadWards(this.selectedProvinceCode);
        },
        async loadProvinces() {
            this.loadingProvinces = true;
            try {
                const res = await fetch(this.apiUrl + '/p/');
                this.provinces = await res.json();
            } finally { this.loadingProvinces = false; }
        },
        selectProvince(province) {
            this.selectedProvinceCode = province.code;
            this.provinceSearch = province.name;
            this.showProvinceList = false;
            this.wards = []; this.selectedWardCode = ''; this.wardSearch = '';
            $wire.set('provinceCode', province.code);
            $wire.set('provinceName', province.name);
            $wire.set('wardCode', ''); $wire.set('wardName', '');
            this.loadWards(province.code);
        },
        clearProvince() {
            this.selectedProvinceCode = ''; this.provinceSearch = '';
            this.wards = []; this.selectedWardCode = ''; this.wardSearch = '';
            $wire.set('provinceCode', ''); $wire.set('provinceName', '');
            $wire.set('wardCode', ''); $wire.set('wardName', '');
        },
        async loadWards(code) {
            this.loadingWards = true;
            try {
                const res = await fetch(this.apiUrl + '/p/' + code + '?depth=2');
                const data = await res.json();
                this.wards = data.wards ?? [];
            } finally { this.loadingWards = false; }
        },
        selectWard(ward) {
            this.selectedWardCode = ward.code;
            this.wardSearch = ward.name;
            this.showWardList = false;
            $wire.set('wardCode', ward.code);
            $wire.set('wardName', ward.name);
        },
        clearWard() {
            this.selectedWardCode = ''; this.wardSearch = '';
            $wire.set('wardCode', ''); $wire.set('wardName', '');
        }
    }" class="fixed inset-0 z-[200] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" wire:click="closeModal"></div>

        {{-- Modal box --}}
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-5 bg-black rounded-full"></div>
                    <h3 class="font-black text-base">
                        {{ $editingId ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ mới' }}
                    </h3>
                </div>
                <button wire:click="closeModal"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-black transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto"
                @click.outside="showProvinceList = false; showWardList = false">

                <div class="grid grid-cols-2 gap-4">
                    {{-- Họ tên --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Họ tên người
                            nhận</label>
                        <input wire:model="receiverName" type="text" placeholder="Nguyễn Văn A"
                            class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white rounded-xl px-4 py-2.5 text-sm font-medium transition-all outline-none" />
                        @error('receiverName')<p class="text-red-500 text-[10px] font-bold">{{ $message }}</p>@enderror
                    </div>

                    {{-- SĐT --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Số điện
                            thoại</label>
                        <input wire:model="receiverPhone" type="text" placeholder="0912 345 678"
                            class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white rounded-xl px-4 py-2.5 text-sm font-medium transition-all outline-none" />
                        @error('receiverPhone')<p class="text-red-500 text-[10px] font-bold">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Tỉnh/Thành --}}
                    <div class="space-y-1.5" @click.stop>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tỉnh / Thành
                            phố</label>
                        <div class="relative">
                            <input type="text" x-model="provinceSearch"
                                @focus="showProvinceList = true; showWardList = false"
                                @input="showProvinceList = true; if(!$event.target.value) clearProvince()"
                                @keydown.escape="showProvinceList = false" placeholder="Tìm tỉnh..."
                                :disabled="loadingProvinces" autocomplete="off"
                                class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white rounded-xl pl-4 pr-8 py-2.5 text-sm font-medium transition-all outline-none disabled:opacity-50" />
                            <button x-show="selectedProvinceCode" @click="clearProvince()" type="button"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <span x-show="loadingProvinces" class="absolute right-2.5 top-1/2 -translate-y-1/2">
                                <div
                                    class="animate-spin h-3.5 w-3.5 border-2 border-black border-t-transparent rounded-full">
                                </div>
                            </span>
                            <div x-show="showProvinceList && filteredProvinces.length > 0"
                                class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                                <ul class="max-h-44 overflow-y-auto py-1">
                                    <template x-for="p in filteredProvinces" :key="p.code">
                                        <li @mousedown.prevent="selectProvince(p)"
                                            :class="selectedProvinceCode == p.code ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
                                            class="px-4 py-2 text-sm font-medium cursor-pointer" x-text="p.name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        @error('provinceName')<p class="text-red-500 text-[10px] font-bold">{{ $message }}</p>@enderror
                    </div>

                    {{-- Phường/Xã --}}
                    <div class="space-y-1.5" @click.stop>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Phường /
                            Xã</label>
                        <div class="relative">
                            <input type="text" x-model="wardSearch"
                                @focus="if(selectedProvinceCode){ showWardList = true; showProvinceList = false }"
                                @input="showWardList = true; if(!$event.target.value) clearWard()"
                                @keydown.escape="showWardList = false" placeholder="Tìm phường/xã..."
                                :disabled="!selectedProvinceCode || loadingWards" autocomplete="off"
                                class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white rounded-xl pl-4 pr-8 py-2.5 text-sm font-medium transition-all outline-none disabled:opacity-40" />
                            <button x-show="selectedWardCode" @click="clearWard()" type="button"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <span x-show="loadingWards" class="absolute right-2.5 top-1/2 -translate-y-1/2">
                                <div
                                    class="animate-spin h-3.5 w-3.5 border-2 border-black border-t-transparent rounded-full">
                                </div>
                            </span>
                            <div x-show="showWardList && filteredWards.length > 0"
                                class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                                <ul class="max-h-44 overflow-y-auto py-1">
                                    <template x-for="w in filteredWards" :key="w.code">
                                        <li @mousedown.prevent="selectWard(w)"
                                            :class="selectedWardCode == w.code ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
                                            class="px-4 py-2 text-sm font-medium cursor-pointer" x-text="w.name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        @error('wardName')<p class="text-red-500 text-[10px] font-bold">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Địa chỉ --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Số nhà, tên
                        đường</label>
                    <input wire:model="addressLine" type="text" placeholder="123 Đường Nguyễn Văn A..."
                        class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white rounded-xl px-4 py-2.5 text-sm font-medium transition-all outline-none" />
                    @error('addressLine')<p class="text-red-500 text-[10px] font-bold">{{ $message }}</p>@enderror
                </div>

                {{-- Mặc định --}}
                <label class="flex items-center gap-3 cursor-pointer group w-fit">
                    <div class="relative flex items-center">
                        <input type="checkbox" wire:model="isDefault"
                            class="peer appearance-none w-5 h-5 border-2 border-gray-200 rounded-md checked:bg-black checked:border-black transition-all" />
                        <svg class="absolute w-3 h-3 text-white left-1 pointer-events-none hidden peer-checked:block"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-500 group-hover:text-black transition-colors">
                        Đặt làm địa chỉ mặc định
                    </span>
                </label>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/40">
                <button wire:click="saveAddress" wire:loading.attr="disabled" wire:target="saveAddress"
                    class="flex-1 bg-black text-white py-2.5 rounded-xl text-sm font-black tracking-widest hover:bg-gray-800 transition-all active:scale-[0.98] disabled:opacity-60 flex items-center justify-center gap-2">
                    <svg wire:loading.remove wire:target="saveAddress" class="w-4 h-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg wire:loading wire:target="saveAddress" class="w-4 h-4 animate-spin" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                    </svg>
                    <span wire:loading.remove
                        wire:target="saveAddress">{{ $editingId ? 'CẬP NHẬT' : 'LƯU ĐỊA CHỈ' }}</span>
                    <span wire:loading wire:target="saveAddress">ĐANG LƯU...</span>
                </button>
                <button wire:click="closeModal" type="button"
                    class="flex-1 border border-gray-200 text-gray-500 py-2.5 rounded-xl text-sm font-bold hover:border-gray-400 hover:text-black transition-all active:scale-[0.98]">
                    Hủy bỏ
                </button>
            </div>
        </div>
    </div>
    @endif

</div>