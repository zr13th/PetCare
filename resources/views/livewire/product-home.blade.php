<div class="max-w-7xl mx-auto px-6 py-12">

    <div class="mb-12 flex items-end justify-between">
        <div>
            <h2 class="text-3xl font-bold tracking-tighter text-black">Sản phẩm mới nhất</h2>
            <p class="mt-2 text-gray-500 font-medium">Hàng hiệu cho thú cưng, tối giản cho ngôi nhà bạn.</p>
        </div>
        <a href="#" class="text-sm font-semibold hover:text-gray-600 transition-colors decoration-1 underline-offset-4">
            Thêm &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($products as $product)
        <div
            class="group relative flex flex-col bg-white border border-gray-200 rounded-xl overflow-hidden transition-all duration-500 hover:border-black hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)]">

            <div class="aspect-square w-full bg-gray-50 overflow-hidden relative border-b border-gray-100">
                @php
                $mediaUrl = $product->getFirstMediaUrl('product_images');
                @endphp

                @if($mediaUrl)
                <img src="{{ $mediaUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center">
                @else
                <div class="flex flex-col items-center justify-center h-full text-gray-300">
                    <svg class="w-12 h-12 stroke-[1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                @endif

                @if($product->brand)
                <span
                    class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm border border-gray-100 text-[9px] font-bold px-2 py-0.5 rounded shadow-sm">
                    {{ $product->brand->name }}
                </span>
                @endif
            </div>

            <div class="p-6 flex flex-col flex-1 bg-white">
                <div class="mb-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            {{ $product->petType->name ?? 'Pet' }}
                        </span>
                    </div>
                    <h3
                        class="text-sm font-semibold text-gray-900 tracking-tight leading-tight min-h-[2.2rem] line-clamp-2">
                        <a href="{{ route('product.detail', $product->slug) }}">
                            <span aria-hidden="true" class="absolute inset-0"></span>
                            {{ $product->name }}
                        </a>
                    </h3>
                </div>

                <div class="mt-auto border-t border-gray-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Giá từ</p>
                        <p class="text-[15px] font-bold text-black tracking-tight">
                            {{ number_format($product->variants->min('price')) }}đ
                        </p>
                    </div>

                    <div
                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-black group-hover:border-black transition-all duration-300">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2.5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>