<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\AdminLoginPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(Request $request): View
    {
        $this->ensurePathMatches($request);

        return view('admin.auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $this->ensurePathMatches($request);

        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function ensurePathMatches(Request $request): void
    {
        abort_unless(
            $request->route('adminLoginPath') === AdminLoginPath::segment(),
            404
        );
    }
}
