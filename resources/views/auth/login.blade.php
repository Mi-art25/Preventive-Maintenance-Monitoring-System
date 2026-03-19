@extends('layouts.auth')

@section('title', 'Sign In — PMMS')

@section('content')
<div class="auth-wrapper">

  {{-- Brand Panel --}}
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
      <p class="brand-sub">Track equipment, schedule maintenance, and scan QR codes — all in one system.</p>
      <div class="brand-stats">
        <div class="stat-cell">
          <div class="num">{{ \App\Models\Equipment::count() }}</div>
          <div class="lbl">Equipment</div>
        </div>
        <div class="stat-cell">
          <div class="num">{{ \App\Models\MaintenanceSchedule::where('status','active')->count() }}</div>
          <div class="lbl">Schedules</div>
        </div>
        <div class="stat-cell">
          <div class="num">{{ \App\Models\MaintenanceSchedule::dueToday()->count() }}</div>
          <div class="lbl">Due Today</div>
        </div>
        <div class="stat-cell">
          <div class="num">{{ \App\Models\Technician::where('is_available', true)->count() }}</div>
          <div class="lbl">Technicians</div>
        </div>
      </div>
    </div>

    <div style="font-size:11px;color:var(--muted);letter-spacing:1px;">
      SYS VER 1.0.0 &nbsp;·&nbsp; LARAVEL 11
    </div>
  </div>

  {{-- Form Panel --}}
  <div class="form-panel">
    <div class="top-tag">Secure Access</div>

    <div class="tabs">
      <button class="tab-btn {{ !session('register_mode') ? 'active' : '' }}"
              onclick="switchTab('login', this)">Sign In</button>
      <button class="tab-btn {{ session('register_mode') ? 'active' : '' }}"
              onclick="switchTab('register', this)">Register</button>
    </div>

    <div class="forms-container">

      {{-- LOGIN --}}
      <div class="form-slide {{ session('register_mode') ? 'hidden-left' : '' }}" id="loginForm">

        @if ($errors->login->any() || (session('active_form') === 'login' && $errors->any()))
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf
          <div class="field">
            <label for="login-email">Email Address</label>
            <input type="email" id="login-email" name="email"
                   value="{{ old('email') }}"
                   placeholder="admin@pmms.com"
                   required autocomplete="email">
          </div>
          <div class="field">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password"
                   placeholder="••••••••"
                   required autocomplete="current-password">
          </div>

          <button type="submit" class="btn-submit">Sign In &nbsp;→</button>
        </form>

        <div class="form-footer">
          <button class="link-btn"
                  onclick="switchTab('register', document.querySelectorAll('.tab-btn')[1])">
            No account? Register
          </button>
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="link-btn">Forgot password?</a>
          @endif
        </div>
      </div>

      {{-- REGISTER --}}
      <div class="form-slide {{ session('register_mode') ? '' : 'hidden-right' }}" id="registerForm">

        @if ($errors->register->any() || (session('active_form') === 'register' && $errors->any()))
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
          @csrf
          <div class="field">
            <label for="reg-name">Full Name</label>
            <input type="text" id="reg-name" name="name"
                   value="{{ old('name') }}"
                   placeholder="Juan dela Cruz"
                   required autocomplete="name">
          </div>
          <div class="field">
            <label for="reg-email">Email Address</label>
            <input type="email" id="reg-email" name="email"
                   value="{{ old('email') }}"
                   placeholder="juan@company.com"
                   required autocomplete="email">
          </div>
          <div class="field-row">
            <div class="field" style="margin-bottom:0">
              <label for="reg-password">Password</label>
              <input type="password" id="reg-password" name="password"
                     placeholder="Min. 8 characters"
                     required autocomplete="new-password">
            </div>
            <div class="field" style="margin-bottom:0">
              <label for="reg-confirm">Confirm</label>
              <input type="password" id="reg-confirm" name="password_confirmation"
                     placeholder="Repeat password" required>
            </div>
          </div>

          <div class="divider">Role</div>

          <div class="field">
            <label for="reg-role">Select Role</label>
            <select id="reg-role" name="role">
              <option value="technician">Technician</option>
              <option value="supervisor">Supervisor</option>
              <option value="viewer">Viewer</option>
            </select>
          </div>

          <button type="submit" class="btn-submit">Create Account &nbsp;→</button>
        </form>

        <div class="form-footer">
          <button class="link-btn"
                  onclick="switchTab('login', document.querySelectorAll('.tab-btn')[0])">
            Already have an account?
          </button>
        </div>
      </div>

    </div>{{-- /forms-container --}}
  </div>{{-- /form-panel --}}

</div>{{-- /auth-wrapper --}}
@endsection