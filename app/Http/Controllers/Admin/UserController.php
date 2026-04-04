<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return view('admin.users.index', [
            'users' => $query->paginate(15)->withQueryString(),
            'totalCount' => User::count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => config('cms.roles', []),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', __('admin.user_created'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => config('cms.roles', []),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->is($user) && ($data['role'] ?? null) !== 'admin') {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('admin.cannot_demote_self'));
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', __('admin.user_updated'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('admin.cannot_delete_self'));
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('admin.user_deleted'));
    }
}
