{{--
    x-back-button — Pulsante "Torna indietro" standard per tutte le pagine
    Props:
      - url:   URL esplicito (opzionale, default JavaScript:history.back())
      - label: testo del pulsante (default "← Torna indietro")
--}}
@props([
    'url'   => null,
    'label' => '← Torna indietro',
])

<div class="mt-4 pt-2 border-top">
    @if($url)
        <a href="{{ $url }}" class="btn btn-outline-secondary">{{ $label }}</a>
    @else
        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">{{ $label }}</button>
    @endif
</div>
