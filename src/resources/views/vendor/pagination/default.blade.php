@if ($paginator->hasPages())
    <nav class="c-pagination" aria-label="ページネーション">
        <ul class="c-pagination__list">
            @if ($paginator->onFirstPage())
                <li>
                    <span class="c-pagination__disabled" aria-disabled="true">
                        前へ
                    </span>
                </li>
            @else
                <li>
                    <a
                        class="c-pagination__link"
                        href="{{ $paginator->previousPageUrl() }}"
                        rel="prev"
                    >
                        前へ
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="c-pagination__disabled">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <li>
                                <span
                                    class="c-pagination__current"
                                    aria-current="page"
                                >
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a
                                    class="c-pagination__link"
                                    href="{{ $url }}"
                                    aria-label="{{ $page }}ページ目へ移動"
                                >
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <a
                        class="c-pagination__link"
                        href="{{ $paginator->nextPageUrl() }}"
                        rel="next"
                    >
                        次へ
                    </a>
                </li>
            @else
                <li>
                    <span class="c-pagination__disabled" aria-disabled="true">
                        次へ
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
