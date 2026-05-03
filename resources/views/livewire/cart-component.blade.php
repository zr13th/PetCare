{{-- resources/views/livewire/cart-component.blade.php --}}
<div class="flex flex-col h-full p-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-xl font-bold">
            Giỏ hàng của bạn
            @if($count > 0)
            <span class="ml-2 text-sm font-normal text-gray-400">({{ $count }} sản phẩm)</span>
            @endif
        </h2>
        <button @click="cartOpen = false" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    {{-- Items --}}
    <div class="flex-1 overflow-y-auto -mx-2 px-2 space-y-4">
        @forelse($cartItems as $item)
        <div class="flex gap-4 py-4 border-b border-gray-100" wire:key="cart-item-{{ $item->rowId }}">

            {{-- Ảnh --}}
            <div class="w-20 h-20 rounded-xl bg-gray-50 border border-gray-100 flex-shrink-0 overflow-hidden">
                @php $img = $item->model?->product?->getFirstMediaUrl('product_images') @endphp
                @if($img)
                <img src="{{ $img }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                @else
                <div class="w-full h-full flex items-center justify-center text-2xl">🐾</div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-black leading-tight line-clamp-2">{{ $item->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $item->model?->sku }}</p>
                <p class="text-sm font-bold text-amber-500 mt-1">
                    {{ number_format($item->price, 0, ',', '.') }}đ
                </p>

                {{-- Qty control --}}
                <div class="flex items-center justify-between mt-3">
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                        <button wire:click="updateQty('{{ $item->rowId }}', {{ $item->qty - 1 }})"
                            class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-lg font-light">
                            −
                        </button>
                        <span class="w-8 text-center text-sm font-bold">{{ $item->qty }}</span>
                        <button wire:click="updateQty('{{ $item->rowId }}', {{ $item->qty + 1 }})"
                            class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-lg font-light">
                            +
                        </button>
                    </div>
                    <span class="text-sm font-bold text-black">
                        {{ number_format($item->price * $item->qty, 0, ',', '.') }}đ
                    </span>
                </div>
            </div>

            {{-- Xóa --}}
            <button wire:click="removeItem('{{ $item->rowId }}')"
                class="self-start text-gray-300 hover:text-red-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        @empty
        <div class="flex flex-col items-center justify-center h-64 text-center">
            <p class="text-gray-400 text-sm">Chưa có sản phẩm nào trong giỏ.</p>
            <button @click="cartOpen = false"
                class="mt-4 text-xs font-bold underline underline-offset-4 text-black hover:text-gray-500">
                Tiếp tục mua sắm
            </button>
        </div>
        @endforelse
    </div>

    {{-- Footer — giữ đúng style cũ của bạn --}}
    @if($cartItems->isNotEmpty())
    <div class="pt-6 border-t border-gray-100 mt-4">
        <div class="flex justify-between mb-6 text-lg font-bold">
            <span>Tổng tiền:</span>
            <span>{{ $total }}đ</span>
        </div>
        <a href="{{ route('checkout') }}"
            class="block w-full bg-black text-white text-center py-4 rounded-2xl font-bold uppercase tracking-widest hover:bg-gray-900 transition-all">
            Thanh toán ngay
        </a>
        <button wire:click="clearCart" wire:confirm="Xóa toàn bộ giỏ hàng?"
            class="block w-full text-center text-xs text-gray-400 hover:text-red-400 mt-3 transition-colors">
            Xóa tất cả
        </button>
    </div>
    @else
    {{-- Placeholder footer khi giỏ trống để giữ layout --}}
    <div class="pt-6 border-t border-gray-100 mt-4">
        <div class="flex justify-between mb-6 text-lg font-bold">
            <span>Tổng tiền:</span>
            <span>0đ</span>
        </div>
        <a href="#"
            class="block w-full bg-black text-white text-center py-4 rounded-2xl font-bold uppercase tracking-widest hover:bg-gray-900 transition-all opacity-40 pointer-events-none">
            Thanh toán ngay
        </a>
    </div>
    @endif

</div>