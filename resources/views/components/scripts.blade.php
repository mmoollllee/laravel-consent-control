{{--
    Boots consent-control with the server-side config. Include once per page
    (e.g. before </body>).

    :assets="false"  – you bundle the runtime JS + CSS yourself (Vite import from
                       the vendor dist); only the inline boot config is emitted.
    default          – loads the published runtime + consent-message CSS from
                       public/vendor/consent-control. Tailwind projects pass
                       :standalone-css="false" (the banner Blade styles itself);
                       others keep the default fallback stylesheet.
--}}
@once
    @if ($assets)
        <link rel="stylesheet" href="{{ asset('vendor/consent-control/css/consent-message.css') }}">
        @if ($standaloneCss)
            <link rel="stylesheet" href="{{ asset('vendor/consent-control/css/consent-control.css') }}">
        @endif
        <script src="{{ asset('vendor/consent-control/js/consent-control.js') }}"></script>
    @endif
    <script>
        (function () {
            var config = {!! \Illuminate\Support\Js::from($initConfig) !!};

            function registerMessages() {
                if (!window.ConsentMessage) return;
                var strings = config.messageStrings || {};

                document.querySelectorAll('.consent-message--wrapper[data-consent]').forEach(function (el) {
                    if (el.dataset.ccBound) return;
                    el.dataset.ccBound = '1';

                    var consent = el.getAttribute('data-consent');
                    var srcName = el.getAttribute('data-src-name') || null;
                    var tpl = el.querySelector('template.consent-content');
                    var cb;
                    if (tpl) {
                        cb = function () {
                            el.appendChild(tpl.content.cloneNode(true));
                            tpl.remove();
                        };
                    }

                    // If the overlay is already server-rendered (Blade component),
                    // just wire it (template:false). Otherwise (e.g. a RichEditor
                    // iframe) let the runtime generate a localised overlay.
                    var opts = el.querySelector('.consent-message')
                        ? { template: false }
                        : {
                            template: {
                                strings: { buttonLabel: strings.button || 'OK', message: strings.text || '' },
                                // .btn classes: the injected overlay button adopts the host app's button component
                                main: '<div class="consent-message"><button class="confirm btn btn-primary">{buttonLabel}</button><p>{message}</p></div>',
                            },
                        };

                    window.ConsentMessage.new(consent, el, opts, srcName, cb);
                });
            }

            function syncGates() {
                if (!window.getConsentControlCookie) return;
                document.querySelectorAll('.consent-gate[data-consent]').forEach(function (el) {
                    el.style.display = window.getConsentControlCookie(el.getAttribute('data-consent')) ? '' : 'none';
                });
            }

            function boot() {
                if (!window.ConsentControl) return;
                window.ConsentControl.init(config);
                registerMessages();
                syncGates();
            }

            window.addEventListener('consent-updated', syncGates);

            if (document.readyState !== 'loading') {
                boot();
            } else {
                document.addEventListener('DOMContentLoaded', boot);
            }
        })();
    </script>
@endonce
