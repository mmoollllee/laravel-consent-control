<script
    type="text/plain"
    data-consent="{{ $consent }}"
    @if ($src) src="{{ $src }}" @endif
    {{ $attributes }}
>{!! $slot !!}</script>
