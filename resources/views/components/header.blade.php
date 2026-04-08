<header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between shrink-0">
    <div id="breadcrumb" class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-slate-400">
        @if(isset($breadcrumb) && count($breadcrumb) > 0)
            @foreach($breadcrumb as $index => $crumb)
                @if($index > 0)
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                @endif
                <span class="{{ $loop->last ? 'text-slate-800' : '' }}" 
                      @if($loop->last) id="current-view-name" @endif>
                    {{ $crumb }}
                </span>
            @endforeach
        @else
            <span>Menu</span>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-slate-800" id="current-view-name">Cursos</span>
        @endif
    </div>
    <div class="flex items-center gap-4">
        <div class="w-px h-6 bg-slate-200"></div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-800 uppercase leading-none">
                    {{ $user['name'] ?? 'Admin Usuario' }}
                </p>
                <p class="text-[9px] font-bold text-blue-600 uppercase tracking-tighter">
                    {{ $user['role'] ?? 'Súper Usuario' }}
                </p>
            </div>
            <div class="w-10 h-10 bg-slate-100 rounded-full border border-slate-200 flex items-center justify-center">
                <i data-lucide="user" class="text-slate-400 w-5 h-5"></i>
            </div>
        </div>
    </div>
</header>
