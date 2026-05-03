{{-- resources/views/livewire/order-failed.blade.php --}}
<div class="max-w-lg mx-auto px-6 py-20 text-center">

    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" />
        </svg>
    </div>

    <h1 class="text-3xl font-black tracking-tighter mb-2">Thanh toán thất bại</h1>
    <p class="text-gray-400 text-sm mb-2">Mã đơn hàng đã bị hủy:</p>
    <p class="text-lg font-black tracking-widest text-red-400 mb-8">{{ $invoice->invoice_number }}</p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/checkout"
            class="inline-block bg-black text-white px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-[0.15em] hover:bg-gray-900 transition-all">
            Thử lại
        </a>
        <a href="/"
            class="inline-block border border-gray-200 text-gray-600 px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-[0.15em] hover:border-black hover:text-black transition-all">
            Về trang chủ
        </a>
    </div>
</div>