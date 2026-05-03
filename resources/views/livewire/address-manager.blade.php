<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-cloak>

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-black">Địa chỉ của tôi</h1>
            <p class="text-gray-400 text-sm mt-0.5 font-medium">Quản lý danh sách địa chỉ giao hàng</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
            class="bg-black text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-800 transition-all active:scale-95 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Thêm địa chỉ
        </button>
        @endif
    </div>

    @if(session('success'))
    <div
        class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-xl text-sm text-emerald-800 font-semibold flex items-center gap-2.5">
        <span
            class="flex-shrink-0 w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center text-[10px]">✓</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- FORM --}}
    @if($showForm)
    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 mb-8 shadow-sm" x-data="{
            provinces: [],
            wards: [],
            selectedProvinceCode: @js($provinceCode) || '',
            selectedWardCode: @js($wardCode) || '',
            provinceSearch: @js($provinceName) || '',
            wardSearch: @js($wardName) || '',
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
                if (this.selectedProvinceCode) {
                    await this.loadWards(this.selectedProvinceCode);
                    this.selectedWardCode = @js($wardCode) || '';
                }
            },

            async loadProvinces() {
                this.loadingProvinces = true;
                try {
                    const res = await fetch(this.apiUrl + '/p/');
                    this.provinces = await res.json();
                } catch (e) {
                    console.error('Lỗi tải tỉnh/thành:', e);
                } finally {
                    this.loadingProvinces = false;
                }
            },

            selectProvince(province) {
                this.selectedProvinceCode = province.code;
                this.provinceSearch = province.name;
                this.showProvinceList = false;
                this.wards = [];
                this.selectedWardCode = '';
                this.wardSearch = '';
                $wire.set('provinceCode', province.code);
                $wire.set('provinceName', province.name);
                $wire.set('wardCode', '');
                $wire.set('wardName', '');
                this.loadWards(province.code);
            },

            clearProvince() {
                this.selectedProvinceCode = '';
                this.provinceSearch = '';
                this.wards = [];
                this.selectedWardCode = '';
                this.wardSearch = '';
                $wire.set('provinceCode', '');
                $wire.set('provinceName', '');
                $wire.set('wardCode', '');
                $wire.set('wardName', '');
            },

            async loadWards(provinceCode) {
                this.loadingWards = true;
                try {
                    const res = await fetch(this.apiUrl + '/p/' + provinceCode + '?depth=2');
                    const data = await res.json();
                    this.wards = data.wards ?? [];
                } catch (e) {
                    console.error('Lỗi tải phường/xã:', e);
                    this.wards = [];
                } finally {
                    this.loadingWards = false;
                }
            },

            selectWard(ward) {
                this.selectedWardCode = ward.code;
                this.wardSearch = ward.name;
                this.showWardList = false;
                $wire.set('wardCode', ward.code);
                $wire.set('wardName', ward.name);
            },

            clearWard() {
                this.selectedWardCode = '';
                this.wardSearch = '';
                $wire.set('wardCode', '');
                $wire.set('wardName', '');
            }
        }" @click.outside="showProvinceList = false; showWardList = false">

        <div class="flex items-center gap-3 mb-7">
            <div class="h-6 w-1 bg-black rounded-full"></div>
            <h2 class="text-base font-bold tracking-tight">
                {{ $editingId ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ mới' }}
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Họ tên --}}
            <div class="space-y-1.5">
                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400">Họ tên người nhận</label>
                <input wire:model="receiverName" type="text" placeholder="Nguyễn Văn A"
                    class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white focus:ring-0 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none">
                @error('receiverName')
                <p class="text-red-500 text-[11px] font-bold uppercase">{{ $message }}</p>
                @enderror
            </div>

            {{-- Số điện thoại --}}
            <div class="space-y-1.5">
                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400">Số điện thoại</label>
                <input wire:model="receiverPhone" type="text" placeholder="0912 345 678"
                    class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white focus:ring-0 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none">
                @error('receiverPhone')
                <p class="text-red-500 text-[11px] font-bold uppercase">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tỉnh / Thành phố — Searchable combobox --}}
            <div class="space-y-1.5" @click.stop>
                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400">Tỉnh / Thành phố</label>
                <div class="relative">
                    <input type="text" x-model="provinceSearch" @focus="showProvinceList = true; showWardList = false"
                        @input="showProvinceList = true; if(!$event.target.value) clearProvince()"
                        @keydown.escape="showProvinceList = false" placeholder="Tìm tỉnh / thành phố..."
                        :disabled="loadingProvinces" autocomplete="off"
                        class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white focus:ring-0 rounded-xl pl-4 pr-9 py-3 text-sm font-medium transition-all outline-none disabled:opacity-50">
                    <button x-show="selectedProvinceCode" @click="clearProvince()" type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <span x-show="!selectedProvinceCode && !loadingProvinces"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                    <span x-show="loadingProvinces" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <div class="animate-spin h-4 w-4 border-2 border-black border-t-transparent rounded-full"></div>
                    </span>

                    <div x-show="showProvinceList && filteredProvinces.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                        <ul class="max-h-52 overflow-y-auto py-1">
                            <template x-for="p in filteredProvinces" :key="p.code">
                                <li @mousedown.prevent="selectProvince(p)"
                                    :class="selectedProvinceCode == p.code ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2.5 text-sm font-medium cursor-pointer transition-colors"
                                    x-text="p.name">
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div x-show="showProvinceList && filteredProvinces.length === 0 && provinceSearch && !loadingProvinces"
                        class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-lg px-4 py-3 text-sm text-gray-400">
                        Không tìm thấy kết quả
                    </div>
                </div>
                @error('provinceName')
                <p class="text-red-500 text-[11px] font-bold uppercase">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phường / Xã — Searchable combobox --}}
            <div class="space-y-1.5" @click.stop>
                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400">Phường / Xã</label>
                <div class="relative">
                    <input type="text" x-model="wardSearch"
                        @focus="if(selectedProvinceCode) { showWardList = true; showProvinceList = false }"
                        @input="showWardList = true; if(!$event.target.value) clearWard()"
                        @keydown.escape="showWardList = false" placeholder="Tìm phường / xã..."
                        :disabled="!selectedProvinceCode || loadingWards" autocomplete="off"
                        class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white focus:ring-0 rounded-xl pl-4 pr-9 py-3 text-sm font-medium transition-all outline-none disabled:opacity-40">
                    <button x-show="selectedWardCode" @click="clearWard()" type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <span x-show="!selectedWardCode && !loadingWards"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                    <span x-show="loadingWards" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <div class="animate-spin h-4 w-4 border-2 border-black border-t-transparent rounded-full"></div>
                    </span>

                    <div x-show="showWardList && filteredWards.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                        <ul class="max-h-52 overflow-y-auto py-1">
                            <template x-for="w in filteredWards" :key="w.code">
                                <li @mousedown.prevent="selectWard(w)"
                                    :class="selectedWardCode == w.code ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2.5 text-sm font-medium cursor-pointer transition-colors"
                                    x-text="w.name">
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div x-show="showWardList && filteredWards.length === 0 && wardSearch && !loadingWards"
                        class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-lg px-4 py-3 text-sm text-gray-400">
                        Không tìm thấy kết quả
                    </div>
                </div>
                @error('wardName')
                <p class="text-red-500 text-[11px] font-bold uppercase">{{ $message }}</p>
                @enderror
            </div>

            {{-- Số nhà, đường --}}
            <div class="md:col-span-2 space-y-1.5">
                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400">Số nhà, tên đường</label>
                <input wire:model="addressLine" type="text" placeholder="123 Đường Nguyễn Văn A..."
                    class="w-full bg-gray-50 border border-transparent focus:border-black focus:bg-white focus:ring-0 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none">
                @error('addressLine')
                <p class="text-red-500 text-[11px] font-bold uppercase">{{ $message }}</p>
                @enderror
            </div>

            {{-- Checkbox mặc định --}}
            <div class="md:col-span-2">
                <label class="flex items-center gap-3 cursor-pointer group w-fit">
                    <div class="relative flex items-center">
                        <input type="checkbox" wire:model="isDefault"
                            class="peer appearance-none w-5 h-5 border-2 border-gray-200 rounded-md checked:bg-black checked:border-black transition-all">
                        <svg class="absolute w-3 h-3 text-white left-1 pointer-events-none hidden peer-checked:block"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-500 group-hover:text-black transition-colors">Đặt làm
                        địa chỉ mặc định</span>
                </label>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 mt-7 pt-6 border-t border-gray-50">
            <button wire:click="save" wire:loading.attr="disabled"
                class="sm:flex-1 bg-black text-white px-6 py-3.5 rounded-xl text-sm font-black tracking-widest hover:bg-gray-800 transition-all active:scale-[0.98] disabled:opacity-60">
                <span wire:loading.remove wire:target="save">{{ $editingId ? 'CẬP NHẬT' : 'LƯU ĐỊA CHỈ' }}</span>
                <span wire:loading wire:target="save">ĐANG XỬ LÝ...</span>
            </button>
            <button wire:click="cancel" type="button"
                class="sm:flex-1 border border-gray-200 text-gray-500 px-6 py-3.5 rounded-xl text-sm font-bold hover:border-gray-400 hover:text-black transition-all active:scale-[0.98]">
                Hủy bỏ
            </button>
        </div>
    </div>
    @endif

    {{-- DANH SÁCH --}}
    @if($addresses->isEmpty() && !$showForm)
    <div class="text-center py-20 border-2 border-dashed border-gray-100 rounded-2xl bg-gray-50/40">
        <div
            class="w-16 h-16 bg-white shadow-sm border border-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
            📍</div>
        <p class="text-base text-black font-black tracking-tight">Chưa có địa chỉ nào</p>
        <p class="text-gray-400 text-sm mt-1.5 max-w-xs mx-auto leading-relaxed">Vui lòng thêm địa chỉ để chúng tôi có
            thể giao hàng đến bạn.</p>
        <button wire:click="openCreate"
            class="mt-6 text-xs font-black uppercase tracking-widest bg-black text-white px-8 py-3 rounded-xl hover:bg-gray-800 transition-all active:scale-95">
            + Thêm địa chỉ đầu tiên
        </button>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($addresses as $address)
        <div class="bg-white border-2 rounded-2xl p-6 transition-all duration-200
            {{ $address->is_default ? 'border-black shadow-sm' : 'border-gray-100 hover:border-gray-300' }}"
            wire:key="addr-{{ $address->id }}">
            <div class="flex flex-col h-full justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2.5 mb-3">
                        <p class="font-black text-lg text-black tracking-tight">{{ $address->receiver_name }}</p>
                        @if($address->is_default)
                        <span
                            class="bg-black text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">Mặc
                            định</span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-gray-700 mb-3">{{ $address->receiver_phone }}</p>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        {{ $address->address_line }},
                        <span class="text-gray-400">{{ $address->ward }}, {{ $address->province }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                    @if(!$address->is_default)
                    <button wire:click="setDefault({{ $address->id }})"
                        class="text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-black transition-colors px-1">
                        Đặt mặc định
                    </button>
                    @endif
                    <div class="flex-1"></div>
                    <button wire:click="openEdit({{ $address->id }})"
                        class="h-9 px-4 rounded-lg bg-gray-50 text-gray-500 text-xs font-bold hover:bg-black hover:text-white transition-all">
                        Sửa
                    </button>
                    <button wire:click="delete({{ $address->id }})" wire:confirm="Xóa địa chỉ này?"
                        class="h-9 px-4 rounded-lg bg-red-50 text-red-400 text-xs font-bold hover:bg-red-500 hover:text-white transition-all">
                        Xóa
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>