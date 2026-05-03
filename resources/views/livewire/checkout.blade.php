{{-- resources/views/livewire/checkout.blade.php --}}
<div class="max-w-6xl mx-auto px-6 py-12">

    <h1 class="text-3xl font-black tracking-tighter mb-10">Thanh toán</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">

        {{-- ===== CỘT TRÁI ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ĐỊA CHỈ --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-4">
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em]">Địa chỉ giao hàng</h2>

                @if($addresses->isNotEmpty())
                <div class="space-y-2">
                    @foreach($addresses as $addr)
                    <label wire:key="addr-{{ $addr->id }}"
                        class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all
                               {{ $selectedAddressId == $addr->id ? 'border-black bg-gray-50' : 'border-gray-100 hover:border-gray-300' }}">
                        {{-- Dùng wire:model.live + value thay vì wire:click + checked thủ công --}}
                        <input type="radio" name="selected_address" wire:model.live="selectedAddressId"
                            value="{{ $addr->id }}" class="mt-0.5 accent-black flex-shrink-0">
                        <div class="text-sm flex-1 min-w-0">
                            <p class="font-bold">{{ $addr->receiver_name }}
                                <span class="font-normal text-gray-400 ml-1">{{ $addr->receiver_phone }}</span>
                            </p>
                            <p class="text-gray-500 mt-0.5 text-xs leading-relaxed">
                                {{ $addr->address_line }}, {{ $addr->ward }}, {{ $addr->province }}
                            </p>
                            @if($addr->is_default)
                            <span
                                class="inline-block mt-1 text-[9px] font-black uppercase tracking-widest text-amber-500">Mặc
                                định</span>
                            @endif
                        </div>
                    </label>
                    @endforeach

                    <button wire:click="$set('selectedAddressId', null)"
                        class="text-xs font-bold text-gray-400 hover:text-black underline underline-offset-4 transition-colors">
                        + Dùng địa chỉ khác
                    </button>
                </div>
                @endif

                @if(!$selectedAddressId)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1.5">Họ
                            tên</label>
                        <input wire:model="receiverName" type="text" placeholder="Nguyễn Văn A"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors">
                        @error('receiverName') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1.5">Số
                            điện thoại</label>
                        <input wire:model="receiverPhone" type="text" placeholder="0912 345 678"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors">
                        @error('receiverPhone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1.5">Địa
                            chỉ</label>
                        <input wire:model="addressLine" type="text" placeholder="Số nhà, tên đường..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors">
                        @error('addressLine') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1.5">Tỉnh
                            / Thành phố</label>
                        <input wire:model="province" type="text" placeholder="TP. Hồ Chí Minh"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors">
                        @error('province') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1.5">Phường /
                            Xã</label>
                        <input wire:model="ward" type="text" placeholder="Phường Bến Nghé"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors">
                        @error('ward') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="saveAddress" class="accent-black">
                            <span class="text-sm text-gray-500">Lưu địa chỉ này cho lần sau</span>
                        </label>
                    </div>
                </div>
                @endif
            </div>

            {{-- VẬN CHUYỂN --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-4">
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em]">Phương thức vận chuyển</h2>
                <div class="space-y-2">
                    @foreach($shipmentMethods as $method)
                    <label wire:key="ship-{{ $method->id }}"
                        class="flex items-center justify-between p-4 border rounded-xl cursor-pointer transition-all
                               {{ $selectedShipmentId == $method->id ? 'border-black bg-gray-50' : 'border-gray-100 hover:border-gray-300' }}">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="shipment_method" wire:model.live="selectedShipmentId"
                                value="{{ $method->id }}" class="accent-black flex-shrink-0">
                            <div>
                                <p class="text-sm font-semibold">{{ $method->name }}</p>
                                @if($method->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $method->description }}</p>
                                @endif
                                @if($method->estimated_days > 0)
                                <p class="text-xs text-gray-400">Dự kiến {{ $method->estimated_days }} ngày</p>
                                @endif
                            </div>
                        </div>
                        <span
                            class="text-sm font-bold flex-shrink-0 ml-4 {{ $method->fee == 0 ? 'text-green-500' : '' }}">
                            {{ $method->fee > 0 ? number_format($method->fee, 0, ',', '.') . 'đ' : 'Miễn phí' }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- THANH TOÁN --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-4">
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em]">Phương thức thanh toán</h2>
                <div class="space-y-2">
                    @foreach($paymentMethods as $method)
                    <label wire:key="pay-{{ $method->id }}"
                        class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all
                               {{ $selectedPaymentId == $method->id ? 'border-black bg-gray-50' : 'border-gray-100 hover:border-gray-300' }}">
                        <input type="radio" name="payment_method" wire:model.live="selectedPaymentId"
                            value="{{ $method->id }}" class="mt-0.5 accent-black flex-shrink-0">
                        <div>
                            <p class="text-sm font-semibold">{{ $method->name }}</p>
                            @if($method->description)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $method->description }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- GHI CHÚ --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-3">
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em]">Ghi chú đơn hàng</h2>
                <textarea wire:model="note" rows="3" placeholder="Ghi chú cho người giao hàng..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors resize-none">
                </textarea>
            </div>
        </div>

        {{-- ===== CỘT PHẢI: Order Summary ===== --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 sticky top-24 space-y-6">
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em]">Đơn hàng</h2>

                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($cartItems as $item)
                    <div class="flex gap-3 items-start" wire:key="co-{{ $item->rowId }}">
                        <div
                            class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-100 flex-shrink-0 overflow-hidden">
                            @php $img = $item->model?->product?->getFirstMediaUrl('product_images') @endphp
                            @if($img)
                            <img src="{{ $img }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-lg">🐾</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold leading-tight line-clamp-2">{{ $item->name }}</p>
                            <p class="text-xs text-gray-400">x{{ $item->qty }}</p>
                        </div>
                        <p class="text-xs font-bold flex-shrink-0">
                            {{ number_format($item->price * $item->qty, 0, ',', '.') }}đ
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Tạm tính</span>
                        <span>{{ number_format($this->subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Phí vận chuyển</span>
                        <span class="{{ $this->shippingFee == 0 ? 'text-green-500 font-semibold' : '' }}">
                            {{ $this->shippingFee > 0 ? number_format($this->shippingFee, 0, ',', '.') . 'đ' : 'Miễn phí' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-lg font-black pt-3 border-t border-gray-100">
                        <span>Tổng cộng</span>
                        <span class="text-amber-500">{{ number_format($this->total, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <button wire:click="placeOrder" wire:loading.attr="disabled" class="w-full bg-black text-white py-4 rounded-2xl text-sm font-black uppercase tracking-[0.15em]
                           hover:bg-gray-900 transition-all active:scale-[0.98] disabled:opacity-50">
                    <span wire:loading.remove wire:target="placeOrder">Đặt hàng ngay</span>
                    <span wire:loading wire:target="placeOrder" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>

    </div>
</div>