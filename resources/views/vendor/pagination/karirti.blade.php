@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Paginasi" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 text-sm">
        <div class="text-xs text-mist">
            Menampilkan
            <span class="font-medium text-paper">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-medium text-paper">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-medium text-paper">{{ $paginator->total() }}</span>
            hasil
        </div>

        <ul class="inline-flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="inline-flex items-center rounded-control border border-ink-line/50 bg-ink-raised/50 px-3 py-1.5 text-xs text-mist/50 cursor-not-allowed">
                        Sebelumnya
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"
                       class="inline-flex items-center rounded-control border border-ink-line bg-ink-raised px-3 py-1.5 text-xs text-mist hover:border-signal hover:text-paper transition-colors">
                        Sebelumnya
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li aria-disabled="true"><span class="px-2 text-xs text-mist">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page">
                                <span class="inline-flex items-center rounded-control border border-signal bg-signal/20 px-3 py-1.5 text-xs font-semibold text-signal">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="inline-flex items-center rounded-control border border-ink-line bg-ink-raised px-3 py-1.5 text-xs text-mist hover:border-signal hover:text-paper transition-colors">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"
                       class="inline-flex items-center rounded-control border border-ink-line bg-ink-raised px-3 py-1.5 text-xs text-mist hover:border-signal hover:text-paper transition-colors">
                        Berikutnya
                    </a>
                </li>
            @else
                <li aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="inline-flex items-center rounded-control border border-ink-line/50 bg-ink-raised/50 px-3 py-1.5 text-xs text-mist/50 cursor-not-allowed">
                        Berikutnya
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
