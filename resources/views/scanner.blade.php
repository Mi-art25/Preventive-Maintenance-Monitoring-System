<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>QR Scanner — PMMS</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg:#0d0f12; --panel:#13161b; --border:#2a2d35;
  --accent:#f0a500; --text:#e8e9eb; --muted:#6b6f7a;
  --danger:#e05c5c; --success:#4caf82; --warning:#f0a500;
}
body { font-family:'Barlow',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; flex-direction:column; }
.topbar { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid var(--border); background:var(--panel); }
.topbar-logo { font-family:'Barlow Condensed',sans-serif; font-size:20px; font-weight:800; letter-spacing:2px; }
.topbar-logo span { color:var(--accent); }
.nav-btn { background:none; border:1px solid var(--border); color:var(--muted); font-size:12px; padding:6px 14px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:color .2s,border-color .2s; font-family:'Barlow',sans-serif; }
.nav-btn:hover { color:var(--accent); border-color:var(--accent); }
.topbar-nav { display:flex; gap:8px; }
.main { flex:1; display:grid; grid-template-columns:1fr 1fr; gap:32px; max-width:1000px; width:100%; margin:0 auto; padding:32px 20px; }
.section-label { font-family:'Barlow Condensed',sans-serif; font-size:11px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); display:flex; align-items:center; gap:8px; }
.section-label::before { content:''; display:inline-block; width:6px; height:6px; background:var(--accent); }
.scanner-box { display:flex; flex-direction:column; gap:16px; }
.camera-frame { position:relative; background:#000; border:1px solid var(--border); aspect-ratio:1; overflow:hidden; width:100%; }
.camera-frame::before { content:''; position:absolute; top:12px; left:12px; width:32px; height:32px; border-top:2px solid var(--accent); border-left:2px solid var(--accent); z-index:10; pointer-events:none; }
.camera-frame::after  { content:''; position:absolute; bottom:12px; right:12px; width:32px; height:32px; border-bottom:2px solid var(--accent); border-right:2px solid var(--accent); z-index:10; pointer-events:none; }
.scan-line { position:absolute; left:12px; right:12px; height:2px; background:linear-gradient(90deg,transparent,var(--accent),transparent); z-index:9; animation:scanMove 2.5s ease-in-out infinite; pointer-events:none; opacity:0; transition:opacity .4s; }
.scan-line.active { opacity:1; }
@keyframes scanMove { 0%{top:12px} 50%{top:calc(100% - 14px)} 100%{top:12px} }
#reader { width:100% !important; border:none !important; }
#reader video { width:100% !important; height:100% !important; object-fit:cover; }
#reader img { display:none; }
.scanner-controls { display:flex; gap:10px; }
.btn { flex:1; font-family:'Barlow Condensed',sans-serif; font-size:13px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; border:1px solid var(--border); padding:11px 16px; cursor:pointer; transition:all .2s; }
.btn-primary { background:var(--accent); color:#0d0f12; border-color:var(--accent); }
.btn-primary:hover { background:#f5b520; }
.btn-secondary { background:none; color:var(--muted); }
.btn-secondary:hover { color:var(--text); border-color:var(--text); }
.manual-wrap { display:flex; gap:8px; }
.manual-wrap input { flex:1; background:#1a1e25; border:1px solid var(--border); color:var(--text); font-family:'Barlow',sans-serif; font-size:13px; padding:10px 14px; outline:none; transition:border-color .2s; }
.manual-wrap input:focus { border-color:var(--accent); }
.manual-wrap input::placeholder { color:var(--muted); }
.result-box { display:flex; flex-direction:column; gap:16px; }
.result-placeholder { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; border:1px dashed var(--border); padding:40px 20px; text-align:center; color:var(--muted); gap:12px; min-height:300px; }
.placeholder-icon { width:56px; height:56px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:24px; }
.loading-card { display:none; border:1px solid var(--border); padding:40px; text-align:center; color:var(--muted); }
.loading-card.show { display:block; }
.spinner { width:28px; height:28px; border:2px solid var(--border); border-top-color:var(--accent); border-radius:50%; animation:spin .8s linear infinite; margin:0 auto 12px; }
@keyframes spin { to{transform:rotate(360deg)} }
.error-card { display:none; border:1px solid var(--danger); padding:20px; text-align:center; color:var(--danger); background:rgba(224,92,92,.05); }
.error-card.show { display:block; animation:fadeUp .3s ease; }
.result-card { display:none; flex-direction:column; gap:16px; }
.result-card.show { display:flex; animation:fadeUp .4s ease; }
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.result-header { border:1px solid var(--border); padding:20px; background:var(--panel); }
.result-serial { font-family:'Barlow Condensed',sans-serif; font-size:11px; letter-spacing:2px; color:var(--accent); text-transform:uppercase; margin-bottom:6px; }
.result-name { font-family:'Barlow Condensed',sans-serif; font-size:28px; font-weight:700; line-height:1.1; }
.result-sub { font-size:13px; color:var(--muted); margin-top:4px; }
.status-badge { display:inline-block; font-family:'Barlow Condensed',sans-serif; font-size:11px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; padding:3px 10px; margin-top:10px; }
.status-active{background:rgba(76,175,130,.15);color:var(--success)}
.status-under_maintenance{background:rgba(240,165,0,.15);color:var(--warning)}
.status-inactive{background:rgba(107,111,122,.2);color:var(--muted)}
.status-retired{background:rgba(224,92,92,.15);color:var(--danger)}
.result-grid { display:grid; grid-template-columns:1fr 1fr; gap:1px; background:var(--border); border:1px solid var(--border); }
.result-cell { background:var(--panel); padding:12px 14px; }
.result-cell .lbl { font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:4px; }
.result-cell .val { font-size:14px; font-weight:500; }
.schedule-alert { padding:14px 16px; border-left:3px solid; font-size:13px; display:none; }
.schedule-alert.overdue  { border-color:var(--danger);  background:rgba(224,92,92,.1);  color:var(--danger); }
.schedule-alert.upcoming { border-color:var(--success); background:rgba(76,175,130,.1); color:var(--success); }
.schedule-alert.show { display:block; }
.schedule-alert strong { display:block; margin-bottom:2px; font-size:14px; }
.result-actions { display:flex; gap:10px; }
@media(max-width:680px){ .main{grid-template-columns:1fr;padding:20px 16px} }
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-logo">PM<span>MS</span></div>
  <div class="topbar-nav">
    <a href="{{ route('filament.admin.pages.dashboard') }}" class="nav-btn">&#9632; Dashboard</a>
    <a href="{{ route('filament.admin.resources.maintenance-logs.create') }}" class="nav-btn">+ Log Maintenance</a>
  </div>
</div>

<div class="main">

  {{-- Scanner --}}
  <div class="scanner-box">
    <div class="section-label">QR Code Scanner</div>

    <div class="camera-frame" id="cameraFrame">
      <div class="scan-line" id="scanLine"></div>
      <div id="reader"></div>
    </div>

    <div class="scanner-controls">
      <button class="btn btn-primary" id="startBtn" onclick="startScanner()">▶ Start Camera</button>
      <button class="btn btn-secondary" id="stopBtn" onclick="stopScanner()" style="display:none">■ Stop</button>
    </div>

    <div class="section-label" style="margin-top:8px">Manual Entry</div>
    <div class="manual-wrap">
      <input type="text" id="manualInput" placeholder="e.g. EQP-2026-00001"
             onkeydown="if(event.key==='Enter') lookupManual()">
      <button class="btn btn-primary" style="flex:0;padding:10px 20px" onclick="lookupManual()">Search</button>
    </div>
  </div>

  {{-- Result --}}
  <div class="result-box">
    <div class="section-label">Equipment Details</div>

    <div class="result-placeholder" id="placeholder">
      <div class="placeholder-icon">⬡</div>
      <div style="font-size:13px">Scan a QR code or enter a serial number</div>
      <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase">Waiting for scan...</div>
    </div>

    <div class="loading-card" id="loadingCard">
      <div class="spinner"></div>
      Looking up equipment...
    </div>

    <div class="error-card" id="errorCard">
      <div style="font-size:32px;margin-bottom:8px">⚠</div>
      <strong id="errorMsg">Equipment not found.</strong>
      <div style="font-size:12px;margin-top:4px;color:var(--muted)" id="errorSub"></div>
    </div>

    <div class="result-card" id="resultCard">
      <div class="result-header">
        <div class="result-serial" id="resSerial"></div>
        <div class="result-name"   id="resName"></div>
        <div class="result-sub"    id="resSub"></div>
        <div class="status-badge"  id="resStatus"></div>
      </div>
      <div class="result-grid">
        <div class="result-cell"><div class="lbl">Category</div><div class="val" id="resCategory"></div></div>
        <div class="result-cell"><div class="lbl">Location</div><div class="val" id="resLocation"></div></div>
        <div class="result-cell"><div class="lbl">Brand</div><div class="val" id="resBrand"></div></div>
        <div class="result-cell"><div class="lbl">Model</div><div class="val" id="resModel"></div></div>
      </div>
      <div class="schedule-alert" id="scheduleAlert">
        <strong id="scheduleTitle"></strong>
        <span   id="scheduleDetail"></span>
      </div>
      <div class="result-actions">
        <a href="#" class="btn btn-primary"   id="logBtn"    style="flex:1;text-align:center;text-decoration:none">+ Log Maintenance</a>
        <a href="#" class="btn btn-secondary" id="detailBtn" style="text-align:center;text-decoration:none">View Details</a>
      </div>
    </div>
  </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null, lastScanned = '';

function startScanner() {
  document.getElementById('startBtn').style.display = 'none';
  document.getElementById('stopBtn').style.display  = 'block';
  document.getElementById('scanLine').classList.add('active');
  html5QrCode = new Html5Qrcode('reader');
  html5QrCode.start(
    { facingMode: 'environment' },
    { fps: 10, qrbox: { width: 220, height: 220 } },
    (decoded) => {
      if (decoded === lastScanned) return;
      lastScanned = decoded;
      lookupEquipment(decoded);
      setTimeout(() => { lastScanned = ''; }, 3000);
    },
    () => {}
  ).catch(() => { showError('Camera access denied.', 'Allow camera permission and try again.'); stopScanner(); });
}

function stopScanner() {
  if (html5QrCode) html5QrCode.stop().then(() => {
    html5QrCode = null;
    document.getElementById('startBtn').style.display = 'block';
    document.getElementById('stopBtn').style.display  = 'none';
    document.getElementById('scanLine').classList.remove('active');
  });
}

function lookupManual() {
  const v = document.getElementById('manualInput').value.trim();
  if (v) lookupEquipment(v);
}

function lookupEquipment(query) {
  showLoading();
  fetch('{{ route("scanner.lookup") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    body: JSON.stringify({ query })
  })
  .then(r => r.json())
  .then(data => { data.found ? showResult(data) : showError('Equipment not found.', `No match for: ${query}`); })
  .catch(() => showError('Network error.', 'Could not reach the server.'));
}

function showLoading() {
  document.getElementById('placeholder').style.display = 'none';
  document.getElementById('loadingCard').classList.add('show');
  document.getElementById('errorCard').classList.remove('show');
  document.getElementById('resultCard').classList.remove('show');
}

function showError(msg, sub) {
  document.getElementById('loadingCard').classList.remove('show');
  document.getElementById('resultCard').classList.remove('show');
  document.getElementById('placeholder').style.display = 'none';
  document.getElementById('errorMsg').textContent = msg;
  document.getElementById('errorSub').textContent = sub;
  document.getElementById('errorCard').classList.add('show');
}

function showResult(data) {
  document.getElementById('loadingCard').classList.remove('show');
  document.getElementById('errorCard').classList.remove('show');
  document.getElementById('placeholder').style.display = 'none';
  const eq = data.equipment;
  document.getElementById('resSerial').textContent   = eq.serial_number;
  document.getElementById('resName').textContent     = eq.name;
  document.getElementById('resSub').textContent      = [eq.brand, eq.model].filter(Boolean).join(' · ') || '—';
  document.getElementById('resCategory').textContent = eq.category || '—';
  document.getElementById('resLocation').textContent = eq.location || '—';
  document.getElementById('resBrand').textContent    = eq.brand    || '—';
  document.getElementById('resModel').textContent    = eq.model    || '—';
  const badge = document.getElementById('resStatus');
  badge.textContent = eq.status.replace('_',' ').toUpperCase();
  badge.className   = `status-badge status-${eq.status}`;
  const alert = document.getElementById('scheduleAlert');
  if (data.next_schedule) {
    const s = data.next_schedule;
    document.getElementById('scheduleTitle').textContent  = (s.is_overdue ? '⚠ OVERDUE: ' : '📅 Next: ') + s.title;
    document.getElementById('scheduleDetail').textContent = `${s.frequency.toUpperCase()} · Due ${s.next_due_date}`;
    alert.className = `schedule-alert show ${s.is_overdue ? 'overdue' : 'upcoming'}`;
  } else { alert.classList.remove('show'); }
  document.getElementById('logBtn').href    = eq.log_url;
  document.getElementById('detailBtn').href = eq.detail_url;
  const card = document.getElementById('resultCard');
  card.classList.remove('show'); void card.offsetWidth;
  card.classList.add('show');
}
</script>
</body>
</html>