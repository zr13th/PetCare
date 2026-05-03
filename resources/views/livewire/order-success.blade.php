{{-- resources/views/livewire/order-success.blade.php --}}
<div class="max-w-2xl mx-auto px-6 py-20">

    {{-- Icon --}}
    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <h1 class="text-3xl font-black tracking-tighter mb-2">Đặt hàng thành công!</h1>
        <p class="text-gray-400 text-sm">Cảm ơn bạn đã tin tưởng PetCare 🐾</p>
    </div>

    {{-- Mã đơn + trạng thái --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-4 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Mã đơn hàng</p>
            <p class="text-xl font-black tracking-wider text-amber-500">{{ $invoice->invoice_number }}</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Trạng thái</p>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest
                {{ match($invoice->status) {
                    'confirmed'  => 'bg-green-50 text-green-600',
                    'pending'    => 'bg-yellow-50 text-yellow-600',
                    'processing' => 'bg-blue-50 text-blue-600',
                    'shipped'    => 'bg-purple-50 text-purple-600',
                    'delivered'  => 'bg-green-50 text-green-700',
                    default      => 'bg-gray-50 text-gray-500',
                } }}">
                {{ $invoice->status_label }}
            </span>
        </div>
    </div>

    {{-- Thông tin giao hàng + thanh toán --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Giao đến</p>
            <p class="text-sm font-bold">{{ $invoice->receiver_name }}</p>
            <p class="text-sm text-gray-500">{{ $invoice->receiver_phone }}</p>
            <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                {{ $invoice->address_line }},<br>
                {{ $invoice->ward }}, {{ $invoice->district }},<br>
                {{ $invoice->province }}
            </p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Thanh toán</p>
            <p class="text-sm font-bold">{{ $invoice->payment?->paymentMethod?->name ?? '—' }}</p>
            <p class="text-sm mt-1">
                <span
                    class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                    {{ $invoice->payment?->status === 'completed' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                    {{ match($invoice->payment?->status) {
                        'completed' => 'Đã thanh toán',
                        'pending'   => 'Chờ thanh toán',
                        'failed'    => 'Thất bại',
                        default     => '—',
                    } }}
                </span>
            </p>
            @if($invoice->payment?->transaction_id)
            <p class="text-xs text-gray-400 mt-2">
                Mã GD: {{ $invoice->payment->transaction_id }}
            </p>
            @endif

            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-4 mb-1">Vận chuyển</p>
            <p class="text-sm font-bold">{{ $invoice->shipment?->shipmentMethod?->name ?? '—' }}</p>
            @if($invoice->shipment?->shipmentMethod?->estimated_days > 0)
            <p class="text-xs text-gray-400">Dự kiến {{ $invoice->shipment->shipmentMethod->estimated_days }} ngày</p>
            @endif
        </div>
    </div>

    {{-- Danh sách sản phẩm --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-4">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Sản phẩm đã đặt</p>
        <div class="space-y-3">
            @foreach($invoice->items as $item)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold leading-tight">{{ $item->product_name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        SKU: {{ $item->variant_sku }} &times; {{ $item->quantity }}
                    </p>
                </div>
                <p class="text-sm font-bold ml-4 flex-shrink-0">
                    {{ number_format($item->subtotal, 0, ',', '.') }}đ
                </p>
            </div>
            @endforeach
        </div>

        {{-- Tổng --}}
        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
            <div class="flex justify-between text-sm text-gray-500">
                <span>Tạm tính</span>
                <span>{{ number_format($invoice->subtotal, 0, ',', '.') }}đ</span>
            </div>
            <div class="flex justify-between text-sm text-gray-500">
                <span>Phí vận chuyển</span>
                <span>
                    {{ $invoice->shipping_fee > 0
                        ? number_format($invoice->shipping_fee, 0, ',', '.') . 'đ'
                        : 'Miễn phí' }}
                </span>
            </div>
            <div class="flex justify-between text-lg font-black pt-2 border-t border-gray-100">
                <span>Tổng cộng</span>
                <span class="text-amber-500">{{ number_format($invoice->total_amount, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="/"
            class="flex-1 text-center border border-gray-200 text-gray-600 py-4 rounded-2xl font-black text-sm uppercase tracking-[0.15em] hover:border-black hover:text-black transition-all">
            Tiếp tục mua sắm
        </a>
        {{-- TODO: Trang lịch sử đơn hàng --}}
        {{-- <a href="{{ route('orders') }}" class="flex-1 ...">Xem đơn hàng</a> --}}
    </div>
</div>