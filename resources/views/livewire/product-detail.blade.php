<div class="bg-white min-h-screen" x-data="{ 
        currentIndex: 0, 
        images: [
            @foreach($product->getMedia('product_images') as $image)
                '{{ $image->getUrl() }}',
            @endforeach
        ],
        qty: @entangle('quantity'),
        max: {{ $this->stock }},
        next() { this.currentIndex = (this.currentIndex + 1) % this.images.length; },
        prev() { this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length; }
    }">

    <div class="max-w-7xl mx-auto px-6 py-12 lg:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">

            {{-- 1. GALLERY --}}
            <div class="flex flex-col" wire:ignore>
                <div
                    class="relative aspect-square w-full rounded-3xl overflow-hidden border border-gray-100 bg-gray-50 group">
                    <div class="flex h-full transition-transform duration-500 ease-out"
                        :style="`transform: translateX(-${currentIndex * 100}%)`" style="width: 100%;">
                        <template x-for="(img, index) in images" :key="index">
                            <div class="w-full h-full flex-shrink-0">
                                <img :src="img" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>

                    <div
                        class="absolute inset-0 flex items-center justify-between px-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button @click="prev()" type="button"
                            class="p-3 rounded-full bg-white/90 backdrop-blur-sm text-black shadow-sm hover:bg-white active:scale-90 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button @click="next()" type="button"
                            class="p-3 rounded-full bg-white/90 backdrop-blur-sm text-black shadow-sm hover:bg-white active:scale-90 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <div class="absolute bottom-6 right-6 px-3 py-1 bg-black/20 backdrop-blur-md rounded-full">
                        <span class="text-[10px] font-black text-white tracking-widest"
                            x-text="(currentIndex + 1) + '/' + images.length"></span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-4 gap-4">
                    <template x-for="(img, index) in images" :key="index">
                        <div @click="currentIndex = index"
                            class="aspect-square rounded-xl border-2 overflow-hidden cursor-pointer transition-all duration-300"
                            :class="currentIndex === index ? 'border-amber-500 shadow-lg' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img :src="img" class="object-cover w-full h-full">
                        </div>
                    </template>
                </div>
            </div>

            {{-- 2. INFO --}}
            <div class="flex flex-col">
                <div class="border-b border-gray-100 pb-8">
                    <nav
                        class="flex items-center gap-2 mb-6 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">
                        <a href="/" class="hover:text-black transition-colors">Trang chủ</a>
                        <span>/</span>
                        <span class="text-black">{{ $product->category->name }}</span>
                    </nav>

                    <h1 class="text-4xl lg:text-5xl font-bold tracking-tighter text-black leading-[1.1] mb-6">
                        {{ $product->name }}
                    </h1>

                    <div class="flex items-center justify-between">
                        <div wire:key="price-{{ $this->currentVariant?->id }}">
                            @if($this->currentVariant)
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-bold tracking-tight text-amber-500">
                                    {{ number_format($this->currentVariant->price) }}đ
                                </span>
                                @if($this->currentVariant->compare_price)
                                <span
                                    class="text-lg text-gray-400 line-through">{{ number_format($this->currentVariant->compare_price) }}đ</span>
                                @endif
                            </div>
                            @else
                            <span class="text-gray-400 italic text-sm">Vui lòng chọn phân loại</span>
                            @endif
                        </div>

                        <div wire:key="stock-{{ $this->currentVariant?->id }}">
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-50 border border-gray-100">
                                <div
                                    class="w-1.5 h-1.5 rounded-full {{ $this->stock > 0 ? 'bg-green-500' : 'bg-red-500' }}">
                                </div>
                                <span class="text-[10px] font-black uppercase text-gray-500">
                                    {{ $this->stock > 0 ? 'Còn hàng' : 'Hết hàng' }} ({{ $this->stock }})
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- THUỘC TÍNH --}}
                <div class="py-10 space-y-10 border-b border-gray-100">
                    @foreach($attributes as $attrId => $attrData)
                    <div class="space-y-4" wire:key="attr-group-{{ $attrId }}">
                        <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-black">{{ $attrData['name'] }}
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($attrData['values'] as $valId => $valName)
                            <button wire:click="selectValue({{ $attrId }}, {{ $valId }})" wire:key="val-{{ $valId }}"
                                class="px-6 py-3 text-sm font-semibold border transition-all duration-300 rounded-xl
                                    {{ ($selectedAttributes[$attrId] ?? null) == $valId ? 'border-black bg-black text-white shadow-xl scale-105' : 'border-gray-200 text-gray-500 hover:border-black bg-white' }}">
                                {{ $valName }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- SỐ LƯỢNG & GIỎ HÀNG --}}
                <div class="py-10 space-y-8">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div
                            class="flex items-center justify-between border border-gray-200 rounded-2xl px-6 py-4 sm:w-40 bg-white">
                            <button type="button" @click="if(qty > 1) qty--"
                                class="text-gray-400 hover:text-black font-bold p-2 transition-all active:scale-75">－</button>
                            <span class="text-lg font-bold text-black select-none" x-text="qty"></span>
                            <button type="button" @click="if(qty < max) qty++"
                                class="text-gray-400 hover:text-black font-bold p-2 transition-all active:scale-75">＋</button>
                        </div>

                        {{-- NÚT THÊM GIỎ HÀNG ĐÃ SỬA --}}
                        <button wire:click="addToCart" wire:loading.attr="disabled"
                            {{ !$this->currentVariant || $this->stock <= 0 ? 'disabled' : '' }}
                            class="flex-1 bg-black text-white py-4 px-8 rounded-2xl font-bold text-sm uppercase tracking-[0.2em] transition-all hover:bg-gray-900 active:scale-[0.98] disabled:bg-gray-100 disabled:text-gray-300 shadow-2xl relative overflow-hidden group/btn">

                            {{-- Trạng thái bình thường --}}
                            <span wire:loading.remove wire:target="addToCart, selectValue">
                                {{ $this->stock > 0 ? ($this->currentVariant ? 'Thêm vào giỏ hàng' : 'Chọn phân loại') : 'Hết hàng' }}
                            </span>

                            {{-- Trạng thái đang xử lý --}}
                            <span wire:loading wire:target="addToCart, selectValue"
                                class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </div>

                    {{-- MÔ TẢ --}}
                    <div class="pt-8 border-t border-gray-100" x-data="{ expanded: false }">
                        <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-black mb-4">Mô tả sản phẩm
                        </h3>
                        <div class="relative transition-all duration-700 overflow-hidden"
                            :class="expanded ? 'max-h-[5000px]' : 'max-h-64'">
                            <div class="prose prose-sm text-gray-500 leading-relaxed max-w-none">
                                {!! $product->description !!}
                            </div>
                            <div x-show="!expanded"
                                class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-white via-white/80 to-transparent">
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-center relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-100"></div>
                            </div>
                            <button type="button" @click="expanded = !expanded"
                                class="relative inline-flex items-center gap-2 px-6 py-2 bg-white border border-gray-200 rounded-full text-[10px] font-black uppercase tracking-widest text-black hover:border-black transition-all">
                                <span x-text="expanded ? 'Thu gọn' : 'Xem thêm nội dung'"></span>
                                <svg class="w-3 h-3 transition-transform duration-500"
                                    :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- CHÍNH SÁCH --}}
                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-10">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-gray-50/50 border border-gray-100">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                        <span class="text-[11px] font-bold text-gray-600 uppercase tracking-tight">Chính hãng
                            100%</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-gray-50/50 border border-gray-100">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                        <span class="text-[11px] font-bold text-gray-600 uppercase tracking-tight">Giao toàn quốc</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>