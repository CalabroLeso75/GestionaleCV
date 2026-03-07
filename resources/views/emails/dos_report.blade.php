<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Incendio - {{ now()->format('d/m/Y H:i') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fc; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-top: 5px solid #e74a3b; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-radius: 5px; }
        .header { text-align: center; border-bottom: 1px solid #eaecf4; padding-bottom: 15px; margin-bottom: 20px; }
        h2 { color: #e74a3b; margin-top: 0; }
        .data-box { background: #f8f9fc; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .data-row { margin-bottom: 10px; font-size: 15px; }
        .data-label { font-weight: bold; color: #5a5c69; display: inline-block; width: 140px; }
        .btn-maps { display: block; width: 100%; text-align: center; background: #4e73df; color: white; text-decoration: none; padding: 15px 0; border-radius: 50px; font-size: 16px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; font-size: 12px; color: #858796; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔥 Incendio {{ $report->municipality ?? 'Ignoto' }} {{ $report->province ? '(' . $report->province . ')' : '' }}<br><small style="color: #858796; font-size: 0.8em;">{{ $report->toponym ?? 'Toponimo non specificato' }}</small></h2>
            <p><strong>{{ $report->role_snapshot }} Operante:</strong> {{ $user->name ?? 'Nome' }} {{ $user->surname ?? 'Cognome' }}</p>
            <p><strong>Orario Rilevazione:</strong> {{ $report->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        <div class="data-box">
            <div class="data-row"><span class="data-label">📡 Coord. Incendio:</span> <span style="color:#e74a3b; font-weight:bold;">{{ $report->fire_lat ?? 'N/D' }} , {{ $report->fire_lng ?? 'N/D' }}</span></div>
            @if(!empty($report->municipality) || !empty($report->province))
            <div class="data-row"><span class="data-label">📍 Località:</span> <strong>{{ $report->municipality ?? 'N/D' }}</strong> @if(!empty($report->province)) ({{ $report->province }}) @endif</div>
            @endif
            <div class="data-row"><span class="data-label">👤 Coord. Op.:</span> {{ $report->op_lat ?? 'N/D' }} , {{ $report->op_lng ?? 'N/D' }}</div>
            <div class="data-row"><span class="data-label">📏 Distanza Stimata:</span> {{ $report->distance ?? 'N/D' }} metri</div>
        </div>

        <div class="data-box" style="border-left: 4px solid #f6c23e;">
            <h4 style="margin-top:0; color:#5a5c69;">🌤️ Condizioni Meteo sul Punto Fuoco:</h4>
            <div class="data-row"><span class="data-label">Temperatura:</span> {{ $report->temperature ?? 'N/D' }} °C</div>
            <div class="data-row"><span class="data-label">Vento:</span> {{ $report->wind_speed ?? 'N/D' }} km/h (Dir: {{ $report->wind_direction ?? 'N/D' }})</div>
            @if($report->wind_forecast_2h_speed || $report->wind_forecast_4h_speed || $report->wind_forecast_6h_speed)
            <hr style="border: 0; border-top: 1px solid #eaecf4; margin: 10px 0;">
            <div style="font-size: 13px; color: #666;">
                <strong>Previsioni Prossime Ore:</strong><br><br>
                <strong>+2h:</strong> Vento {{ $report->wind_forecast_2h_speed ?? '-' }} km/h | Raffiche: {{ $report->wind_forecast_2h_gust ?? '-' }} | Dir: {{ $report->wind_forecast_2h_dir ?? '-' }}<br>
                <strong>+4h:</strong> Vento {{ $report->wind_forecast_4h_speed ?? '-' }} km/h | Raffiche: {{ $report->wind_forecast_4h_gust ?? '-' }} | Dir: {{ $report->wind_forecast_4h_dir ?? '-' }}<br>
                <strong>+6h:</strong> Vento {{ $report->wind_forecast_6h_speed ?? '-' }} km/h | Raffiche: {{ $report->wind_forecast_6h_gust ?? '-' }} | Dir: {{ $report->wind_forecast_6h_dir ?? '-' }}
            </div>
            @endif
        </div>

        @if($report->area_hectares || $report->front_meters)
        <div class="data-box" style="border-left: 4px solid #1cc88a;">
            <h4 style="margin-top:0; color:#5a5c69;">📐 Dati Rilevamento GIS:</h4>
            @if($report->area_hectares)
                <div class="data-row"><span class="data-label">Area Stimata:</span> ~{{ $report->area_hectares }} ettari</div>
            @endif
            @if($report->front_meters)
                <div class="data-row"><span class="data-label">Fronte Fiamma (circa):</span> {{ $report->front_meters }} metri</div>
            @endif
        </div>
        @endif

        @if(!empty($data['nearest_toponyms_json']))
        @php
            $toponyms = json_decode($data['nearest_toponyms_json'], true);
        @endphp
        @if(is_array($toponyms) && count($toponyms) > 0)
        <div class="data-box" style="border-left: 4px solid #36b9cc;">
            <h4 style="margin-top:0; color:#5a5c69;">📍 Toponimi Vicini:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach(array_slice($toponyms, 0, 5) as $toponym)
                    <li>{{ $toponym['name'] }} ({{ number_format($toponym['distanza'], 2) }} km)</li>
                @endforeach
            </ul>
        </div>
        @endif
        @endif

        @if(!empty($report->notes))
        <div class="data-box" style="background:#eaecf4;">
            <h4 style="margin-top:0; color:#5a5c69;">📝 Note Operative:</h4>
            <p style="white-space: pre-wrap; font-style:italic;">{{ $report->notes }}</p>
        </div>
        @endif

        <a href="https://maps.google.com/?q={{ $report->fire_lat ?? '' }},{{ $report->fire_lng ?? '' }}" class="btn-maps" target="_blank">
            Apri Posizione su Google Maps
        </a>

        <div class="footer">
            <p>Report generato automaticamente da Calabria Verde (Strumenti DOS).</p>
        </div>
    </div>
</body>
</html>
