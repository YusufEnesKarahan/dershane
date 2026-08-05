<?php
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

    public function store(LoginRequest $request, LoginAction $action)
    {
        $request->ensureIsNotRateLimited();

        if ($action->execute($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->clearRateLimiter();
            
            $user = auth()->user();
            if ($user->hasRole(['Super Admin', 'Admin', 'Branch Admin'])) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->hasRole('Teacher')) {
                return redirect()->intended(route('teacher.dashboard'));
            } elseif ($user->hasRole('Student')) {
                return redirect()->intended(route('student.dashboard'));
            } elseif ($user->hasRole('Parent')) {
                return redirect()->intended(route('parent.dashboard'));
            }

            return redirect()->intended(route('tenant.dashboard'));
        }

        $request->hitRateLimiter();

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }
}
