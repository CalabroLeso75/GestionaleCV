<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>File Manager — Gestionale CV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin:0; background:#1a1a2e; color:#e0e0e0; font-family:'Consolas','Courier New',monospace; font-size:13px; height:100vh; display:flex; flex-direction:column; overflow:hidden; }

        /* ── TOP BAR ── */
        #topbar { background:#16213e; border-bottom:1px solid #0f3460; padding:6px 12px; display:flex; align-items:center; gap:6px; flex-shrink:0; }
        #topbar .brand { color:#e94560; font-weight:bold; font-size:14px; margin-right:10px; }
        #topbar button { background:#0f3460; border:1px solid #e94560; color:#e0e0e0; padding:3px 10px; border-radius:4px; cursor:pointer; font-size:12px; transition:background 0.15s; }
        #topbar button:hover { background:#e94560; color:#fff; }
        #topbar button:disabled { opacity:0.4; cursor:default; }
        #topbar .sep { width:1px; background:#0f3460; height:24px; margin:0 4px; }
        #topbar .spacer { flex:1; }
        #topbar select { background:#0f3460; border:1px solid #0f3460; color:#e0e0e0; padding:3px 6px; border-radius:4px; font-size:12px; }

        /* ── BREADCRUMB ── */
        #breadcrumb { background:#0d1117; border-bottom:1px solid #21262d; padding:5px 12px; display:flex; align-items:center; gap:4px; flex-shrink:0; overflow-x:auto; }
        .breadcrumb-part { color:#58a6ff; cursor:pointer; } .breadcrumb-part:hover { text-decoration:underline; }
        #breadcrumb .sep { color:#444; }

        /* ── MAIN LAYOUT ── */
        #main { display:flex; flex:1; overflow:hidden; }

        /* ── TREE SIDEBAR ── */
        #sidebar { width:230px; min-width:150px; background:#0d1117; border-right:1px solid #21262d; overflow-y:auto; flex-shrink:0; }
        .tree-node { padding:3px 8px; cursor:pointer; white-space:nowrap; display:flex; align-items:center; gap:4px; }
        .tree-node:hover { background:#1c2128; }
        .tree-node.active { background:#0f3460; }
        .tree-node .arrow { font-size:9px; width:12px; text-align:center; transition:transform 0.15s; }
        .tree-node .arrow.open { transform:rotate(90deg); }
        .tree-children { display:none; padding-left:14px; }
        .tree-children.open { display:block; }

        /* ── FILE LIST ── */
        #filelist-wrap { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        #filelist { flex:1; overflow-y:auto; padding:4px; }
        #filelist.drag-over { background:#0f3460 !important; }

        /* grid view */
        #filelist.grid { display:grid; grid-template-columns:repeat(auto-fill,90px); gap:8px; align-content:start; }
        .file-item.grid { display:flex; flex-direction:column; align-items:center; width:90px; padding:8px 4px; border-radius:6px; cursor:pointer; user-select:none; }
        .file-item.grid .ico { font-size:2em; line-height:1; }
        .file-item.grid .name { font-size:11px; text-align:center; word-break:break-all; margin-top:4px; }

        /* list view */
        #filelist.list { display:block; }
        .file-item.list { display:flex; align-items:center; gap:8px; padding:4px 8px; border-radius:4px; cursor:pointer; user-select:none; }
        .file-item.list .ico { font-size:1.1em; width:20px; text-align:center; }
        .file-item.list .name { flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .file-item.list .size, .file-item.list .date, .file-item.list .perms { width:80px; text-align:right; color:#8b949e; font-size:11px; }
        .file-item.list .perms { width:50px; font-family:monospace; }

        .file-item:hover { background:#1c2128; }
        .file-item.selected { background:#0f3460 !important; outline:1px solid #58a6ff; }
        .file-item.cut { opacity:0.5; }

        /* ── STATUS BAR ── */
        #statusbar { background:#0d1117; border-top:1px solid #21262d; padding:3px 12px; font-size:11px; color:#8b949e; display:flex; gap:16px; flex-shrink:0; }

        /* ── TERMINAL ── */
        #terminal { height:180px; background:#000; border-top:2px solid #e94560; flex-shrink:0; display:flex; flex-direction:column; }
        #term-output { flex:1; overflow-y:auto; padding:6px 10px; font-size:12px; line-height:1.4; white-space:pre-wrap; color:#00ff00; }
        #term-input-row { display:flex; align-items:center; background:#111; padding:4px 8px; gap:6px; }
        #term-prompt { color:#e94560; font-weight:bold; white-space:nowrap; }
        #term-input { flex:1; background:transparent; border:none; outline:none; color:#00ff00; font-family:inherit; font-size:12px; }
        #term-send { background:#e94560; border:none; color:#fff; padding:3px 10px; border-radius:3px; cursor:pointer; font-size:11px; }

        /* ── CONTEXT MENU ── */
        #ctx-menu { position:fixed; background:#16213e; border:1px solid #0f3460; border-radius:6px; box-shadow:0 8px 24px rgba(0,0,0,0.6); z-index:9999; display:none; min-width:160px; }
        .ctx-item { padding:7px 14px; cursor:pointer; display:flex; align-items:center; gap:8px; }
        .ctx-item:hover { background:#0f3460; }
        .ctx-sep { height:1px; background:#21262d; margin:3px 0; }

        /* ── MODAL / OVERLAY ── */
        #overlay { position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:8000; display:none; align-items:center; justify-content:center; }
        #overlay.show { display:flex; }
        .modal-box { background:#16213e; border:1px solid #0f3460; border-radius:10px; padding:24px; min-width:380px; max-width:600px; }
        .modal-box h5 { color:#e94560; margin-bottom:16px; }
        .modal-box input, .modal-box textarea { background:#0d1117; border:1px solid #21262d; color:#e0e0e0; border-radius:5px; padding:6px 10px; width:100%; font-family:inherit; }
        .modal-box textarea { min-height:300px; resize:vertical; font-size:12px; }
        .modal-box iframe { width:100%; height:70vh; border:none; background:#fff; }
        .modal-box img.lightbox { max-width:100%; max-height:75vh; object-fit:contain; border-radius:4px; }
        .modal-box .btn-ok { background:#e94560; border:none; color:#fff; padding:6px 18px; border-radius:5px; cursor:pointer; }
        .modal-box .btn-ok:hover { background:#c73652; }
        .modal-box .btn-cancel { background:#21262d; border:none; color:#e0e0e0; padding:6px 18px; border-radius:5px; cursor:pointer; margin-left:6px; }

        /* ── UPLOAD DROP ZONE ── */
        #upload-input { display:none; }

        /* ── RESIZE HANDLE ── */
        #resize-handle { width:4px; background:#21262d; cursor:col-resize; flex-shrink:0; }
        #resize-handle:hover, #resize-handle.dragging { background:#e94560; }

        /* ── SCROLLBAR STYLE ── */
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-track { background:#0d1117; }
        ::-webkit-scrollbar-thumb { background:#21262d; border-radius:3px; }
        ::-webkit-scrollbar-thumb:hover { background:#58a6ff; }
    </style>
</head>
<body>

<!-- TOP BAR -->
<div id="topbar">
    <span class="brand">📂 File Manager</span>
    <div class="sep"></div>
    <button id="btn-upload" onclick="triggerUpload()" title="Carica file">⬆ Upload</button>
    <button id="btn-mkdir" onclick="showMkdir()" title="Nuova cartella">📁+ Nuova Cartella</button>
    <button id="btn-rename" onclick="showRename()" disabled title="Rinomina">✏️ Rinomina</button>
    <div class="sep"></div>
    <button id="btn-zip" onclick="showZip()" disabled title="Comprimi selezionati">🗜️ Comprimi</button>
    <button id="btn-unzip" onclick="showUnzip()" disabled title="Estrai archivio">📂 Estrai</button>
    <button id="btn-chmod" onclick="showChmod()" disabled title="Permessi">🔐 Permessi</button>
    <div class="sep"></div>
    <button id="btn-copy" onclick="clipCopy()" disabled title="Copia (Ctrl+C)">📋 Copia</button>
    <button id="btn-cut" onclick="clipCut()" disabled title="Taglia (Ctrl+X)">✂️ Taglia</button>
    <button id="btn-paste" onclick="clipPaste()" disabled title="Incolla (Ctrl+V)">📌 Incolla</button>
    <div class="sep"></div>
    <button id="btn-delete" onclick="confirmDelete()" disabled title="Elimina (Canc)">🗑 Elimina</button>
    <button id="btn-download" onclick="downloadSelected()" disabled title="Scarica">⬇ Scarica</button>
    <div class="sep"></div>
    <button id="btn-db" onclick="dbBackup()" title="Backup Database">🗄️ DB Dump</button>
    <div class="spacer"></div>
    <input type="text" id="search-input" placeholder="🔍 Cerca file (Invio)..." style="background:#0f3460; border:1px solid #21262d; color:#e0e0e0; padding:4px 8px; border-radius:4px; font-size:12px; margin-right:12px; min-width:180px;" onkeyup="if(event.key==='Enter') searchFile()">
    <select id="view-toggle" onchange="setView(this.value)" title="Modalità vista">
        <option value="list">☰ Lista</option>
        <option value="grid">⊞ Griglia</option>
    </select>
    <span style="color:#8b949e; margin-left:12px;">Root: {{ $root }}</span>
    <a href="{{ route('admin.index') }}" style="color:#58a6ff; margin-left:12px; font-size:12px;">← Admin</a>
</div>

<!-- BREADCRUMB -->
<div id="breadcrumb"></div>

<!-- MAIN -->
<div id="main">
    <!-- TREE SIDEBAR -->
    <div id="sidebar">
        <div class="tree-node" style="padding:6px 8px; color:#8b949e; font-size:11px;">CARTELLE</div>
        <div id="tree-root"></div>
    </div>

    <div id="resize-handle"></div>

    <!-- FILE LIST + STATUS -->
    <div id="filelist-wrap">
        <div id="filelist" class="list"
             ondragover="e=event;e.preventDefault();e.currentTarget.classList.add('drag-over')"
             ondragleave="event.currentTarget.classList.remove('drag-over')"
             ondrop="handleDrop(event)">
            <!-- items rendered here -->
        </div>
        <div id="statusbar">
            <span id="status-count">Caricamento...</span>
            <span id="status-sel"></span>
            <span id="status-clip"></span>
        </div>
    </div>
</div>

<!-- TERMINAL -->
<div id="terminal">
    <div id="term-output">🟢 File Manager pronto. Digita un comando nella barra in basso.
</div>
    <div id="term-input-row">
        <span id="term-prompt">$ </span>
        <input type="text" id="term-input" placeholder="php artisan, ls, git log -n5, tail storage/logs/laravel.log" autocomplete="off" spellcheck="false">
        <button id="term-send" onclick="execCmd()">▶ Esegui</button>
    </div>
</div>

<!-- CONTEXT MENU -->
<div id="ctx-menu">
    <div class="ctx-item" onclick="ctxAction('open')">📂 <span>Apri</span></div>
    <div class="ctx-item" onclick="ctxAction('zip')">🗜️ <span>Comprimi</span></div>
    <div class="ctx-item" onclick="ctxAction('unzip')">📂 <span>Estrai</span></div>
    <div class="ctx-item" onclick="ctxAction('chmod')">🔐 <span>Permessi</span></div>
    <div class="ctx-sep"></div>
    <div class="ctx-item" onclick="ctxAction('copy')">📋 <span>Copia</span></div>
    <div class="ctx-item" onclick="ctxAction('cut')">✂️ <span>Taglia</span></div>
    <div class="ctx-item" onclick="ctxAction('paste')">📌 <span>Incolla qui</span></div>
    <div class="ctx-sep"></div>
    <div class="ctx-item" onclick="ctxAction('rename')">✏️ <span>Rinomina</span></div>
    <div class="ctx-item" onclick="ctxAction('download')">⬇️ <span>Scarica</span></div>
    <div class="ctx-sep"></div>
    <div class="ctx-item" onclick="ctxAction('delete')" style="color:#f85149;">🗑 <span>Elimina</span></div>
    <div class="ctx-sep"></div>
    <div class="ctx-item" onclick="ctxAction('mkdir')">📁+ <span>Nuova Cartella qui</span></div>
    <div class="ctx-item" onclick="ctxAction('upload')">⬆ <span>Carica file qui</span></div>
</div>

<!-- OVERLAY MODAL -->
<div id="overlay">
    <div class="modal-box" id="modal-box">
        <h5 id="modal-title">Azione</h5>
        <div id="modal-body"></div>
        <div style="margin-top:16px; display:flex; gap:8px;">
            <button class="btn-ok" id="modal-ok" onclick="modalOk()">OK</button>
            <button class="btn-cancel" onclick="closeModal()">Annulla</button>
        </div>
    </div>
</div>

<!-- HIDDEN UPLOAD INPUT -->
<input type="file" id="upload-input" multiple onchange="uploadFiles(this.files)">

<script>
'use strict';

// ── State ────────────────────────────────────────────────────────────
const CSRF    = document.querySelector('meta[name=csrf-token]').content;
let CWD       = '/';        // current working directory (relative)
let items     = [];         // current directory listing
let selected  = new Set();  // selected item paths
let clipboard = { mode: null, paths: [] }; // mode: 'copy'|'cut'
let viewMode  = 'list';
let modalAction = null;

const fileIcons = {
    dir    : '📁',
    php    : '🐘', blade: '🗃️', html: '🌐', css: '🎨', js: '📜', json: '{}',
    md     : '📝', txt : '📄', sql : '🗄️', env: '🔒', log: '📋',
    jpg    : '🖼️', jpeg: '🖼️', png: '🖼️', gif: '🖼️', svg: '🖼️', webp: '🖼️',
    zip    : '🗜️', rar : '🗜️', gz : '🗜️', tar: '🗜️',
    pdf    : '📑', xml : '📋', sh  : '⚙️', bat: '⚙️',
    default: '📄',
};

function getIcon(item) {
    if (item.type === 'dir') return fileIcons.dir;
    return fileIcons[item.extension] || fileIcons.default;
}

// ── API Helpers ──────────────────────────────────────────────────────
async function api(route, data = {}, method = 'POST') {
    const opts = { method, headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } };
    if (method === 'POST' && !(data instanceof FormData)) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(data);
    } else if (data instanceof FormData) {
        opts.body = data;
    }
    const res  = await fetch(route, opts);
    const json = await res.json();
    if (!res.ok) throw new Error(json.error || res.statusText);
    return json;
}

// ── Navigation ───────────────────────────────────────────────────────
async function navigate(path) {
    CWD = path;
    selected.clear();
    updateButtons();
    await refreshList();
    updateBreadcrumb();
}

async function refreshList() {
    try {
        const data = await api('{{ route("admin.filemanager.ls") }}', { path: CWD });
        items = data.items;
        renderFiles();
        updateStatus();
    } catch(e) { termPrint('❌ ' + e.message); }
}

// ── Render Files ──────────────────────────────────────────────────────
function renderFiles() {
    const list = document.getElementById('filelist');
    list.className = viewMode;
    list.innerHTML = '';

    if (CWD !== '/') {
        const el = makeItem({ name: '..', type: 'dir', path: parentOf(CWD) }, viewMode);
        el.ondblclick = () => navigate(parentOf(CWD));
        list.appendChild(el);
    }

    items.forEach(item => {
        const el = makeItem(item, viewMode);
        el.onclick  = (e) => toggleSelect(e, item.path);
        el.ondblclick = () => openItem(item);
        el.oncontextmenu = (e) => { e.preventDefault(); toggleSelect(e, item.path, true); showCtx(e); };
        list.appendChild(el);
    });
}

function makeItem(item, mode) {
    const el  = document.createElement('div');
    el.className = `file-item ${mode}`;
    el.dataset.path = item.path;

    const isCut = clipboard.mode === 'cut' && clipboard.paths.includes(item.path);
    if (isCut) el.classList.add('cut');
    if (selected.has(item.path)) el.classList.add('selected');

    if (mode === 'grid') {
        el.innerHTML = `<div class="ico">${getIcon(item)}</div><div class="name">${esc(item.name)}</div>`;
    } else {
        const size = item.size != null ? fmtSize(item.size) : '';
        const date = item.modified ? new Date(item.modified * 1000).toLocaleDateString('it-IT') + ' ' + new Date(item.modified * 1000).toLocaleTimeString('it-IT', {hour:'2-digit',minute:'2-digit'}) : '';
        const perms = item.perms || '';
        el.innerHTML = `<span class="ico">${getIcon(item)}</span><span class="name">${esc(item.name)}</span><span class="size">${size}</span><span class="date">${date}</span><span class="perms">${perms}</span>`;
    }
    return el;
}

function openItem(item) {
    if (item.type === 'dir') { navigate(item.path); return; }
    
    // Lightbox for images
    if (['jpg','jpeg','png','gif','svg','webp'].includes(item.extension)) {
        openImage(item.path); return;
    }
    // PDF Viewer
    if (item.extension === 'pdf') {
        openPdf(item.path); return;
    }
    // Log Tail
    if (item.extension === 'log') {
        openLog(item.path); return;
    }

    // Check if text-editable
    const editableExt = ['php','js','css','html','blade','json','env','txt','md','sql','sh','bat','xml','ini','htaccess','gitignore','yaml','yml'];
    if (editableExt.includes(item.extension || '')) { openEditor(item); }
    else { downloadFile(item.path); }
}

// ── Selection ────────────────────────────────────────────────────────
function toggleSelect(e, path, forceSelect = false) {
    if (!e.ctrlKey && !e.shiftKey && !forceSelect) {
        if (!selected.has(path)) {
            selected.clear();
            selected.add(path);
        }
    } else if (e.ctrlKey || forceSelect && !selected.has(path)) {
        if (selected.has(path) && !forceSelect) selected.delete(path);
        else selected.add(path);
    }
    if (forceSelect) { if (!selected.has(path)) { selected.clear(); selected.add(path); } }
    renderFiles();
    updateButtons();
    updateStatus();
}

document.getElementById('filelist').onclick = (e) => {
    if (e.target.id === 'filelist') { selected.clear(); renderFiles(); updateButtons(); updateStatus(); }
};

// ── Breadcrumb ───────────────────────────────────────────────────────
function updateBreadcrumb() {
    const bc  = document.getElementById('breadcrumb');
    const parts = CWD.split('/').filter(Boolean);
    let html = `<span class="breadcrumb-part" onclick="navigate('/')">🏠 /</span>`;
    let acc  = '/';
    parts.forEach(p => {
        acc += (acc.endsWith('/') ? '' : '/') + p;
        const path = acc;
        html += `<span class="sep">›</span><span class="breadcrumb-part" onclick="navigate('${path}')">${esc(p)}</span>`;
    });
    bc.innerHTML = html;
    document.getElementById('term-prompt').textContent = `${CWD} $ `;
}

// ── Status ───────────────────────────────────────────────────────────
function updateStatus() {
    const total = items.length;
    const dirs  = items.filter(i => i.type === 'dir').length;
    document.getElementById('status-count').textContent = `${total} elementi  (${dirs} cartelle, ${total - dirs} file)`;
    const selArr = [...selected];
    document.getElementById('status-sel').textContent = selArr.length ? `✔ ${selArr.length} selezionati` : '';
    document.getElementById('status-clip').textContent = clipboard.paths.length ? `📋 ${clipboard.mode === 'cut' ? '✂️' : '📋'} ${clipboard.paths.length} nel clipboard` : '';
}

function updateButtons() {
    const hasSel = selected.size > 0;
    const oneSel = selected.size === 1;
    ['btn-rename','btn-copy','btn-cut','btn-delete','btn-download','btn-zip','btn-chmod'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = !hasSel;
    });
    const rn = document.getElementById('btn-rename');
    if (rn) rn.disabled = !oneSel;
    
    const unz = document.getElementById('btn-unzip');
    if (unz) {
        const firstSel = items?.find(i => i.path === [...selected][0]);
        const isZip = oneSel && firstSel && firstSel.extension === 'zip';
        unz.disabled = !isZip;
    }

    const pst = document.getElementById('btn-paste');
    if (pst) pst.disabled  = clipboard.paths.length === 0;
}

// ── Context Menu ─────────────────────────────────────────────────────
function showCtx(e) {
    const m = document.getElementById('ctx-menu');
    m.style.display = 'block';
    m.style.left = Math.min(e.clientX, innerWidth - 170) + 'px';
    m.style.top  = Math.min(e.clientY, innerHeight - 250) + 'px';
}
document.addEventListener('click', () => document.getElementById('ctx-menu').style.display = 'none');
document.addEventListener('contextmenu', e => { if (!e.target.closest('#ctx-menu')) document.getElementById('ctx-menu').style.display = 'none'; });

function ctxAction(action) {
    document.getElementById('ctx-menu').style.display = 'none';
    switch(action) {
        case 'open'    : if (selected.size === 1) { const item = items.find(i => i.path === [...selected][0]); if (item) openItem(item); } break;
        case 'copy'    : clipCopy(); break;
        case 'cut'     : clipCut(); break;
        case 'paste'   : clipPaste(); break;
        case 'rename'  : showRename(); break;
        case 'zip'     : showZip(); break;
        case 'unzip'   : showUnzip(); break;
        case 'chmod'   : showChmod(); break;
        case 'download': downloadSelected(); break;
        case 'delete'  : confirmDelete(); break;
        case 'mkdir'   : showMkdir(); break;
        case 'upload'  : triggerUpload(); break;
    }
}

// ── Clipboard ────────────────────────────────────────────────────────
function clipCopy() { clipboard = { mode:'copy', paths: [...selected] }; updateButtons(); updateStatus(); termPrint(`📋 ${clipboard.paths.length} elemento/i copiati nel clipboard.`); }
function clipCut()  { clipboard = { mode:'cut',  paths: [...selected] }; renderFiles(); updateButtons(); updateStatus(); termPrint(`✂️ ${clipboard.paths.length} elemento/i tagliati.`); }

async function clipPaste() {
    if (!clipboard.paths.length) return;
    try {
        const endpoint = clipboard.mode === 'cut'
            ? '{{ route("admin.filemanager.move") }}'
            : '{{ route("admin.filemanager.copy") }}';
        await api(endpoint, { sources: clipboard.paths, destination: CWD });
        if (clipboard.mode === 'cut') clipboard = { mode:null, paths:[] };
        termPrint(`✅ Incollati ${clipboard.paths.length > 0 ? clipboard.paths.length : 'elementi'} in ${CWD}`);
        await refreshList();
    } catch(e) { termPrint('❌ ' + e.message); }
}

// ── Delete ───────────────────────────────────────────────────────────
function confirmDelete() {
    const paths = [...selected];
    if (!paths.length) return;
    showModal('🗑 Conferma Eliminazione',
        `<p style="color:#f85149;">Eliminare <strong>${paths.length}</strong> elemento/i? <br><em>Azione irreversibile.</em></p>`,
        async () => {
            try {
                await api('{{ route("admin.filemanager.delete") }}', { paths });
                termPrint(`🗑 Eliminati: ${paths.map(p => p.split('/').pop()).join(', ')}`);
                selected.clear(); await refreshList();
            } catch(e) { termPrint('❌ ' + e.message); }
        }
    );
}

// ── Rename ───────────────────────────────────────────────────────────
function showRename() {
    if (selected.size !== 1) return;
    const path = [...selected][0];
    const current = path.split('/').pop();
    showModal('✏️ Rinomina', `<input id="rename-input" value="${esc(current)}" style="font-size:14px;">`, async () => {
        const newName = document.getElementById('rename-input').value.trim();
        if (!newName || newName === current) return;
        try {
            await api('{{ route("admin.filemanager.rename") }}', { path, name: newName });
            termPrint(`✏️ Rinominato: ${current} → ${newName}`);
            selected.clear(); await refreshList();
        } catch(e) { termPrint('❌ ' + e.message); }
    });
    setTimeout(() => { const i = document.getElementById('rename-input'); i?.focus(); i?.select(); }, 50);
}

// ── Mkdir ─────────────────────────────────────────────────────────────
function showMkdir() {
    showModal('📁 Nuova Cartella', `<input id="mkdir-input" placeholder="nome cartella" style="font-size:14px;">`, async () => {
        const name = document.getElementById('mkdir-input').value.trim();
        if (!name) return;
        try {
            await api('{{ route("admin.filemanager.mkdir") }}', { path: CWD, name });
            termPrint(`📁 Cartella creata: ${name}`);
            await refreshList();
        } catch(e) { termPrint('❌ ' + e.message); }
    });
    setTimeout(() => document.getElementById('mkdir-input')?.focus(), 50);
}

// ── Upload ────────────────────────────────────────────────────────────
function triggerUpload() { document.getElementById('upload-input').click(); }

async function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    await uploadFiles(e.dataTransfer.files);
}

async function uploadFiles(files) {
    if (!files || files.length === 0) return;
    const fd = new FormData();
    fd.append('path', CWD);
    fd.append('_token', CSRF);
    [...files].forEach(f => fd.append('files[]', f));
    termPrint(`⬆ Caricamento di ${files.length} file in ${CWD}...`);
    try {
        const res = await fetch('{{ route("admin.filemanager.upload") }}', { method:'POST', body:fd, headers:{ 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' } });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Upload fallito');
        termPrint(`✅ Caricati: ${data.uploaded.join(', ')}`);
        await refreshList();
    } catch(e) { termPrint('❌ Upload error: ' + e.message); }
}

// ── Download ──────────────────────────────────────────────────────────
function downloadSelected() {
    [...selected].filter(p => {
        const item = items.find(i => i.path === p);
        return item && item.type === 'file';
    }).forEach(p => downloadFile(p));
}

function downloadFile(path) {
    const a = document.createElement('a');
    a.href = '{{ route("admin.filemanager.download") }}?path=' + encodeURIComponent(path);
    a.download = path.split('/').pop();
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
}

// ── Inline Editor ────────────────────────────────────────────────────
async function openEditor(item) {
    try {
        const data = await api('{{ route("admin.filemanager.read") }}', { path: item.path });
        showModal(`✏️ Editor: ${item.name}`,
            `<textarea id="editor-content" spellcheck="false">${esc(data.content)}</textarea>`,
            async () => {
                const content = document.getElementById('editor-content').value;
                try {
                    await api('{{ route("admin.filemanager.write") }}', { path: item.path, content });
                    termPrint(`💾 Salvato: ${item.path}`);
                } catch(e) { termPrint('❌ ' + e.message); }
            }
        );
    } catch(e) { termPrint('❌ ' + e.message); }
}

// ── Terminal ──────────────────────────────────────────────────────────
const termOut = document.getElementById('term-output');

function termPrint(text) {
    termOut.textContent += '\n' + text;
    termOut.scrollTop = termOut.scrollHeight;
}

async function execCmd() {
    const input = document.getElementById('term-input');
    const cmd   = input.value.trim();
    if (!cmd) return;
    termPrint(`${CWD} $ ${cmd}`);
    input.value = '';
    try {
        const data = await api('{{ route("admin.filemanager.exec") }}', { command: cmd, cwd: CWD });
        termPrint(data.output);
    } catch(e) { termPrint('❌ ' + e.message); }
    await refreshList();
}

document.getElementById('term-input').addEventListener('keydown', e => { if (e.key === 'Enter') execCmd(); });

// ── Modal ─────────────────────────────────────────────────────────────
function showModal(title, body, onOk) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').innerHTML = body;
    modalAction = onOk;
    document.getElementById('overlay').classList.add('show');
}
function closeModal() { document.getElementById('overlay').classList.remove('show'); modalAction = null; }
async function modalOk() { closeModal(); if (modalAction) await modalAction(); }
document.getElementById('overlay').addEventListener('click', e => { if (e.target.id === 'overlay') closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// ── Extensions JS Handlers ───────────────────────────────────────────

function showZip() {
    if (!selected.size) return;
    const paths = [...selected];
    const defaultName = paths.length === 1 ? paths[0].split('/').pop() + '.zip' : 'archivio.zip';
    
    showModal('🗜️ Comprimi (Zip)', `<p>Comprimi ${paths.length} elementi in:</p><input id="zip-input" value="${esc(defaultName)}" style="font-size:14px;">`, async () => {
        const dest = document.getElementById('zip-input').value.trim();
        if (!dest) return;
        termPrint(`🗜️ Creazione archivio ${dest}...`);
        try {
            await api('{{ route("admin.filemanager.zip") }}', { paths, destination: dest });
            termPrint(`✅ Archivio creato.`);
            selected.clear(); await refreshList();
        } catch(e) { termPrint('❌ ' + e.message); }
    });
    setTimeout(() => { const i = document.getElementById('zip-input'); i?.focus(); i?.select(); }, 50);
}

function showUnzip() {
    if (selected.size !== 1) return;
    const path = [...selected][0];
    showModal('📂 Estrai', `<p>Vuoi estrarre <strong>${esc(path.split('/').pop())}</strong> nella cartella corrente?</p>`, async () => {
        termPrint(`📂 Estrazione in corso...`);
        try {
            await api('{{ route("admin.filemanager.unzip") }}', { path });
            termPrint(`✅ File estratto.`);
            selected.clear(); await refreshList();
        } catch(e) { termPrint('❌ ' + e.message); }
    });
}

function showChmod() {
    if (!selected.size) return;
    const paths = [...selected];
    const firstItem = items.find(i => i.path === paths[0]);
    const currentPerms = firstItem ? firstItem.perms : '0755';
    
    showModal('🔐 Cambia Permessi', `<p>Imposta permessi ottali (es. 0755, 0644) per ${paths.length} elementi:</p><input id="chmod-input" value="${currentPerms}" style="font-size:14px;">`, async () => {
        const perms = document.getElementById('chmod-input').value.trim();
        if (!perms) return;
        
        let successCount = 0;
        for (const p of paths) {
            try {
                await api('{{ route("admin.filemanager.chmod") }}', { path: p, permissions: perms });
                successCount++;
            } catch(e) { termPrint(`❌ ${p}: ${e.message}`); }
        }
        if (successCount > 0) {
            termPrint(`✅ Permessi cambiati su ${successCount} file.`);
            selected.clear(); await refreshList();
        }
    });
    setTimeout(() => { const i = document.getElementById('chmod-input'); i?.focus(); i?.select(); }, 50);
}

async function searchFile() {
    const q = document.getElementById('search-input').value.trim();
    if (!q || q.length < 2) {
        // Clear search, reload current dir
        document.getElementById('search-input').value = '';
        await refreshList();
        return;
    }
    
    termPrint(`🔍 Ricerca di '${q}' in ${CWD}...`);
    try {
        const data = await api('{{ route("admin.filemanager.search") }}', { path: CWD, query: q });
        items = data.items;
        renderFiles();
        document.getElementById('status-count').textContent = `${items.length} risultati trovati.`;
        termPrint(`✅ Ricerca completata: ${items.length} risultati.`);
    } catch(e) { termPrint('❌ ' + e.message); }
}

function dbBackup() {
    const cmd = `php artisan db:dump || mysqldump -u root -p --all-databases > backup_$(date +%s).sql`;
    document.getElementById('term-input').value = cmd;
    document.getElementById('term-input').focus();
    termPrint(`🗄️ Premi invio per tentare il backup (puoi modificare il comando prima).`);
}

function openImage(path) {
    const url = '{{ route("admin.filemanager.download") }}?path=' + encodeURIComponent(path);
    showModal('🖼️ Anteprima', `<div style="text-align:center;"><img src="${url}" class="lightbox"></div>`, null);
}

function openPdf(path) {
    const url = '{{ route("admin.filemanager.download") }}?path=' + encodeURIComponent(path);
    showModal('📑 Anteprima PDF', `<iframe src="${url}"></iframe>`, null);
}

function openLog(path) {
    const cmd = `tail -n 200 "${path}"`;
    document.getElementById('term-input').value = cmd;
    execCmd();
    termPrint(`📋 Tail attivato per ${path.split('/').pop()}`);
}

// ── View toggle ───────────────────────────────────────────────────────
function setView(v) { viewMode = v; renderFiles(); }

// ── Tree Sidebar ──────────────────────────────────────────────────────
async function loadTree(parentPath, container) {
    try {
        const dirs = await api('{{ route("admin.filemanager.tree") }}', { path: parentPath });
        container.innerHTML = '';
        dirs.forEach(dir => {
            const wrap  = document.createElement('div');
            const node  = document.createElement('div');
            node.className = 'tree-node';
            node.innerHTML = `<span class="arrow">▶</span>📁 ${esc(dir.name)}`;
            node.title = dir.path;
            node.onclick = (e) => {
                e.stopPropagation();
                navigate(dir.path);
                node.classList.toggle('active');
                if (dir.hasChildren) {
                    const ch = wrap.querySelector('.tree-children');
                    if (!ch.classList.contains('open')) {
                        ch.classList.add('open');
                        node.querySelector('.arrow').classList.add('open');
                        if (!ch.children.length) loadTree(dir.path, ch);
                    } else {
                        ch.classList.remove('open');
                        node.querySelector('.arrow').classList.remove('open');
                    }
                }
            };
            const children = document.createElement('div');
            children.className = 'tree-children';
            wrap.appendChild(node);
            if (dir.hasChildren) wrap.appendChild(children);
            else node.querySelector('.arrow').style.visibility = 'hidden';
            container.appendChild(wrap);
        });
    } catch(e) { console.error(e); }
}

// ── Keyboard Shortcuts ────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    if (e.key === 'Delete' && selected.size > 0) confirmDelete();
    if (e.ctrlKey && e.key === 'c') clipCopy();
    if (e.ctrlKey && e.key === 'x') clipCut();
    if (e.ctrlKey && e.key === 'v') clipPaste();
    if (e.ctrlKey && e.key === 'a') { selected = new Set(items.map(i => i.path)); renderFiles(); updateButtons(); updateStatus(); }
    if (e.key === 'F2' && selected.size === 1) showRename();
    if (e.key === 'F5') refreshList();
});

// ── Resize Sidebar ────────────────────────────────────────────────────
const handle  = document.getElementById('resize-handle');
const sidebar = document.getElementById('sidebar');
let resizing  = false;
handle.addEventListener('mousedown', e => { resizing = true; handle.classList.add('dragging'); e.preventDefault(); });
document.addEventListener('mousemove', e => { if (!resizing) return; const w = e.clientX; if (w > 100 && w < 500) sidebar.style.width = w + 'px'; });
document.addEventListener('mouseup', () => { resizing = false; handle.classList.remove('dragging'); });

// ── Helpers ────────────────────────────────────────────────────────────
function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function fmtSize(b) { if (b < 1024) return b + ' B'; if (b < 1048576) return (b/1024).toFixed(1) + ' KB'; if (b < 1073741824) return (b/1048576).toFixed(1) + ' MB'; return (b/1073741824).toFixed(1) + ' GB'; }
function parentOf(p) { const parts = p.split('/').filter(Boolean); parts.pop(); return parts.length ? '/' + parts.join('/') : '/'; }

// ── Boot ───────────────────────────────────────────────────────────────
(async () => {
    await loadTree('/', document.getElementById('tree-root'));
    await navigate('/');
    termPrint('💡 Scorciatoie: Ctrl+C=Copia, Ctrl+X=Taglia, Ctrl+V=Incolla, Del=Elimina, F2=Rinomina, F5=Aggiorna, Ctrl+A=Seleziona tutto');
})();
</script>
</body>
</html>
