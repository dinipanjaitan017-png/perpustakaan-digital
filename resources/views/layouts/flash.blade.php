@if (session('status') || session('error') || ($errors->any() && ! request()->routeIs('profile.edit', 'login', 'register', 'password.*')))
    <div class="container">
        @if (session('status') && ! request()->routeIs('login', 'password.*', 'profile.edit'))
            <div class="alert alert-sukses">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any() && ! request()->routeIs('profile.edit', 'login', 'register', 'password.*'))
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
    </div>
@endif
