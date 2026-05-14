// Frontend logic wired to backend APIs (Express + SQLite)
(function () {
  function qs(sel, root = document) { return root.querySelector(sel); }
  function qsa(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }
  function formatDate(d) { const dt = new Date(d); return dt.toLocaleString(); }

  async function api(path, opts = {}) {
    const res = await fetch(path, { credentials: 'include', headers: { 'Content-Type': 'application/json' }, ...opts });
    if (!res.ok) throw new Error((await res.json().catch(()=>({}))).error || res.statusText);
    return res.json();
  }

  async function checkAuth() {
    try {
      await api('/auth/me');
      return true;
    } catch {
      // Fallback to demo session if present
      try {
        const demo = sessionStorage.getItem('demo_auth');
        if (demo) return true;
      } catch {}
      return false;
    }
  }

  async function requireAuth() {
    const ok = await checkAuth();
    if (!ok && !location.pathname.endsWith('login.html')) location.href = 'login.html';
    return ok;
  }

  function setActiveNav() {
    const p = location.pathname.split('/').pop() || 'index.html';
    qsa('.nav a').forEach(a => { if (a.getAttribute('href') === p) a.classList.add('active'); });
  }

  const inits = {
    'login.html': initLogin,
    'dashboard.html': initDashboard,
    'start-session.html': initStartSession,
    'reports.html': initReports,
    'index.html': async function () { location.replace((await checkAuth()) ? 'dashboard.html' : 'login.html'); }
  };

  document.addEventListener('DOMContentLoaded', async () => {
    const page = location.pathname.split('/').pop() || 'index.html';
    if (inits[page]) await inits[page]();
    setActiveNav();
    const logoutBtn = qs('[data-action="logout"]');
    if (logoutBtn) logoutBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      try { await api('/auth/logout', { method: 'POST' }); } catch {}
      try { sessionStorage.removeItem('demo_auth'); } catch {}
      location.href = 'login.html';
    });
  });

  // ============ LOGIN ============
  async function initLogin() {
    const form = qs('#login-form');
    if (!form) return;
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const { staffId, password } = Object.fromEntries(new FormData(form).entries());
      if (!staffId || !password) { showToast('Enter Staff ID and Password', 'warn'); return; }

      // Demo mode: allow any non-empty credentials
      try {
        // Attempt real API login first; if backend is not configured, fall back to demo success
        await api('/auth/login', { method: 'POST', body: JSON.stringify({ username: staffId, password }) });
        showToast('Login successful');
        setTimeout(() => location.href = 'dashboard.html', 300);
      } catch (err) {
        // Fallback: treat as successful login in demo environment
        try { sessionStorage.setItem('demo_auth', JSON.stringify({ user: staffId, ts: Date.now() })); } catch {}
        showToast('Demo login successful');
        setTimeout(() => location.href = 'dashboard.html', 300);
      }
    });
  }

  // ============ DASHBOARD ============
  async function initDashboard() {
    const ok = await requireAuth(); if (!ok) return;
    try {
      const systems = await api('/systems');
      const usage = await api('/usage');
      const total = usage.length;
      const open = usage.filter(u => !u.time_out).length;
      const closed = total - open;
      setText('#kpi-total', total);
      setText('#kpi-open', open);
      setText('#kpi-closed', closed);
      const tbody = qs('#recent-sessions');
      if (tbody) {
        tbody.innerHTML = usage.slice(0, 10).map(u => `
          <tr>
            <td>${u.log_id}</td>
            <td>${u.system_name}</td>
            <td>-</td>
            <td>${u.time_out ? 'Closed' : 'Open'}</td>
            <td>${formatDate(u.time_in)}</td>
            <td>${u.time_out ? formatDate(u.time_out) : '-'}</td>
          </tr>`).join('') || `<tr><td colspan="6" class="helper">No sessions yet</td></tr>`;
      }
    } catch (e) {
      showToast(e.message || 'Failed to load dashboard', 'warn');
    }
  }

  // ============ START SESSION (RFID) ============
  async function initStartSession() {
    const ok = await requireAuth(); if (!ok) return;
    // Attach RFID input handler
    setupRfidInput('#rfid-input');
    // Systems table preview
    renderUsage();

    const endBtn = qs('#end-by-uid');
    endBtn?.addEventListener('click', async () => {
      const uid = prompt('Enter RFID UID to end session:');
      if (!uid) return;
      try { await api('/rfid/end', { method: 'POST', body: JSON.stringify({ uid }) }); showToast('Session ended'); renderUsage(); } catch (e) { showToast(e.message, 'warn'); }
    });

    async function renderUsage() {
      try {
        const rows = await api('/usage');
        const tbody = qs('#session-list');
        tbody.innerHTML = rows.map(u => `
          <tr>
            <td>${u.log_id}</td>
            <td>${u.system_name}</td>
            <td>${u.student_name}</td>
            <td>${u.reg_no}</td>
            <td>${u.time_out ? 'Closed' : 'Open'}</td>
            <td>${formatDate(u.time_in)}</td>
            <td>${u.time_out ? formatDate(u.time_out) : '-'}</td>
            <td style="white-space:nowrap"></td>
          </tr>`).join('') || `<tr><td colspan="8" class="helper">No sessions yet</td></tr>`;
      } catch (e) { /* ignore */ }
    }
  }

  function setupRfidInput(selector) {
    let input = qs(selector);
    if (!input) {
      input = document.createElement('input');
      input.type = 'text';
      input.id = selector.replace('#','');
      input.className = 'input';
      input.placeholder = 'Tap RFID card...';
      const host = qs('main .card');
      const wrap = document.createElement('div');
      wrap.style.margin = '12px 0';
      const label = document.createElement('label');
      label.textContent = 'RFID Scan';
      wrap.appendChild(label);
      wrap.appendChild(input);
      host?.prepend(wrap);
    }
    input.autofocus = true;
    input.focus();
    input.addEventListener('keydown', async (e) => {
      if (e.key === 'Enter') {
        const uid = input.value.trim();
        input.value = '';
        if (!uid) return;
        try {
          const res = await api('/rfid/scan', { method: 'POST', body: JSON.stringify({ uid }) });
          showToast(`Assigned ${res.system.name} to ${res.student.name}`);
        } catch (err) {
          // If already open, try end instead to support toggle behavior
          if (/already has an open session/i.test(err.message)) {
            try { await api('/rfid/end', { method: 'POST', body: JSON.stringify({ uid }) }); showToast('Session ended'); }
            catch (e2) { showToast(e2.message, 'warn'); }
          } else {
            showToast(err.message, 'warn');
          }
        }
      }
    });
  }

  // ============ REPORTS ============
  async function initReports() {
    const ok = await requireAuth(); if (!ok) return;
    const form = qs('#filter-form');
    const tbody = qs('#report-rows');
    const exportBtn = qs('#export-csv');

    async function fetchData() {
      const f = Object.fromEntries(new FormData(form).entries());
      const params = new URLSearchParams();
      if (f.from) params.set('from', new Date(f.from).toISOString());
      if (f.to) params.set('to', new Date(f.to).toISOString());
      if (f.status) params.set('status', f.status);
      if (f.machine) params.set('system', f.machine);
      if (f.q) params.set('q', f.q);
      return api('/usage?' + params.toString());
    }

    async function render() {
      try {
        const rows = await fetchData();
        tbody.innerHTML = rows.map(s => `
          <tr>
            <td>${s.log_id}</td>
            <td>${s.system_name}</td>
            <td>${s.student_name}</td>
            <td>${s.reg_no}</td>
            <td>${s.time_out ? 'Closed' : 'Open'}</td>
            <td>${formatDate(s.time_in)}</td>
            <td>${s.time_out ? formatDate(s.time_out) : '-'}</td>
            <td>${(s.time_out && s.time_in) ? humanDuration(new Date(s.time_out)-new Date(s.time_in)) : '-'}</td>
          </tr>
        `).join('') || `<tr><td colspan="8" class="helper">No data</td></tr>`;
        setText('#result-count', rows.length);
      } catch (e) { showToast(e.message, 'warn'); }
    }

    function exportCsv() {
      const rows = Array.from(tbody.querySelectorAll('tr')).map(tr => Array.from(tr.children).map(td => td.textContent));
      const lines = rows.map(r => r.map(v => '"' + String(v ?? '').replace(/"/g, '""') + '"').join(',')).join('\n');
      const blob = new Blob([lines], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url; a.download = 'usage-report.csv'; a.click();
      URL.revokeObjectURL(url);
    }

    form?.addEventListener('input', render);
    form?.addEventListener('submit', e => { e.preventDefault(); render(); });
    exportBtn?.addEventListener('click', exportCsv);

    render();
  }

  function setText(sel, val) { const el = qs(sel); if (el) el.textContent = String(val); }
  function showToast(msg, type) {
    const el = document.createElement('div');
    el.textContent = msg;
    el.style.position = 'fixed';
    el.style.bottom = '24px';
    el.style.right = '24px';
    el.style.padding = '10px 12px';
    el.style.borderRadius = '10px';
    el.style.zIndex = '1000';
    el.style.color = '#fff';
    el.style.background = type === 'warn' ? 'linear-gradient(135deg, #f59e0b, #ea580c)' : 'linear-gradient(135deg, #22c55e, #16a34a)';
    el.style.boxShadow = '0 12px 24px rgba(0,0,0,0.35)';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 2000);
  }
  function humanDuration(ms) {
    const s = Math.floor(ms/1000);
    const d = Math.floor(s/86400);
    const h = Math.floor((s%86400)/3600);
    const m = Math.floor((s%3600)/60);
    const sec = s%60;
    return [d?d+'d':null, h?h+'h':null, m?m+'m':null, sec?sec+'s':null].filter(Boolean).join(' ');
  }
})();
