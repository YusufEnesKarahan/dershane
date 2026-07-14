<?php

function createClass($path, $content) {
    $dir = dirname(__DIR__ . '/' . $path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(__DIR__ . '/' . $path, $content);
}

// Form Requests
$requests = [
    'LoginRequest' => "<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts(\$this->throttleKey(), config('security.login.max_attempts', 5))) {
            return;
        }

        \$seconds = RateLimiter::availableIn(\$this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => \$seconds,
                'minutes' => ceil(\$seconds / 60),
            ]),
        ]);
    }

    public function hitRateLimiter(): void
    {
        RateLimiter::hit(\$this->throttleKey(), config('security.login.decay_minutes', 1) * 60);
    }

    public function clearRateLimiter(): void
    {
        RateLimiter::clear(\$this->throttleKey());
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower(\$this->input('email')).'|'.\$this->ip());
    }
}
",
    'ForgotPasswordRequest' => "<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }
}
",
    'ResetPasswordRequest' => "<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        \$passwordRule = Password::min(config('security.password.min_length', 8));
        if (config('security.password.require_uppercase')) \$passwordRule->mixedCase();
        if (config('security.password.require_numbers')) \$passwordRule->numbers();
        if (config('security.password.require_symbols')) \$passwordRule->symbols();
        if (config('security.password.uncompromised')) \$passwordRule->uncompromised();

        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', \$passwordRule],
        ];
    }
}
",
    'ChangePasswordRequest' => "<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        \$passwordRule = Password::min(config('security.password.min_length', 8));
        if (config('security.password.require_uppercase')) \$passwordRule->mixedCase();
        if (config('security.password.require_numbers')) \$passwordRule->numbers();
        if (config('security.password.require_symbols')) \$passwordRule->symbols();

        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \$passwordRule],
        ];
    }
}
"
];

foreach ($requests as $name => $content) {
    createClass('app/Http/Requests/Auth/' . $name . '.php', $content);
}

// Controllers
$controllers = [
    'LoginController' => "<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Domain\Auth\Actions\LoginAction;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest \$request, LoginAction \$action)
    {
        \$request->ensureIsNotRateLimited();

        if (\$action->execute(\$request->only('email', 'password'), \$request->boolean('remember'))) {
            \$request->clearRateLimiter();
            return redirect()->intended(route('dashboard'));
        }

        \$request->hitRateLimiter();

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }
}
",
    'LogoutController' => "<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Actions\LogoutAction;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function destroy(Request \$request, LogoutAction \$action)
    {
        \$action->execute();
        return redirect('/');
    }
}
",
    'ForgotPasswordController' => "<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Domain\Auth\Actions\ForgotPasswordAction;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest \$request, ForgotPasswordAction \$action)
    {
        \$status = \$action->execute(\$request->only('email'));

        if (\$status == Password::RESET_LINK_SENT) {
            return back()->with('status', __(\$status));
        }

        return back()->withInput(\$request->only('email'))
                     ->withErrors(['email' => __(\$status)]);
    }
}
",
    'ResetPasswordController' => "<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Domain\Auth\Actions\ResetPasswordAction;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function create(Request \$request)
    {
        return view('auth.reset-password', ['request' => \$request]);
    }

    public function store(ResetPasswordRequest \$request, ResetPasswordAction \$action)
    {
        \$status = \$action->execute(\$request->only('email', 'password', 'password_confirmation', 'token'));

        if (\$status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __(\$status));
        }

        return back()->withInput(\$request->only('email'))
                     ->withErrors(['email' => __(\$status)]);
    }
}
",
    'ProfileController' => "<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function dashboard()
    {
        return view('auth.dashboard-placeholder');
    }
}
"
];

foreach ($controllers as $name => $content) {
    createClass('app/Http/Controllers/Auth/' . $name . '.php', $content);
}

// auth.php Routes
createClass('routes/auth.php', "<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ProfileController;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'destroy'])->name('logout');
    
    // Placeholder dashboard for redirect
    Route::get('dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
});
");

echo "Auth HTTP layer created.\n";
