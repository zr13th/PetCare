{{-- resources/views/livewire/payment-sandbox.blade.php --}}
<div class="max-w-lg mx-auto px-6 py-20">

    {{-- Header giả lập cổng thanh toán --}}
    <div class="text-center mb-10">
        <div
            class="inline-flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-6">
            🧪 Sandbox Payment Gateway
        </div>
        <h1 class="text-2xl font-black tracking-tighter">Giả lập thanh toán</h1>
        <p class="text-gray-400 text-sm mt-2">Môi trường test — không có giao dịch thật</p>
    </div>

    {{-- Thông tin đơn hàng --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-6 space-y-3">
        <div class="flex justify-between text-sm">
            <span class="text-gray-400">Mã đơn hàng</span>
            <span class="font-bold tracking-wider">{{ $invoice->invoice_number }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-400">Người nhận</span>
            <span class="font-semibold">{{ $invoice->receiver_name }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-400">Số sản phẩm</span>
            <span class="font-semibold">{{ $invoice->items->count() }} sản phẩm</span>
        </div>
        <div class="flex justify-between text-sm border-t border-gray-100 pt-3">
            <span class="text-gray-400">Phí ship</span>
            <span class="font-semibold">
                {{ $invoice->shipping_fee > 0 ? number_format($invoice->shipping_fee, 0, ',', '.') . 'đ' : 'Miễn phí' }}
            </span>
        </div>
        <div class="flex justify-between text-lg font-black border-t border-gray-100 pt-3">
            <span>Tổng thanh toán</span>
            <span class="text-amber-500">{{ number_format($invoice->total_amount, 0, ',', '.') }}đ</span>
        </div>
    </div>

    {{-- Giả lập nhập thẻ --}}
    <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-6 mb-6 space-y-4">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Thông tin thẻ (giả lập)
        </p>
        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-1.5">Số thẻ</label>
            <input type="text" value="4111 1111 1111 1111" readonly
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white text-gray-400 cursor-not-allowed">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-1.5">Hết
                    hạn</label>
                <input type="text" value="12/30" readonly
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white text-gray-400 cursor-not-allowed">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-1.5">CVV</label>
                <input type="text" value="***" readonly
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white text-gray-400 cursor-not-allowed">
            </div>
        </div>
    </div>

    {{-- 2 nút xử lý --}}
    <div class="grid grid-cols-2 gap-4">
        <button wire:click="processPayment(false)" wire:loading.attr="disabled" class="py-4 rounded-2xl border-2 border-red-200 text-red-500 font-black text-sm uppercase tracking-widest
                   hover:bg-red-50 transition-all active:scale-95 disabled:opacity-50">
            <span wire:loading.remove wire:target="processPayment(false)">✗ Thất bại</span>
            <span wire:loading wire:target="processPayment(false)">Đang xử lý...</span>
        </button>

        <button wire:click="processPayment(true)" wire:loading.attr="disabled" class="py-4 rounded-2xl bg-black text-white font-black text-sm uppercase tracking-widest
                   hover:bg-gray-900 transition-all active:scale-95 disabled:opacity-50">
            <span wire:loading.remove wire:target="processPayment(true)">✓ Thành công</span>
            <span wire:loading wire:target="processPayment(true)">Đang xử lý...</span>
        </button>
    </div>

    <p class="text-center text-xs text-gray-300 mt-6">
        Sandbox mode — mọi giao dịch đều là giả lập
    </p>
</div>