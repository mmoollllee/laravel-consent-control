{{--
    Shows its slot only when the given consent category is granted. Rendered with
    the correct initial state server-side (no flash) and kept in sync client-side
    via the `consent-updated` event (see <x-consent-control-scripts />).
--}}
<div
    {{ $attributes->merge(['class' => 'consent-gate']) }}
    data-consent="{{ $consent }}"
    @unless ($granted) style="display:none" @endunless
>
    {{ $slot }}
</div>
