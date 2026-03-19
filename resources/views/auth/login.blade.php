<x-auth-layout :activeTab="session('active_tab', 'login')">

<div class="auth-wrapper">

  {{-- ── Brand Panel ── --}}
  <div class="brand-panel">
    <div class="logo-mark">
      <div class="logo-icon"></div>
      <div class="logo-text">PM<span>MS</span></div>
    </div>

    <div>
      <div class="brand-headline">
        Preventive
        <em>Maintenance</em>
        Monitoring
      </div>
      <p class="brand-sub">
        Centralized equipment tracking, scheduled maintenance, and real-time status monitoring.
      </p>

      <div class="brand-stats">
        <div class="stat-cell">
          <div class="num">100%</div>
          <div class="lbl">Uptime Target</div>
        </div>
        <div class="stat-cell">
          <div class="num">QR</div>
          <div class="lbl">Scan Ready</div>
        </div>
        <div class="stat-cell">
          <div class="num">24/7</div>
          <div class="lbl">Monitoring</div>
        </div>
        <div class="stat-cell">
          <div class="num">∞</div>
          <div class="lbl">Equipment</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Form Panel ── --}}
  <div class="form-panel">
    <div class="top-tag">Secure Access</div>

    {{-- Tabs --}}
    <div class="tabs">
      <button
        class="tab-btn {{ session('active_tab', 'login') === 'login' ? 'active' : '' }}"
        onclick="switchTab('login', this)">
        Sign In
      </button>
      <button
        class="tab-btn {{ session('active_tab') === 'register' ? 'active' : '' }}"
        onclick="switchTab('register', this)">
        Register
      </button>
    </div>

    <div class="forms-container">

      {{-- ── LOGIN FORM ── --}}
      <div id="loginForm" class="form-slide {{ session('active_tab') === 'register' ? 'hidden-left' : '' }}">

        @if ($errors->any() && session('active_tab', 'login') === 'login')
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <div class="field">
            <label for="login_email">Email Address</label>
            <input
              type="email"
              id="login_email"
              name="email"
              placeholder="you@example.com"
              value="{{ old('email') }}"
              autocomplete="email"
              required>
          </div>

          <div class="field">
            <label for="login_password">Password</label>
            <input
              type="password"
              id="login_password"
              name="password"
              placeholder="••••••••"
              autocomplete="current-password"
              required>
          </div>

          <button type="submit" class="btn-submit">Sign In →</button>

          <div class="form-footer">
            <label style="display:flex;align-items:center;gap:8px;font-size:12px;text-transform:none;letter-spacing:0;cursor:pointer;">
              <input type="checkbox" name="remember" style="width:auto;background:none;border:1px solid var(--border);padding:0;">
              Remember me
            </label>
          </div>
        </form>
      </div>

      {{-- ── REGISTER FORM ── --}}
      <div id="registerForm" class="form-slide {{ session('active_tab') !== 'register' ? 'hidden-right' : '' }}">

        @if ($errors->any() && session('active_tab') === 'register')
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="field">
            <label for="reg_name">Full Name</label>
            <input
              type="text"
              id="reg_name"
              name="name"
              placeholder="Juan dela Cruz"
              value="{{ old('name') }}"
              autocomplete="name"
              required>
          </div>

          <div class="field">
            <label for="reg_email">Email Address</label>
            <input
              type="email"
              id="reg_email"
              name="email"
              placeholder="you@example.com"
              value="{{ old('email') }}"
              autocomplete="email"
              required>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="reg_password">Password</label>
              <input
                type="password"
                id="reg_password"
                name="password"
                placeholder="Min. 8 chars"
                autocomplete="new-password"
                required>
            </div>
            <div class="field">
              <label for="reg_password_confirmation">Confirm</label>
              <input
                type="password"
                id="reg_password_confirmation"
                name="password_confirmation"
                placeholder="Repeat password"
                autocomplete="new-password"
                required>
            </div>
          </div>

          <button type="submit" class="btn-submit">Create Account →</button>
        </form>
      </div>

    </div>{{-- end forms-container --}}
  </div>{{-- end form-panel --}}

</div>{{-- end auth-wrapper --}}

</x-auth-layout>