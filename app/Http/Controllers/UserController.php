<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('role')->orderBy('username')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);
        $validated['password'] = Hash::make($validated['password']);

        DB::transaction(function () use ($request, $validated, $auditLogger): void {
            $user = User::create($validated);
            $auditLogger->record(
                $request->user(),
                'account.created',
                "Created {$user->role->name} account {$user->username}.",
                $user,
                ['username' => $user->username, 'role' => $user->role->name],
            );
        });

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function update(Request $request, User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:10', 'confirmed'],
        ]);

        $newRole = Role::findOrFail($validated['role_id']);
        if ($user->hasRole('admin') && $newRole->name !== 'admin' && User::where('role_id', $user->role_id)->count() === 1) {
            return back()->withErrors(['role_id' => 'The last administrator cannot be demoted.']);
        }

        DB::transaction(function () use ($request, $user, $newRole, $validated, $auditLogger): void {
            $before = ['username' => $user->username, 'role' => $user->role->name];
            $passwordWasReset = ! empty($validated['password']);

            $user->username = $validated['username'];
            $user->role_id = $newRole->id;
            if ($passwordWasReset) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $after = ['username' => $user->username, 'role' => $newRole->name];
            if ($before !== $after) {
                $auditLogger->record(
                    $request->user(),
                    'account.updated',
                    "Updated account {$user->username}.",
                    $user,
                    ['before' => $before, 'after' => $after],
                );
            }

            if ($passwordWasReset) {
                $auditLogger->record(
                    $request->user(),
                    'account.password_reset',
                    "Reset the password for {$user->username}.",
                    $user,
                    ['username' => $user->username],
                );
            }
        });

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user, Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->hasRole('admin') && User::where('role_id', $user->role_id)->count() === 1) {
            return back()->withErrors(['user' => 'The last administrator cannot be deleted.']);
        }

        if ($user->sales()->exists()) {
            return back()->withErrors(['user' => 'Users with sales records cannot be deleted.']);
        }

        DB::transaction(function () use ($user, $request, $auditLogger): void {
            $auditLogger->record(
                $request->user(),
                'account.deleted',
                "Deleted account {$user->username}.",
                $user,
                ['username' => $user->username, 'role' => $user->role->name],
            );
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
