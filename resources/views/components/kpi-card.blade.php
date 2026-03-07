{{--
    x-kpi-card — Tessera KPI con gradiente colorato (stile Risorse Umane)
    Props:
      - value: numero/stringa da mostrare grande
      - label: etichetta sotto il numero
      - color: 'green' | 'blue' | 'orange' | 'purple' | 'red' | 'teal' | 'indigo'
      - cols: classi col (default "col-6 col-md-3")
--}}
@props([
    'value'  => 0,
    'label'  => '',
    'color'  => 'blue',
    'cols'   => 'col-6 col-md-3',
])

@php
$gradients = [
    'green'  => 'linear-gradient(135deg, #2e7d32, #43a047)',
    'blue'   => 'linear-gradient(135deg, #1565c0, #1e88e5)',
    'orange' => 'linear-gradient(135deg, #e65100, #fb8c00)',
    'purple' => 'linear-gradient(135deg, #6a1b9a, #8e24aa)',
    'red'    => 'linear-gradient(135deg, #c62828, #e53935)',
    'teal'   => 'linear-gradient(135deg, #00695c, #00897b)',
    'indigo' => 'linear-gradient(135deg, #283593, #3949ab)',
    'cyan'   => 'linear-gradient(135deg, #006064, #0097a7)',
];
$bg = $gradients[$color] ?? $gradients['blue'];
@endphp

<div class="{{ $cols }}">
    <div class="card border-0 shadow-sm h-100" style="border-radius:12px; overflow:hidden;">
        <div class="p-4 text-white h-100" style="background: {{ $bg }};">
            <div style="font-size:2em; font-weight:700; line-height:1.1;">{{ $value }}</div>
            <div style="font-size:0.85em; opacity:0.88; margin-top:4px; text-transform:uppercase; letter-spacing:0.03em;">{{ $label }}</div>
        </div>
    </div>
</div>
