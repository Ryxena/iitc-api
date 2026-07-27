<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 24px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="margin-bottom: 32px; text-align: center;">
        <h2 style="font-size: 20px; font-weight: 600; color: var(--text-main); letter-spacing: -0.025em;">Welcome Back</h2>
        <p style="font-size: 14px; font-weight: 400; color: var(--text-muted); margin-top: 6px;">Please enter your credentials to login</p>
    </div>

    <form method="POST" action="{{ route('login-web') }}">
        @csrf

        <!-- Email Address -->
        <div style="margin-bottom: 20px;">
            <label for="email" style="display: block; font-size: 13px; font-weight: 500; color: var(--text-main); margin-bottom: 6px;">Email</label>
            <input id="email" class="brutal-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@example.com" />
            @error('email')
                <p style="color: #DC2626; font-size: 13px; font-weight: 500; margin-top: 6px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
            <label for="password" style="display: block; font-size: 13px; font-weight: 500; color: var(--text-main); margin-bottom: 6px;">Password</label>
            <input id="password" class="brutal-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            @error('password')
                <p style="color: #DC2626; font-size: 13px; font-weight: 500; margin-top: 6px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div style="margin-bottom: 32px; display: flex; align-items: center;">
            <input id="remember_me" type="checkbox" name="remember" style="width: 16px; height: 16px; border: 1px solid var(--border); border-radius: 4px; accent-color: var(--accent); cursor: pointer;">
            <label for="remember_me" style="margin-left: 10px; font-size: 14px; font-weight: 400; color: var(--text-muted); cursor: pointer;">Remember me</label>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            <button type="submit" class="brutal-btn">
                Log in
            </button>
            
            @if (Route::has('password.request'))
                <div style="text-align: center; margin-top: 4px;">
                    <a class="brutal-link" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                </div>
            @endif
        </div>
    </form>
</x-guest-layout>
