<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.profile.index', [
            'user' => $request->user(),
            'newApiKey' => session('new_mcp_api_key'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.profile.index')
            ->with('success', __('admin.profile_updated'));
    }

    public function generateMcpApiKey(Request $request): RedirectResponse
    {
        $plainApiKey = $request->user()->generateMcpApiKey();

        return redirect()
            ->route('admin.profile.index')
            ->with('success', __('admin.mcp_api_key_generated'))
            ->with('new_mcp_api_key', $plainApiKey);
    }

    public function revokeMcpApiKey(Request $request): RedirectResponse
    {
        $request->user()->revokeMcpApiKey();

        return redirect()
            ->route('admin.profile.index')
            ->with('success', __('admin.mcp_api_key_revoked'));
    }
}
