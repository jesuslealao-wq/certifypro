<aside id="sidebar" class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 z-50">
    <div class="p-6 border-b border-slate-100 flex items-center gap-3 overflow-hidden">
        <div class="bg-blue-600 p-2 rounded-xl text-white shadow-lg shadow-blue-200 shrink-0">
            <i data-lucide="shield-check"></i>
        </div>
        <span class="font-black text-xl tracking-tighter uppercase italic nav-text">Certify<span class="text-blue-600">Pro</span></span>
    </div>

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        @foreach($items as $item)
            @if(isset($item['divider']))
                <div class="pt-4 pb-2 px-3">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] nav-text">{{ $item['label'] }}</p>
                </div>
            @else
            @php
                $href = '#';
                if (!empty($item['route']) && \Illuminate\Support\Facades\Route::has($item['route'])) {
                    $href = route($item['route']);
                } elseif (!empty($item['url'])) {
                    $href = $item['url'];
                }
                    
                $isActive = false;
                if (!empty($item['route']) && \Illuminate\Support\Facades\Route::has($item['route'])) {
                    $isActive = request()->routeIs($item['route']);
                } elseif (!empty($item['url'])) {
                    $isActive = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/').'/*');
                }
            @endphp

<a
    href="{{ $href }}"
    class="nav-item w-full flex items-center gap-3 p-3 rounded-xl transition-all font-bold text-xs uppercase tracking-widest
    {{ $isActive ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}
>
    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
    <span class="nav-text">{{ $item['label'] }}</span>
</a>
            @endif
        @endforeach

    </nav>

    <div class="p-4 border-t border-slate-100">
        <button onclick="toggleSidebar()" class="w-full flex items-center gap-3 p-3 text-slate-400 hover:text-blue-600 transition-all font-bold text-[10px] uppercase tracking-widest">
            <i data-lucide="menu"></i>
            <span class="nav-text">Colapsar</span>
        </button>
    </div>
</aside>
