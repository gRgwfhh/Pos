<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | Grain &amp; Feed POS</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f7f4;color:#21332d;font-family:system-ui,-apple-system,"Segoe UI",sans-serif}.wrap{min-height:100vh;display:grid;place-items:center;padding:20px}.card{width:min(420px,100%);padding:30px;background:white;border:1px solid #e1e9e3;border-radius:14px;box-shadow:0 10px 36px #183d2d12}h1{margin:0 0 6px}p{color:#718078;margin:0 0 22px}.field{display:flex;flex-direction:column;gap:6px;margin-bottom:15px}label{font-weight:650;font-size:14px}input{font:inherit;padding:11px;border:1px solid #ccd8cf;border-radius:7px;width:100%}button{width:100%;border:0;border-radius:8px;background:#176b4a;color:white;padding:12px;font:inherit;font-weight:700;cursor:pointer}.error{color:#8b2f28;background:#fbe9e6;border-radius:7px;padding:11px;margin-bottom:14px}
    </style>
</head>
<body><main class="wrap"><section class="card">
    <h1>Grain &amp; Feed POS</h1><p>Sign in to continue to the store.</p>
    @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('login.store') }}">@csrf
        <div class="field"><label for="username">Username</label><input id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"></div>
        <div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
        <div class="field"><label><input type="checkbox" name="remember" value="1" style="width:auto"> Remember me</label></div>
        <button type="submit">Sign in</button>
    </form>
</section></main></body></html>
