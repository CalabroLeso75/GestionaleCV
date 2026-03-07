<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #003366 0%, #005a9e 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.85; font-size: 14px; }
        .body { padding: 30px; color: #333; line-height: 1.6; }
        .body h2 { color: #003366; font-size: 18px; margin-top: 0; }
        .btn-action { display: inline-block; background: #d32f2f; color: white !important; padding: 14px 35px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 25px 0; text-align: center; box-shadow: 0 4px 6px rgba(211, 47, 47, 0.2); transition: all 0.3s ease; }
        .warning { background: #fff3e0; border: 1px solid #ffe0b2; border-radius: 6px; padding: 12px 16px; margin: 20px 0; font-size: 13px; color: #e65100; }
        .footer { background: #f5f7fa; padding: 20px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eee; }
        .text-center { text-align: center; }
        .meta-text { font-size: 12px; color: #666; word-break: break-all; margin-top: 20px; padding-top: 20px; border-top: 1px dashed #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Richiesta Reset Password</h1>
            <p>Gestionale Calabria Verde</p>
        </div>
        <div class="body">
            <h2>Ciao {{ $user->name ?? 'Utente' }},</h2>
            <p>Abbiamo ricevuto una richiesta per reimpostare la password associata al tuo account sul sistema informativo <strong>Gestionale Calabria Verde</strong>.</p>
            
            <div class="text-center">
                <a href="{{ $url }}" class="btn-action">Reimposta la tua Password</a>
            </div>

            <div class="warning">
                ⏱️ <strong>Attenzione:</strong> Questo link di sicurezza scadrà automaticamente dopo 60 minuti.
            </div>

            <p style="font-size: 14px; color: #555;">
                Se non hai effettuato tu questa richiesta, nessuna ulteriore azione è necessaria e il tuo account rimarrà al sicuro.
            </p>

            <div class="meta-text">
                Se hai problemi a cliccare il pulsante "Reimposta la tua Password", copia e incolla il seguente URL direttamente nel tuo browser web: <br>
                <a href="{{ $url }}" style="color: #005a9e;">{{ $url }}</a>
            </div>
        </div>
        <div class="footer">
            <p>Questa email è inviata in automatico dal sistema di sicurezza di Calabria Verde.<br>
            Non rispondere a questa casella di posta.</p>
        </div>
    </div>
</body>
</html>
