<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity to the database.
     *
     * @param string $action The action performed (e.g., 'login', 'create', 'update')
     * @param string|null $model The model name (e.g., 'ExternalEmployee')
     * @param int|null $modelId The ID of the model instance
     * @param string|null $details A clear description of the activity
     * @return void
     */
    public static function log(string $action, ?string $model = null, ?int $modelId = null, ?string $details = null)
    {
        $ip = Request::ip();
        if ($ip === '::1') $ip = '127.0.0.1';

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'details' => $details,
            'ip_address' => $ip,
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a login event.
     */
    public static function logLogin($user)
    {
        self::log('login', 'User', $user->id, "Accesso effettuato dall'utente {$user->email}");
    }

    /**
     * Log a logout event.
     */
    public static function logLogout($user)
    {
        if ($user) {
            self::log('logout', 'User', $user->id, "Disconnessione effettuata");
        }
    }

    /**
     * Log a security event (password change, role assignment).
     */
    public static function logSecurity(string $details)
    {
        self::log('security', null, null, $details);
    }
}
