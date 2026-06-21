@props(['paginator'])

@php
    $elements = $paginator->getUrlRange(1, $paginator->lastPage());
    $window = 3;
    $lastPage = $paginator->lastPage();
    $currentPage = $paginator->currentPage();

    // Build compact elements with "..." separators
    $compact = [];
    foreach ($elements as $page => $url) {
        if ($page == 1 || $page == $lastPage || ($page >= $currentPage - $window && $page <= $currentPage + $window)) {
            $compact[$page] = $url;
        }
    }

    $final = [];
    $prevPage = 0;
    foreach ($compact as $page => $url) {
        if ($page - $prevPage > 1) {
            $final[] = '...';
        }
        $final[$page] = $url;
        $prevPage = $page;
    }
    $elements = $final;
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" {{ $attributes->class(['flex flex-col sm:flex-row items-center justify-between gap-3']) }}>
        {{-- Info --}}
        <div class="text-sm text-gray-500 order-2 sm:order-1">
            Menampilkan <span class="font-semibold text-[#0F034D]">{{ $paginator->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#0F034D]">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#0F034D]">{{ $paginator->total() }}</span> data
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-1.5 order-1 sm:order-2">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-[#0F034D]/5 hover:text-[#0F034D] hover:border-[#0F034D]/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $key => $value)
                @if ($value === '...')
                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-gray-400 select-none">…</span>
                @elseif ($key == $paginator->currentPage())
                    <span aria-current="page" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-1.5 text-xs font-bold text-white bg-[#0F034D] rounded-lg select-none shadow-sm shadow-[#0F034D]/20">{{ $key }}</span>
                @else
                    <a href="{{ $value }}" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-[#0F034D]/5 hover:text-[#0F034D] hover:border-[#0F034D]/30 transition-colors">{{ $key }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-[#0F034D]/5 hover:text-[#0F034D] hover:border-[#0F034D]/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
