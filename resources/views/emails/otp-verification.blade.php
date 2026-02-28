<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codice OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #0066cc;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 30px;
        }
        .otp-code {
            background-color: #f0f4ff;
            border: 2px dashed #0066cc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-code span {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 6px;
            color: #0066cc;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px 15px;
            margin: 15px 0;
            font-size: 13px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌲 Gestionale Calabria Verde</h1>
            <p style="margin: 5px 0 0; font-size: 14px;">Verifica Email - Codice OTP</p>
        </div>
        
        <div class="content">
            <p>Gentile <strong>{{ $userName }}</strong>,</p>
            
            <p>Grazie per la registrazione al Gestionale Calabria Verde. Per completare la verifica del tuo indirizzo email, inserisci il seguente codice OTP:</p>
            
            <div class="otp-code">
                <span>{{ $otpCode }}</span>
            </div>
            
            <div class="warning">
                ⚠️ <strong>Attenzione:</strong> Questo codice è valido per <strong>15 minuti</strong>. 
                Non condividere questo codice con nessuno.
            </div>
            
            <p>Se non hai richiesto questa registrazione, puoi ignorare questa email.</p>
            
            <p>Dopo la verifica dell'email, il tuo account dovrà essere approvato da un amministratore prima di poter accedere al sistema.</p>
        </div>
        
        <div class="footer">
            <p>Questa email è stata generata automaticamente dal Gestionale Calabria Verde.</p>
            <p>© {{ date('Y') }} Calabria Verde - Tutti i diritti riservati</p>
        </div>
    </div>
</body>
</html>
