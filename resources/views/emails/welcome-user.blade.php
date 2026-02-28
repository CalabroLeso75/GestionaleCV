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
        .body { padding: 30px; color: #333; }
        .body h2 { color: #003366; font-size: 18px; margin-top: 0; }
        .credentials { background: #f0f4ff; border: 1px solid #d0daf0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .credentials table { width: 100%; border-collapse: collapse; }
        .credentials td { padding: 8px 0; font-size: 14px; }
        .credentials td:first-child { font-weight: 600; color: #555; width: 120px; }
        .credentials .pwd { font-family: monospace; font-size: 16px; color: #d32f2f; font-weight: bold; letter-spacing: 1px; }
        .roles-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .roles-table th { background: #003366; color: white; padding: 10px 12px; text-align: left; font-size: 13px; }
        .roles-table td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 13px; }
        .roles-table tr:nth-child(even) { background: #f9fbff; }
        .admin-info { background: #e8f5e9; border-radius: 6px; padding: 12px 16px; margin: 15px 0; font-size: 13px; color: #2e7d32; }
        .btn-login { display: inline-block; background: #005a9e; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 15px 0; }
        .footer { background: #f5f7fa; padding: 20px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eee; }
        .warning { background: #fff3e0; border: 1px solid #ffe0b2; border-radius: 6px; padding: 12px 16px; margin: 15px 0; font-size: 13px; color: #e65100; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestionale Calabria Verde</h1>
            <p>Sistema Informativo Aziendale</p>
        </div>
        <div class="body">
            <h2>Benvenuto {{ $userName }} {{ $userSurname }}!</h2>
            <p>Il tuo account nel Gestionale Calabria Verde è stato creato e attivato. Di seguito trovi le credenziali di accesso e i ruoli assegnati.</p>

            <div class="credentials">
                <table>
                    <tr>
                        <td>Email:</td>
                        <td><strong>{{ $email }}</strong></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td class="pwd">{{ $password }}</td>
                    </tr>
                </table>
            </div>

            <div class="warning">
                ⚠️ <strong>Importante:</strong> Ti consigliamo di cambiare la password al primo accesso per motivi di sicurezza.
            </div>

            <h3 style="color: #003366; font-size: 15px;">Ruoli e Aree assegnati</h3>
            <table class="roles-table">
                <thead>
                    <tr>
                        <th>Ruolo</th>
                        <th>Area di Competenza</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($areaRoles as $ar)
                    <tr>
                        <td>{{ ucfirst($ar['role']) }}</td>
                        <td>{{ $ar['area'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="admin-info">
                ✅ Account abilitato da: <strong>{{ $adminName }}</strong>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn-login">Accedi al Gestionale</a>
            </div>
        </div>
        <div class="footer">
            <p>Questa email è stata generata automaticamente dal Gestionale Calabria Verde.<br>
            Per assistenza, contatta l'amministratore di sistema.</p>
        </div>
    </div>
</body>
</html>
