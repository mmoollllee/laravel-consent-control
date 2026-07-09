{{--
    Server-rendered consent banner. The markup matches the selector contract of
    the `consent-control` JS runtime, which only binds behaviour to it (no client
    re-render). Starts hidden via `hide is-collapsed`; the runtime reveals it on
    the first visit and re-opens it when a `.consent-control--open` button is clicked.
--}}
<div
    id="consent-control-banner"
    class="hide is-collapsed"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('consent-control::consent.banner.title') }}"
>
    <header>
        <h3>{{ __('consent-control::consent.banner.title') }}</h3>
        <p>{!! __('consent-control::consent.banner.description', [
            'privacy_url' => $links['privacy'] ?? '/datenschutz/',
            'imprint_url' => $links['imprint'] ?? '/impressum/',
        ]) !!}</p>
        <button type="button" class="collapsed-only consent-control--open">
            {{ __('consent-control::consent.banner.settings_button') }}
        </button>
    </header>

    <div class="switches">
        @foreach ($categories as $key => $category)
            @php
                $label = __($category['label'] ?? $key);
                $description = ! empty($category['description']) ? __($category['description']) : null;
                $children = $category['children'] ?? $category['childs'] ?? [];
            @endphp
            <div class="form-check form-switch">
                <input
                    id="consent-{{ $key }}"
                    value="{{ $key }}"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    @checked($category['checked'] ?? false)
                    @disabled($category['disabled'] ?? false)
                >
                <label for="consent-{{ $key }}" class="form-check-label">{{ $label }}</label>

                @if ($description)
                    <p class="description">{!! $description !!}</p>
                @endif

                @if (! empty($children))
                    <ul class="childs">
                        @foreach ($children as $child)
                            <li>
                                <h4>{{ __($child['label'] ?? '') }}</h4>
                                @if (! empty($child['description']))
                                    <p class="description">{!! __($child['description']) !!}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>

    <div class="uncollapsed-only">
        <button type="button" class="consent-control--reset">
            {{ __('consent-control::consent.banner.reset_button') }}
        </button>
    </div>

    <div class="control">
        <button type="button" class="secondary uncollapsed-only consent-control--close">
            {{ __('consent-control::consent.banner.close_button') }}
        </button>
        @if ($banner['reject_button'] ?? false)
            <button type="button" class="secondary uncollapsed-only consent-control--deny" id="consent-control--submit-none">
                {{ __('consent-control::consent.banner.reject_all_button') }}
            </button>
        @endif
        <button type="button" id="consent-control--submit">
            {{ __('consent-control::consent.banner.ok_button') }}
        </button>
        <button type="button" id="consent-control--submit-all">
            {{ __('consent-control::consent.banner.all_button') }}
        </button>
    </div>
</div>
