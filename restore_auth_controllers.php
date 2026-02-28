<?php

$baseDir = __DIR__ . '/app/Http/Controllers/Auth';

if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
    echo "Created directory: $baseDir<br>";
}

$controllers = [
    'AuthenticatedSessionController.php' => <<<'PHP'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
PHP,
    'RegisteredUserController.php' => <<<'PHP'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
PHP,
    'PasswordResetLinkController.php' => <<<'PHP'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
PHP,
    // Add stub for others to prevent crashing, even if empty logic for now
    'NewPasswordController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
class NewPasswordController extends Controller {
    public function create() {}
    public function store() {}
}
PHP,
    'EmailVerificationPromptController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
class EmailVerificationPromptController extends Controller {
    public function __invoke(Request $request) {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
PHP,
    'VerifyEmailController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
class VerifyEmailController extends Controller {
    public function __invoke() {}
}
PHP,
    'EmailVerificationNotificationController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
class EmailVerificationNotificationController extends Controller {
    public function store() {}
}
PHP,
    'ConfirmablePasswordController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
class ConfirmablePasswordController extends Controller {
    public function show() {}
    public function store() {}
}
PHP,
    'PasswordController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
class PasswordController extends Controller {
    public function update() {}
}
PHP,
];

foreach ($controllers as $name => $content) {
    file_put_contents("$baseDir/$name", $content);
    echo "Created: $name<br>";
}

echo "<h1>Controllers Restored!</h1>";
