<span>
    @if($count > 0)
    <span
        class="absolute top-2 right-2 bg-amber-500 text-white text-[8px] w-4 h-4 rounded-full flex items-center justify-center font-black">
        {{ $count > 99 ? '99+' : $count }}
    </span>
    @endif
</span>