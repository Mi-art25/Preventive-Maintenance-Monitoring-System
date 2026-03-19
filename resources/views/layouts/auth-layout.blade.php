<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PMMS — {{ $title ?? 'Secure Access' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:       #0d0f12;
  --panel:    #13161b;
  --border:   #2a2d35;
  --accent:   #f0a500;
  --text:     #e8e9eb;
  --muted:    #6b6f7a;
  --input-bg: #1a1e25;
  --danger:   #e05c5c;
  --success:  #4caf82;
}

body {
  font-family: 'Barlow', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image:
    linear-gradient(var(--border) 1px, transparent 1px),
    linear-gradient(90deg, var(--border) 1px, transparent 1px);
  background-size: 48px 48px;
  opacity: 0.35;
  pointer-events: none;
}

body::after {
  content: '';
  position: fixed;
  inset: 24px;
  border: 1px solid var(--border);
  pointer-events: none;
}

.glow {
  position: fixed;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(240,165,0,0.08) 0%, transparent 70%);
  top: -100px; left: -100px;
  pointer-events: none;
  animation: driftGlow 12s ease-in-out infinite alternate;
}

@keyframes driftGlow { to { transform: translate(60px, 80px); } }

.auth-wrapper {
  position: relative;
  width: 900px;
  max-width: 95vw;
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 560px;
  border: 1px solid var(--border);
  background: var(--panel);
  box-shadow: 0 40px 100px rgba(0,0,0,0.6);
  animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}

.brand-panel {
  background: linear-gradient(145deg, #111418 0%, #0d0f12 100%);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 48px 40px;
  position: relative;
  overflow: hidden;
}

.brand-panel::before {
  content: '';
  position: absolute;
  bottom: -80px; right: -80px;
  width: 320px; height: 320px;
  border: 1px solid var(--border);
  border-radius: 50%;
  opacity: 0.4;
}

.brand-panel::after {
  content: '';
  position: absolute;
  bottom: -40px; right: -40px;
  width: 200px; height: 200px;
  border: 1px solid rgba(240,165,0,0.15);
  border-radius: 50%;
}

.logo-mark { display: flex; align-items: center; gap: 12px; }

.logo-icon {
  width: 44px; height: 44px;
  border: 2px solid var(--accent);
  display: flex; align-items: center; justify-content: center;
  position: relative;
}

.logo-icon::before {
  content: '';
  position: absolute;
  inset: 4px;
  background: var(--accent);
  clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
}

.logo-text {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 28px;
  font-weight: 800;
  letter-spacing: 2px;
}

.logo-text span { color: var(--accent); }

.brand-headline {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 48px;
  font-weight: 800;
  line-height: 1;
  text-transform: uppercase;
}

.brand-headline em { display: block; font-style: normal; color: var(--accent); }

.brand-sub {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.7;
  margin-top: 12px;
  max-width: 220px;
}

.brand-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1px;
  background: var(--border);
  border: 1px solid var(--border);
  margin-top: 32px;
}

.stat-cell { background: var(--panel); padding: 14px 16px; }
.stat-cell .num {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: var(--accent);
}
.stat-cell .lbl {
  font-size: 11px;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-top: 2px;
}

.form-panel {
  padding: 48px 44px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.top-tag {
  position: absolute;
  top: 20px; right: 20px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 10px;
  letter-spacing: 2px;
  color: var(--muted);
  text-transform: uppercase;
  border: 1px solid var(--border);
  padding: 4px 10px;
}

.tabs {
  display: flex;
  margin-bottom: 36px;
  border-bottom: 1px solid var(--border);
}

.tab-btn {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 15px;
  font-weight: 600;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  background: none;
  border: none;
  color: var(--muted);
  padding: 10px 20px 12px;
  cursor: pointer;
  position: relative;
  transition: color 0.2s;
}

.tab-btn::after {
  content: '';
  position: absolute;
  bottom: -1px; left: 0; right: 0;
  height: 2px;
  background: var(--accent);
  transform: scaleX(0);
  transition: transform 0.25s ease;
}

.tab-btn.active { color: var(--text); }
.tab-btn.active::after { transform: scaleX(1); }

.forms-container { position: relative; overflow: hidden; }

.form-slide {
  transition: transform 0.45s cubic-bezier(0.77, 0, 0.175, 1), opacity 0.35s ease;
  will-change: transform, opacity;
}

.form-slide.hidden-left {
  position: absolute; top: 0; left: 0; right: 0;
  transform: translateX(-110%);
  opacity: 0;
  pointer-events: none;
}

.form-slide.hidden-right {
  position: absolute; top: 0; left: 0; right: 0;
  transform: translateX(110%);
  opacity: 0;
  pointer-events: none;
}

.field { margin-bottom: 18px; }

label {
  display: block;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 7px;
}

input[type="text"],
input[type="email"],
input[type="password"],
select {
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--border);
  color: var(--text);
  font-family: 'Barlow', sans-serif;
  font-size: 14px;
  padding: 11px 14px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  appearance: none;
}

input:focus, select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(240,165,0,0.08);
}

input::placeholder { color: var(--muted); opacity: 0.6; }

.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.btn-submit {
  width: 100%;
  margin-top: 6px;
  background: var(--accent);
  color: #0d0f12;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 15px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  border: none;
  padding: 14px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: background 0.2s, transform 0.1s;
}

.btn-submit::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
  transform: translateX(-100%);
  transition: transform 0.5s ease;
}

.btn-submit:hover { background: #f5b520; }
.btn-submit:hover::before { transform: translateX(100%); }
.btn-submit:active { transform: scale(0.99); }

.form-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 18px;
}

.link-btn {
  background: none;
  border: none;
  color: var(--muted);
  font-size: 12px;
  cursor: pointer;
  text-decoration: underline;
  text-underline-offset: 3px;
  transition: color 0.2s;
  padding: 0;
  font-family: 'Barlow', sans-serif;
}

.link-btn:hover { color: var(--accent); }
a.link-btn { text-decoration: underline; color: var(--muted); }
a.link-btn:hover { color: var(--accent); }

.alert {
  padding: 10px 14px;
  font-size: 12px;
  margin-bottom: 16px;
  border-left: 3px solid;
}

.alert-error   { background: rgba(224,92,92,0.1);  border-color: var(--danger);  color: var(--danger); }
.alert-success { background: rgba(76,175,130,0.1); border-color: var(--success); color: var(--success); }

.divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 16px 0;
  color: var(--muted);
  font-size: 11px;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.divider::before, .divider::after {
  content: ''; flex: 1;
  height: 1px;
  background: var(--border);
}

@media (max-width: 640px) {
  .auth-wrapper { grid-template-columns: 1fr; }
  .brand-panel { display: none; }
  .form-panel { padding: 36px 28px; }
}
</style>
</head>
<body>
<div class="glow"></div>
{{ $slot }}
<script>
let current = '{{ $activeTab ?? "login" }}';

function switchTab(tab, btnEl) {
  if (tab === current) return;
  const loginForm    = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const tabs         = document.querySelectorAll('.tab-btn');

  tabs.forEach(b => b.classList.remove('active'));
  btnEl.classList.add('active');

  if (tab === 'register') {
    loginForm.classList.add('hidden-left');
    loginForm.classList.remove('hidden-right');
    registerForm.classList.remove('hidden-right');
    registerForm.classList.remove('hidden-left');
  } else {
    registerForm.classList.add('hidden-right');
    registerForm.classList.remove('hidden-left');
    loginForm.classList.remove('hidden-left');
    loginForm.classList.remove('hidden-right');
  }
  current = tab;
}
</script>
</body>
</html>