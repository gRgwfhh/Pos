<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Grain & Feed POS') | Grain & Feed</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#21332d;background:#f4f7f4;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0}a{color:#176b4a;text-decoration:none}button,.button{border:0;border-radius:8px;background:#176b4a;color:#fff;padding:10px 15px;font:inherit;font-weight:650;cursor:pointer;display:inline-block}button:hover,.button:hover{background:#12553b;color:white}.button.secondary{background:#e6eee9;color:#23483a}.button.danger,button.danger{background:#a53d36}.topbar{background:#153d2d;color:white;padding:14px max(22px,calc((100% - 1180px)/2));display:flex;align-items:center;justify-content:flex-start;gap:20px}.menu-toggle{background:#345a49;font-size:16px}.navigation-drawer{position:fixed;inset:0 auto 0 0;width:min(320px,calc(100vw - 48px));height:100vh;height:100dvh;max-height:none;margin:0;padding:20px;border:0;background:#153d2d;color:#fff;box-shadow:8px 0 32px #0003}.navigation-drawer[open]{display:flex;flex-direction:column;gap:22px}.navigation-drawer::backdrop{background:#0008}.drawer-heading{display:flex;align-items:center;justify-content:space-between;gap:12px}.brand{font-size:19px;font-weight:800;color:white}.drawer-close{background:#345a49;padding:8px 12px}.nav{display:flex;flex-direction:column;gap:6px;align-items:stretch}.nav a{display:block;color:#e4eee8;padding:11px 12px;border-radius:7px}.nav a:hover{background:#ffffff18}.drawer-account{margin-top:auto;display:grid;gap:12px;color:#e4eee8}.drawer-account form{margin:0}.drawer-account button{background:#345a49}.container{max-width:1180px;margin:30px auto;padding:0 20px}.page-head{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:20px}.page-head h1{margin:0;font-size:27px}.panel,.stat{background:white;border:1px solid #e1e9e3;border-radius:12px;padding:20px;box-shadow:0 4px 16px #183d2d0b}.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:15px;margin-bottom:20px}.stat small{color:#718078}.stat strong{display:block;font-size:25px;margin-top:8px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}.product-card{border:1px solid #e2eae4;border-radius:10px;padding:15px;background:#fff}.product-card h3{margin:0 0 7px}.muted{color:#718078;font-size:14px}.price{font-weight:750;margin:10px 0}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;background:white}th,td{text-align:left;padding:12px 10px;border-bottom:1px solid #e8eee9;vertical-align:middle}th{font-size:13px;color:#607168;text-transform:uppercase;letter-spacing:.04em}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.field{display:flex;flex-direction:column;gap:6px;margin-bottom:13px}.field label{font-weight:650;font-size:14px}.field input,.field select{font:inherit;padding:10px;border:1px solid #ccd8cf;border-radius:7px;width:100%;background:#fff}.actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.alert{border-radius:8px;padding:12px 15px;margin-bottom:15px}.alert.success{background:#e0f2e7;color:#195c37}.alert.error{background:#fbe9e6;color:#8b2f28}.alert ul{margin:5px 0;padding-left:22px}.split{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(300px,.8fr);gap:20px}.cart-line{display:grid;grid-template-columns:1fr 90px auto;gap:8px;align-items:center;padding:11px 0;border-bottom:1px solid #e8eee9}.cart-line input{width:85px;padding:7px;border:1px solid #ccd8cf;border-radius:6px}.total{display:flex;justify-content:space-between;font-size:20px;font-weight:800;padding:18px 0}.login-wrap{min-height:100vh;display:grid;place-items:center;padding:20px}.login-card{width:min(420px,100%);background:#fff;border:1px solid #e1e9e3;border-radius:14px;padding:30px;box-shadow:0 10px 36px #183d2d12}.login-card h1{margin:0 0 6px}.pagination{margin-top:16px}.pagination nav{display:flex;gap:8px}.inline-form{display:flex;gap:6px;align-items:center}.inline-form select,.inline-form input{padding:7px;border:1px solid #ccd8cf;border-radius:6px}.inline-form input{width:120px}.low{color:#a53d36;font-weight:700}@media(max-width:780px){.split{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}.page-head{align-items:flex-start;flex-direction:column}}
    </style>
    <style>
        .topbar{display:none}
        #navbar{position:fixed;inset:0 auto 0 0;z-index:1000;width:72px;overflow-x:hidden;background:#153d2d;color:#fff;transition:width .25s ease}
        #navbar:hover,#navbar:focus-within{width:260px;overflow-y:auto}
        #navbar .navbar-items{height:100%;display:flex;flex-direction:column;align-items:stretch;gap:4px;margin:0;padding:10px 0;list-style:none}
        #navbar .navbar-logo{height:64px;display:flex;align-items:center;gap:14px;margin:0 0 16px;padding:0 12px;white-space:nowrap}
        #navbar .brand-mark{width:46px;height:46px;flex:0 0 46px;display:grid;place-items:center;border-radius:10px;background:#345a49;color:white;font-size:12px;font-weight:800}
        #navbar .brand-name{width:0;overflow:hidden;opacity:0;white-space:nowrap;transition:opacity .2s ease}
        #navbar:hover .brand-name,#navbar:focus-within .brand-name{width:auto;opacity:1}
        #navbar .navbar-item{width:100%;padding:0 10px;list-style:none}
        #navbar .navbar-item-inner{width:100%;min-height:48px;display:flex;align-items:center;gap:14px;padding:0 2px;border-radius:8px;color:#e4eee8;text-decoration:none;white-space:nowrap}
        #navbar .navbar-item-inner:hover,#navbar .navbar-item-inner:focus-visible{background:#ffffff18;color:#fff;outline:none}
        #navbar .navbar-item-inner-icon-wrapper{width:48px;height:48px;flex:0 0 48px;display:grid;place-items:center}
        #navbar .navbar-item-inner-icon-wrapper svg{width:21px;height:21px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        #navbar .link-text{width:0;overflow:hidden;opacity:0;white-space:nowrap;transition:width .25s ease,opacity .2s ease}
        #navbar:hover .link-text,#navbar:focus-within .link-text{width:calc(100% - 62px);opacity:1}
        #navbar .navbar-account{margin-top:0}
        #navbar .navbar-account:nth-last-child(2){margin-top:auto}
        #navbar .navbar-account .navbar-item-inner{border:0;background:transparent;font:inherit;text-align:left;cursor:pointer}
        #navbar .navbar-close{display:none}
        .sidenav-backdrop{display:none}
        .container{width:min(1180px,calc(100% - 112px));margin:30px auto 30px calc(72px + max(20px,(100% - 1252px)/2))}
        @media(max-width:780px){
            .topbar{height:58px;position:sticky;top:0;z-index:900;display:flex;align-items:center;justify-content:flex-start;padding:8px 16px;background:#153d2d}
            .menu-toggle{background:#345a49}
            #navbar{width:min(280px,85vw);transform:translateX(-100%);transition:transform .25s ease}
            #navbar.is-open{transform:translateX(0);overflow-y:auto}
            #navbar .brand-name,#navbar .link-text{width:auto;opacity:1}
            #navbar .navbar-close{display:inline-flex;margin-left:auto;padding:8px 10px;background:#345a49}
            .sidenav-backdrop:not([hidden]){position:fixed;inset:0;z-index:950;display:block;border:0;border-radius:0;background:#0008}
            body.nav-open{overflow:hidden}
            .container{width:auto;max-width:none;margin:22px 20px;padding:0}
        }
        @media(prefers-reduced-motion:reduce){
            #navbar,#navbar .link-text,#navbar .brand-name{transition:none}
        }
    </style>
    @stack('head')
</head>
<body>
<header class="topbar">
    <button class="menu-toggle" id="navigation-toggle" type="button" aria-controls="navbar" aria-expanded="false">
        <span aria-hidden="true">☰</span> Menu
    </button>
</header>
<button class="sidenav-backdrop" id="navigation-backdrop" type="button" aria-label="Close navigation" hidden></button>
<aside id="navbar" aria-label="Main navigation" aria-hidden="false">
    <ul class="navbar-items">
        <li class="navbar-logo">
            <a class="brand-mark" href="{{ route('dashboard') }}" aria-label="Grain and Feed dashboard">G&amp;F</a>
            <span class="brand-name">Grain &amp; Feed POS</span>
            <button class="navbar-close" id="navigation-close" type="button" aria-label="Close navigation">Close</button>
        </li>
        <li class="navbar-item">
            <a class="navbar-item-inner" href="{{ route('dashboard') }}">
                <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1z"/></svg></span>
                <span class="link-text">Dashboard</span>
            </a>
        </li>
        <li class="navbar-item">
            <a class="navbar-item-inner" href="{{ route('pos.index') }}">
                <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h4"/></svg></span>
                <span class="link-text">Point of Sale</span>
            </a>
        </li>
        <li class="navbar-item">
            <a class="navbar-item-inner" href="{{ route('products.index') }}">
                <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="m3 7 9-4 9 4-9 4zM3 7v10l9 4 9-4V7M12 11v10"/></svg></span>
                <span class="link-text">Inventory</span>
            </a>
        </li>
        @if(auth()->user()->hasRole('admin'))
            <li class="navbar-item">
                <a class="navbar-item-inner" href="{{ route('sales.index') }}">
                    <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2zM9 8h6M9 12h6"/></svg></span>
                    <span class="link-text">Sales</span>
                </a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-inner" href="{{ route('audit-logs.index') }}">
                    <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M6 3h9l4 4v14H6zM14 3v5h5M9 12h7M9 16h7"/></svg></span>
                    <span class="link-text">Audit log</span>
                </a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-inner" href="{{ route('users.index') }}">
                    <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2M17 4a4 4 0 0 1 0 8M18 14a6 6 0 0 1 4 5v2"/></svg></span>
                    <span class="link-text">Users</span>
                </a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-inner" href="{{ route('sack-sizes.index') }}">
                    <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M8 4h8l-1 4 4 3v10H5V11l4-3zM9 8h6M9 14h6"/></svg></span>
                    <span class="link-text">Sack sizes</span>
                </a>
            </li>
        @endif
        <li class="navbar-item navbar-account">
            <span class="navbar-item-inner" title="{{ auth()->user()->username }} ({{ auth()->user()->role->name }})">
                <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span>
                <span class="link-text">{{ auth()->user()->username }} ({{ auth()->user()->role->name }})</span>
            </span>
        </li>
        <li class="navbar-item navbar-account">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="navbar-item-inner" type="submit">
                    <span class="navbar-item-inner-icon-wrapper"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg></span>
                    <span class="link-text">Log out</span>
                </button>
            </form>
        </li>
    </ul>
</aside>
<main class="container">
    @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
@stack('scripts')
<script>
    const navigationSidebar = document.getElementById('navbar');
    const navigationToggle = document.getElementById('navigation-toggle');
    const navigationBackdrop = document.getElementById('navigation-backdrop');
    const mobileNavigation = window.matchMedia('(max-width: 780px)');

    function closeMobileNavigation(returnFocus = false) {
        navigationSidebar.classList.remove('is-open');
        navigationSidebar.setAttribute('aria-hidden', 'true');
        navigationSidebar.inert = true;
        navigationBackdrop.hidden = true;
        navigationToggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('nav-open');

        if (returnFocus) {
            navigationToggle.focus();
        }
    }

    function openMobileNavigation() {
        navigationSidebar.classList.add('is-open');
        navigationSidebar.setAttribute('aria-hidden', 'false');
        navigationSidebar.inert = false;
        navigationBackdrop.hidden = false;
        navigationToggle.setAttribute('aria-expanded', 'true');
        document.body.classList.add('nav-open');
        document.getElementById('navigation-close').focus();
    }

    navigationToggle.addEventListener('click', () => {
        if (navigationSidebar.classList.contains('is-open')) {
            closeMobileNavigation(true);
        } else {
            openMobileNavigation();
        }
    });

    document.getElementById('navigation-close').addEventListener('click', () => closeMobileNavigation(true));
    navigationBackdrop.addEventListener('click', () => closeMobileNavigation(true));
    navigationSidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (mobileNavigation.matches) {
                closeMobileNavigation();
            }
        });
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && navigationSidebar.classList.contains('is-open')) {
            closeMobileNavigation(true);
        }
    });
    mobileNavigation.addEventListener('change', event => {
        if (!event.matches) {
            navigationSidebar.classList.remove('is-open');
            navigationSidebar.setAttribute('aria-hidden', 'false');
            navigationSidebar.inert = false;
            navigationBackdrop.hidden = true;
            navigationToggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('nav-open');
        } else {
            closeMobileNavigation();
        }
    });

    if (mobileNavigation.matches) {
        closeMobileNavigation();
    }
</script>
</body>
</html>
