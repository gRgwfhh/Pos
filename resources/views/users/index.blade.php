@extends('layouts.app')

@section('title', 'User management')

@section('content')
    <div class="page-head"><div><h1>User management</h1><div class="muted">Create cashier accounts and manage access.</div></div></div>
    <section class="panel" style="margin-bottom:20px">
        <h2 style="margin-top:0;font-size:19px">Add user</h2>
        <form method="POST" action="{{ route('users.store') }}">@csrf
            <div class="form-grid">
                <div class="field"><label for="new-username">Username</label><input id="new-username" name="username" required value="{{ old('username') }}"></div>
                <div class="field"><label for="new-role">Role</label><select id="new-role" name="role_id" required>@foreach($roles as $role)<option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>@endforeach</select></div>
                <div class="field"><label for="new-password">Password (10+ characters)</label><input id="new-password" type="password" name="password" required minlength="10" autocomplete="new-password"></div>
                <div class="field"><label for="new-password-confirmation">Confirm password</label><input id="new-password-confirmation" type="password" name="password_confirmation" required minlength="10" autocomplete="new-password"></div>
            </div>
            <button type="submit">Create user</button>
        </form>
    </section>
    <section class="panel table-wrap">
        <table><thead><tr><th>Username</th><th>Role</th><th>Update account</th><th>Delete</th></tr></thead><tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->username }} @if($user->is(auth()->user()))<span class="muted">(you)</span>@endif</td>
                <td>{{ ucfirst($user->role->name) }}</td>
                <td>
                    <form class="inline-form" method="POST" action="{{ route('users.update', $user) }}">@csrf @method('PUT')
                        <input aria-label="Username" name="username" value="{{ $user->username }}" required>
                        <select aria-label="Role" name="role_id">@foreach($roles as $role)<option value="{{ $role->id }}" @selected($role->id === $user->role_id)>{{ ucfirst($role->name) }}</option>@endforeach</select>
                        <input aria-label="New password (optional)" type="password" name="password" placeholder="New password" minlength="10" autocomplete="new-password">
                        <input aria-label="Confirm new password" type="password" name="password_confirmation" placeholder="Confirm password" minlength="10" autocomplete="new-password">
                        <button type="submit">Save</button>
                    </form>
                </td>
                <td><form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE')<button class="danger" type="submit" @disabled($user->is(auth()->user()))>Delete</button></form></td>
            </tr>
        @endforeach
        </tbody></table>
    </section>
@endsection
