{{--
    Consent-gated external content. The `.consent-message--wrapper` + iframe[data-src]
    markup is wired by <x-consent-control-scripts /> via ConsentMessage.new(): the
    real `src` is only set once the visitor grants the given consent category.
--}}
<div
    {{ $attributes->merge(['class' => 'consent-message--wrapper']) }}
    data-consent="{{ $consent }}"
    data-src-name="{{ $srcName }}"
>
    <div class="consent-message">
        <button type="button" class="confirm">
            {{ __('consent-control::consent.message.button') }}
        </button>
        <p>{!! __('consent-control::consent.message.text', [
            'source' => '<i class="consent-message--source">' . e($srcName) . '</i>',
            'privacy_url' => $privacyUrl,
        ]) !!}</p>
    </div>

    @if ($type === 'iframe')
        <iframe
            data-src="{{ $src }}"
            @if ($width) width="{{ $width }}" @endif
            @if ($height) height="{{ $height }}" @endif
            loading="lazy"
            {{ $attributes->only(['style', 'allow', 'allowfullscreen', 'frameborder', 'title', 'sandbox', 'referrerpolicy']) }}
        ></iframe>
    @else
        {{-- Custom content: revealed (cloned into place) on consent. --}}
        <template class="consent-content">{{ $slot }}</template>
    @endif
</div>
