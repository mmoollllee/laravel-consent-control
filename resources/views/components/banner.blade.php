{{--
    Server-rendered consent banner, styled with Tailwind utilities (scan this file
    via @source in your app.css). The markup matches the selector contract of the
    `consent-control` JS runtime, which only toggles state classes on it:
    `hide` (off-screen) and `is-collapsed` (compact pill) — expressed here as
    group-[.is-collapsed]/[&.hide]. Buttons use the host app's .btn component
    classes. Projects without Tailwind load the small fallback stylesheet instead
    (see <x-consent-control-scripts :standalone-css="true" />).

    Ships with an inline display:none so it can never flash before CSS/JS load
    (Vite dev server, late stylesheet, cached pages): visibility is JS-only —
    the runtime's show() clears the inline style (missing/stale consent on boot,
    reopen buttons) and only then animates via the `hide` class.
--}}
<div
    id="consent-control-banner"
    class="group hide is-collapsed flex flex-col gap-2 fixed bottom-0 right-0 z-[9999] w-full max-w-md overflow-auto rounded-md bg-white text-center text-sm text-gray-800 shadow-lg transition-all duration-500 sm:bottom-4 sm:right-4 sm:w-[calc(100%-2rem)] sm:max-w-lg [&.is-collapsed]:max-w-xs! [&.hide.is-collapsed]:translate-x-[calc(100%+2rem)]"
    style="display: none"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('consent-control::consent.banner.title') }}"
>
    <header class="pt-4 px-4">
        <h3 class="mb-1 text-base font-semibold group-[.is-collapsed]:hidden">
            {{ __('consent-control::consent.banner.title') }}
        </h3>
        <p class="text-sm group-[.is-collapsed]:text-xs group-[.is-collapsed]:mb-0!">
            {!! __('consent-control::consent.banner.description') !!}
            {{-- Privacy/imprint links: own line, only in the expanded panel --}}
            <span class="block mt-1 uncollapsed-only group-[.is-collapsed]:hidden">
                <a href="{{ $links['privacy'] ?? '/datenschutz/' }}" class="underline hover:text-gray-600">{{ __('consent-control::consent.banner.privacy_label') }}</a>
                ·
                <a href="{{ $links['imprint'] ?? '/impressum/' }}" class="underline hover:text-gray-600">{{ __('consent-control::consent.banner.imprint_label') }}</a>
            </span>
        </p>
        <button
            type="button"
            class="collapsed-only consent-control--open hidden group-[.is-collapsed]:inline-block text-xs text-gray-500 underline hover:text-gray-700"
        >
            {{ __('consent-control::consent.banner.settings_button') }}
        </button>
    </header>

    <div class="switches text-center flex flex-wrap justify-center gap-x-3 gap-y-1 bg-gray-50 group-[.is-collapsed]:bg-transparent">
        @foreach ($categories as $key => $category)
            @php
                $label = __($category['label'] ?? $key);
                $description = ! empty($category['description']) ? __($category['description']) : null;
                $children = $category['children'] ?? $category['childs'] ?? [];
            @endphp
            <div class="w-full border-b border-gray-100 py-3 last:border-b-0 group-[.is-collapsed]:w-auto group-[.is-collapsed]:inline-flex! group-[.is-collapsed]:items-center group-[.is-collapsed]:gap-2 group-[.is-collapsed]:border-0! group-[.is-collapsed]:py-0!">
                <label class="relative inline-flex cursor-pointer items-center gap-2">
                    <input
                        type="checkbox"
                        value="{{ $key }}"
                        class="peer sr-only"
                        role="switch"
                        @checked($category['checked'] ?? false)
                        @disabled($category['disabled'] ?? false)
                    >
                    <div class="h-5 w-9 shrink-0 rounded-full bg-gray-300 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform peer-checked:bg-primary peer-checked:after:translate-x-4 peer-disabled:cursor-not-allowed peer-disabled:opacity-50"></div>
                    <span class="font-medium text-sm group-[.is-collapsed]:text-xs">
                        {{ $label }}
                    </span>
                </label>

                @if ($description)
                    <p class="description mt-1 text-xs text-gray-500 mb-0! group-[.is-collapsed]:hidden">{!! $description !!}</p>
                @endif

                @if (! empty($children))
                    <ul class="childs mt-2 list-none group-[.is-collapsed]:hidden">
                        @foreach ($children as $child)
                            <li>
                                <h5 class="text-xs font-semibold">{{ __($child['label'] ?? '') }}</h5>
                                @if (! empty($child['description']))
                                    <p class="text-xs text-gray-500">{!! __($child['description']) !!}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>

    <div class="uncollapsed-only px-4 pb-2 group-[.is-collapsed]:hidden">
        <button type="button" class="consent-control--reset text-xs text-gray-400 underline hover:text-gray-600">
            {{ __('consent-control::consent.banner.reset_button') }}
        </button>
    </div>

    <div class="control flex flex-wrap items-center justify-center gap-2 pb-4 px-4 group-[.is-collapsed]:gap-1 group-[.is-collapsed]:pb-3 group-[.is-collapsed]:px-3">
        <button type="button" class="btn btn-secondary uncollapsed-only consent-control--close group-[.is-collapsed]:hidden">
            {{ __('consent-control::consent.banner.close_button') }}
        </button>
        @if ($banner['reject_button'] ?? false)
            <button type="button" class="btn btn-secondary uncollapsed-only consent-control--deny group-[.is-collapsed]:hidden" id="consent-control--submit-none">
                {{ __('consent-control::consent.banner.reject_all_button') }}
            </button>
        @endif
        <button type="button" class="btn btn-secondary group-[.is-collapsed]:px-4 group-[.is-collapsed]:py-2" id="consent-control--submit">
            {{ __('consent-control::consent.banner.ok_button') }}
        </button>
        <button type="button" class="btn btn-primary group-[.is-collapsed]:px-4 group-[.is-collapsed]:py-2" id="consent-control--submit-all">
            {{ __('consent-control::consent.banner.all_button') }}
        </button>
    </div>
</div>
