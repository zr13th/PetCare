<div class="space-y-4 py-4">
    {{-- Lấy dữ liệu từ Infolist Component --}}
    @php
    $data = $getState();
    $movements = $data['movements'] ?? collect();
    @endphp

    @foreach($movements as $movement)
    <div
        class="relative pl-6 pb-4 border-l-2 {{ $movement->type === 'IN' ? 'border-green-200' : 'border-red-200' }} last:border-0 last:pb-0">
        <div
            class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $movement->type === 'IN' ? 'bg-green-500' : 'bg-red-500' }} border-2 border-white shadow-sm">
        </div>

        <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-sm font-bold {{ $movement->type === 'IN' ? 'text-green-700' : 'text-red-700' }}">
                        {{ $movement->type === 'IN' ? 'NHẬP' : 'XUẤT' }}: {{ $movement->quantity }} đơn vị
                    </span>
                    <p class="text-xs text-gray-500 font-medium italic mt-1">{{ $movement->notes }}</p>
                </div>
                <span
                    class="text-[10px] text-gray-400 font-mono">{{ $movement->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="mt-2 pt-2 border-t border-gray-50 flex items-center gap-3 text-[11px] text-gray-500">
                <span> <strong>Kho:</strong> {{ $movement->warehouse->name ?? 'N/A' }}</span>
                <span> <strong>Ref:</strong> {{ class_basename($movement->reference_type) }}
                    #{{ $movement->reference_id }}</span>
            </div>
        </div>
    </div>
    @endforeach

    @if($movements->isEmpty())
    <div class="text-center py-6">
        <p class="text-sm text-gray-400 italic">Lô hàng này chưa có dữ liệu biến động.</p>
    </div>
    @endif
</div>