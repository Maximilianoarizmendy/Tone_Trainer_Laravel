@extends('layouts.dashboard')
@section('title', 'Mensajes')
@section('page-title', '💬 Mensajes')

@section('styles')
<style>
/* ═══════════════════════════════════════════════════
   LAYOUT PRINCIPAL
═══════════════════════════════════════════════════ */
.messages-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 0;
    height: calc(100vh - 120px);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}

/* ═══════════════════════════════════════════════════
   PANEL IZQUIERDO — CONTACTOS
═══════════════════════════════════════════════════ */
.contacts-panel {
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    background: var(--surface);
    overflow: hidden;
}

.contacts-header {
    padding: 16px;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    flex-shrink: 0;
}

.contacts-header h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 12px;
}

.contacts-search {
    position: relative;
}

.contacts-search input {
    width: 100%;
    padding: 9px 14px 9px 36px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    font-size: 13px;
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
    transition: border-color .2s;
}

.contacts-search input:focus {
    outline: none;
    border-color: var(--primary);
}

.contacts-search .search-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: var(--muted);
    pointer-events: none;
}

/* Filtros de rol */
.role-filters {
    display: flex;
    gap: 4px;
    padding: 10px 16px;
    border-bottom: 1px solid var(--border);
    overflow-x: auto;
    scrollbar-width: none;
    flex-shrink: 0;
}

.role-filters::-webkit-scrollbar { display: none; }

.role-btn {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid var(--border);
    background: transparent;
    color: var(--muted);
    white-space: nowrap;
    transition: all .15s;
    font-family: 'Poppins', sans-serif;
}

.role-btn:hover { background: var(--surface2); color: var(--text); }
.role-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
}

/* Lista de contactos */
.contacts-list {
    flex: 1;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

.contacts-list::-webkit-scrollbar { width: 3px; }
.contacts-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 16px;
    cursor: pointer;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: background .15s;
    position: relative;
}

.contact-item:hover { background: var(--surface2); }
.contact-item.active {
    background: rgba(255,69,0,.08);
    border-left: 3px solid var(--primary);
}

.contact-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #ff6347);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    position: relative;
}

.contact-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.contact-info { flex: 1; overflow: hidden; min-width: 0; }
.contact-name { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.contact-preview { font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }

.contact-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
.contact-time { font-size: 10px; color: var(--muted); }

.unread-badge {
    background: var(--primary);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
    animation: badgePop .2s ease;
}

@keyframes badgePop {
    0%  { transform: scale(0.6); }
    70% { transform: scale(1.15); }
    100%{ transform: scale(1); }
}

/* Etiqueta de rol del contacto */
.role-tag {
    font-size: 9px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.role-tag.r1 { background: rgba(99,179,237,.15); color: #63b3ed; }
.role-tag.r2 { background: rgba(236,201,75,.15); color: #ecc94b; }
.role-tag.r3 { background: rgba(104,211,145,.15); color: #68d391; }
.role-tag.r4 { background: rgba(245,101,101,.15); color: #f56565; }

/* ═══════════════════════════════════════════════════
   PANEL DERECHO — CHAT
═══════════════════════════════════════════════════ */
.chat-panel {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: var(--surface);
}

/* Estado vacío */
.no-chat {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 14px;
    color: var(--muted);
    padding: 40px;
}

.no-chat .icon {
    font-size: 56px;
    opacity: .6;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}

.no-chat h3 { font-size: 18px; font-weight: 600; color: var(--text); margin: 0; }
.no-chat p  { font-size: 13px; color: var(--muted); margin: 0; text-align: center; }

/* Header del chat */
.chat-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--surface);
    flex-shrink: 0;
}

.chat-header-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #ff6347);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}

.chat-header-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.chat-header-info { flex: 1; }
.chat-header-name { font-size: 14px; font-weight: 600; color: var(--text); }
.chat-header-sub  { font-size: 11px; color: var(--muted); margin-top: 1px; }

/* Indicador de conexión / polling */
.chat-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #68d391;
    animation: pulse-green 2s ease infinite;
    flex-shrink: 0;
}

@keyframes pulse-green {
    0%, 100% { box-shadow: 0 0 0 0 rgba(104,211,145,.4); }
    50%       { box-shadow: 0 0 0 4px rgba(104,211,145,0); }
}

/* Área de mensajes */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

.chat-messages::-webkit-scrollbar { width: 3px; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

/* Burbujas */
.msg-bubble {
    max-width: 68%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.55;
    word-break: break-word;
    animation: bubbleIn .18s ease-out;
}

@keyframes bubbleIn {
    from { opacity: 0; transform: scale(.93) translateY(4px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.msg-me {
    align-self: flex-end;
    background: linear-gradient(135deg, rgba(255,69,0,.22), rgba(255,99,71,.15));
    border: 1px solid rgba(255,69,0,.25);
    color: var(--text);
    border-bottom-right-radius: 4px;
}

.msg-me.sending { opacity: .65; }

.msg-other {
    align-self: flex-start;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text);
    border-bottom-left-radius: 4px;
}

.msg-time {
    font-size: 10px;
    color: var(--muted);
    margin-top: 5px;
    text-align: right;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

.msg-other .msg-time { justify-content: flex-start; }

/* Separadores de fecha */
.date-sep {
    align-self: center;
    font-size: 11px;
    color: var(--muted);
    background: var(--surface2);
    border: 1px solid var(--border);
    padding: 3px 12px;
    border-radius: 20px;
    margin: 6px 0;
}

/* Área de entrada */
.chat-input-area {
    padding: 14px 16px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 10px;
    align-items: flex-end;
    background: var(--surface);
    flex-shrink: 0;
}

.chat-input {
    flex: 1;
    padding: 10px 16px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 12px;
    color: var(--text);
    font-size: 13px;
    font-family: 'Poppins', sans-serif;
    resize: none;
    max-height: 120px;
    min-height: 40px;
    line-height: 1.5;
    transition: border-color .2s;
    overflow-y: auto;
}

.chat-input:focus { outline: none; border-color: var(--primary); }

.btn-send {
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--primary), #ff6347);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .2s, transform .1s;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    gap: 6px;
    height: 40px;
    flex-shrink: 0;
}

.btn-send:hover  { opacity: .9; }
.btn-send:active { transform: scale(.97); }
.btn-send:disabled { opacity: .5; cursor: not-allowed; }

/* Empty state contactos */
.contacts-empty {
    padding: 20px 16px;
    text-align: center;
    color: var(--muted);
    font-size: 13px;
}

/* Loader */
.chat-loader {
    align-self: center;
    font-size: 12px;
    color: var(--muted);
    padding: 8px;
    display: flex;
    gap: 4px;
    align-items: center;
}

.dot-anim { display: inline-flex; gap: 3px; }
.dot-anim span {
    width: 5px; height: 5px;
    background: var(--primary);
    border-radius: 50%;
    animation: dotBounce 1s infinite;
}
.dot-anim span:nth-child(2) { animation-delay: .15s; }
.dot-anim span:nth-child(3) { animation-delay: .30s; }

@keyframes dotBounce {
    0%, 80%, 100% { transform: translateY(0); }
    40%           { transform: translateY(-5px); }
}

/* ═══════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════ */
@media (max-width: 768px) {
    .messages-layout { grid-template-columns: 1fr; }
    .contacts-panel  { display: none; }
    .contacts-panel.mobile-open { display: flex; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 200; }
}

/* Estilos para Modal de Agregar Contacto y Solicitudes */
@keyframes scaleUp {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.btn-add-contact:hover {
    transform: scale(1.1);
    opacity: 0.9;
}

.pending-request-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-bottom: 1px solid var(--border);
}

.pending-request-info {
    flex: 1;
    min-width: 0;
}

.pending-request-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pending-request-email {
    font-size: 10px;
    color: var(--muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pending-request-actions {
    display: flex;
    gap: 6px;
}

.btn-action-accept, .btn-action-decline {
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    transition: background 0.15s;
}

.btn-action-accept {
    background: #48bb78;
    color: #fff;
}
.btn-action-accept:hover {
    background: #38a169;
}

.btn-action-decline {
    background: #f56565;
    color: #fff;
}
.btn-action-decline:hover {
    background: #e53e3e;
}

/* Modal Search Items */
.search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    transition: background 0.15s;
}

.search-result-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #ff6347);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.search-result-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.search-result-info {
    flex: 1;
    min-width: 0;
}

.search-result-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.search-result-role {
    font-size: 9px;
    color: var(--muted);
}

.btn-request-send {
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    background: var(--primary);
    color: #fff;
    transition: opacity 0.15s;
}
.btn-request-send:hover {
    opacity: 0.9;
}
.btn-request-send:disabled {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--muted);
    cursor: not-allowed;
}
</style>
@endsection

@section('content')
<div class="messages-layout" id="messagesLayout">

    <!-- ═══════ PANEL IZQUIERDO ═══════ -->
    <div class="contacts-panel" id="contactsPanel">

        <div class="contacts-header">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h3 style="margin:0;">💬 Mensajería</h3>
                <button class="btn-add-contact" onclick="openAddContactModal()" style="background:var(--primary); border:none; border-radius:50%; width:28px; height:28px; display:flex; align-items:center; justify-content:center; color:#fff; cursor:pointer; font-size:16px; font-weight:700; transition:transform 0.15s;" title="Agregar Contacto">+</button>
            </div>
            <div class="contacts-search">
                <span class="search-icon">🔍</span>
                <input type="text" id="contactSearch" placeholder="Buscar persona..." oninput="filterContacts(this.value)">
            </div>
        </div>

        <!-- Solicitudes Pendientes -->
        <div class="pending-requests-section" id="pendingRequestsSection" style="display: none;">
            <div class="pending-requests-header" onclick="togglePendingRequests()" style="padding:10px 16px; background:rgba(255, 69, 0, 0.1); border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; cursor:pointer; font-size:12px; font-weight:600; color:var(--text);">
                <span>📩 Solicitudes pendientes (<span id="pendingCount">0</span>)</span>
                <span id="pendingToggleIcon" style="transition:transform 0.2s; display:inline-block;">▼</span>
            </div>
            <div class="pending-requests-list" id="pendingRequestsList" style="display: none; background:var(--surface); border-bottom:1px solid var(--border); max-height:200px; overflow-y:auto;">
                <!-- Se cargará dinámicamente -->
            </div>
        </div>

        <!-- Filtros por rol -->
        <div class="role-filters">
            <button class="role-btn active" onclick="setRoleFilter(0, this)">Todos</button>
            <button class="role-btn" onclick="setRoleFilter(1, this)">👤 Usuarios</button>
            <button class="role-btn" onclick="setRoleFilter(2, this)">🛡 Admin</button>
            <button class="role-btn" onclick="setRoleFilter(3, this)">🥗 Nutri</button>
            <button class="role-btn" onclick="setRoleFilter(4, this)">💪 Trainer</button>
        </div>

        <div class="contacts-list" id="contactsList">
            <div class="contacts-empty">Cargando contactos...</div>
        </div>
    </div>

    <!-- ═══════ PANEL DERECHO ═══════ -->
    <div class="chat-panel" id="chatPanel">

        <!-- Estado vacío -->
        <div class="no-chat" id="noChatState">
            <div class="icon">💬</div>
            <h3>Bienvenido al Chat</h3>
            <p>Selecciona un contacto de la lista<br>para comenzar una conversación.</p>
        </div>

        <!-- Chat activo -->
        <div id="chatActive" style="display:none; flex-direction:column; height:100%;">

            <!-- Header -->
            <div class="chat-header">
                <div class="chat-header-avatar" id="chatAvatar">?</div>
                <div class="chat-header-info">
                    <div class="chat-header-name" id="chatName"></div>
                    <div class="chat-header-sub" id="chatSub"></div>
                </div>
                <div class="chat-status-dot" id="statusDot" title="Conectado"></div>
            </div>

            <!-- Mensajes -->
            <div class="chat-messages" id="chatMessages"></div>

            <!-- Entrada -->
            <div class="chat-input-area">
                <textarea
                    class="chat-input"
                    id="msgInput"
                    placeholder="Escribe un mensaje... (Enter para enviar, Shift+Enter nueva línea)"
                    rows="1"
                    onkeydown="handleInputKey(event)"
                    oninput="autoResize(this)"
                ></textarea>
                <button class="btn-send" id="btnSend" onclick="sendMessage()">
                    Enviar <span>➤</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Contacto -->
<div class="chat-modal" id="addContactModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:1000; backdrop-filter:blur(4px);">
    <div class="chat-modal-content" style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); width:450px; max-width:90%; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.5); display:flex; flex-direction:column; animation: scaleUp 0.25s ease;">
        <div class="chat-modal-header" style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--text);">Agregar Contacto</h3>
            <button class="modal-close" onclick="closeAddContactModal()" style="background:transparent; border:none; color:var(--muted); font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        <div class="chat-modal-body" style="padding:20px;">
            <div class="contacts-search" style="margin-bottom:15px; position:relative;">
                <span class="search-icon" style="position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:13px; color:var(--muted);">🔍</span>
                <input type="text" id="modalSearchInput" placeholder="Buscar por nombre o correo..." oninput="searchUsersToContact(this.value)" style="width:100%; padding:9px 14px 9px 36px; background:var(--surface2); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13px; font-family:'Poppins',sans-serif; box-sizing:border-box;">
            </div>
            <div class="search-results" id="modalSearchResults" style="max-height:250px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
                <div class="results-empty" style="text-align:center; color:var(--muted); font-size:12px; padding:20px;">Escribe el nombre o correo de la persona que deseas agregar.</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
/* ═══════════════════════════════════════════════════
   ESTADO GLOBAL
   ═══════════════════════════════════════════════════ */
const MY_ID = {{ auth()->id() }};
const ROLE_NAMES = { 1:'Usuario', 2:'Administrador', 3:'Nutricionista', 4:'Entrenador' };
const ROLE_ICONS = { 1:'👤', 2:'🛡️', 3:'🥗', 4:'💪' };

let allContacts = @json($contacts->values());   // Lista inicial (blade pass-through)
let filteredContacts = [...allContacts];
let currentRoleFilter = 0;
let currentSearchQuery = '';

let currentContact = null;
let lastMsgTimestamp = null;   // Timestamp del último mensaje visto (para polling eficiente)
let lastMsgId = null;          // ID del último mensaje visto
let pollTimer = null;
let unreadByContact = {};      // { contact_id: count }
let isLoading = false;

/* ═══════════════════════════════════════════════════
   INIT
   ═══════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    // Cargar contactos vía API para obtener unread_count actualizado
    loadConversations();
    loadPendingRequests();

    // Polling global de badges y solicitudes (cada 8s) cuando no hay chat abierto
    setInterval(() => {
        if (!currentContact) {
            loadConversations();
        }
        loadPendingRequests();
    }, 8000);
});

/* ═══════════════════════════════════════════════════
   CARGAR CONVERSACIONES (API)
   ═══════════════════════════════════════════════════ */
async function loadConversations() {
    try {
        const res = await fetch('/api/messages/conversations');
        const data = await res.json();
        if (!data.success) return;

        unreadByContact = {};
        allContacts = data.data || [];
        allContacts.forEach(c => {
            unreadByContact[c.id] = c.unread_count || 0;
        });

        applyFilters();
    } catch(e) {
        console.warn('No se pudo cargar conversaciones:', e);
    }
}

/* ═══════════════════════════════════════════════════
   FILTROS Y BÚSQUEDA
   ═══════════════════════════════════════════════════ */
function setRoleFilter(role, btn) {
    currentRoleFilter = role;
    document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    applyFilters();
}

function filterContacts(q) {
    currentSearchQuery = q.toLowerCase();
    applyFilters();
}

function applyFilters() {
    filteredContacts = allContacts.filter(c => {
        const matchRole = currentRoleFilter === 0 || c.role === currentRoleFilter;
        const matchQ    = !currentSearchQuery || c.name.toLowerCase().includes(currentSearchQuery) || (c.email && c.email.toLowerCase().includes(currentSearchQuery));
        return matchRole && matchQ;
    });
    renderContacts();
}

/* ═══════════════════════════════════════════════════
   RENDER LISTA DE CONTACTOS
   ═══════════════════════════════════════════════════ */
function renderContacts() {
    const ul = document.getElementById('contactsList');

    // Ordenar: primero los que tienen mensajes nuevos, luego por último mensaje
    const sorted = [...filteredContacts].sort((a, b) => {
        const ua = unreadByContact[a.id] || 0;
        const ub = unreadByContact[b.id] || 0;
        if (ua !== ub) return ub - ua;   // Primero los con no leídos
        const ta = a.last_message_time ? new Date(a.last_message_time) : 0;
        const tb = b.last_message_time ? new Date(b.last_message_time) : 0;
        return tb - ta;
    });

    if (!sorted.length) {
        ul.innerHTML = '<div class="contacts-empty">No se encontraron contactos.</div>';
        return;
    }

    ul.innerHTML = sorted.map(c => {
        const isActive  = currentContact?.id === c.id;
        const initials  = c.name.charAt(0).toUpperCase();
        const unread    = unreadByContact[c.id] || 0;
        const preview   = c.last_message ? escHtml(c.last_message.substring(0, 45)) : '<em>Sin mensajes aún</em>';
        const timeStr   = c.last_message_time ? fmtRelTime(c.last_message_time) : '';
        const roleTag   = `<span class="role-tag r${c.role}">${ROLE_ICONS[c.role] || ''} ${ROLE_NAMES[c.role] || 'Usuario'}</span>`;

        return `
        <div class="contact-item ${isActive ? 'active' : ''}" id="ci-${c.id}" onclick="openChat(${c.id})">
            <div class="contact-avatar">
                ${c.profile_photo
                    ? `<img src="${c.profile_photo}" alt="${escHtml(c.name)}" onerror="this.parentElement.textContent='${initials}'">`
                    : initials
                }
            </div>
            <div class="contact-info">
                <div class="contact-name">${escHtml(c.name)}</div>
                <div style="margin-bottom:3px">${roleTag}</div>
                <div class="contact-preview">${preview}</div>
            </div>
            <div class="contact-meta">
                ${timeStr ? `<div class="contact-time">${timeStr}</div>` : ''}
                ${unread > 0 ? `<div class="unread-badge" id="badge-${c.id}">${unread}</div>` : `<div style="height:18px" id="badge-${c.id}"></div>`}
            </div>
        </div>`;
    }).join('');
}

/* ═══════════════════════════════════════════════════
   ABRIR CHAT
   ═══════════════════════════════════════════════════ */
async function openChat(contactId) {
    currentContact = allContacts.find(c => c.id === contactId);
    if (!currentContact) return;

    // UI
    document.getElementById('noChatState').style.display = 'none';
    const chatActive = document.getElementById('chatActive');
    chatActive.style.display = 'flex';

    const av = document.getElementById('chatAvatar');
    if (currentContact.profile_photo) {
        av.innerHTML = `<img src="${currentContact.profile_photo}" alt="${escHtml(currentContact.name)}" onerror="this.parentElement.textContent='${currentContact.name.charAt(0).toUpperCase()}'">`;
    } else {
        av.textContent = currentContact.name.charAt(0).toUpperCase();
    }

    document.getElementById('chatName').textContent = currentContact.name;
    document.getElementById('chatSub').innerHTML = `
        <span class="role-tag r${currentContact.role}" style="display:inline;">
            ${ROLE_ICONS[currentContact.role] || ''} ${ROLE_NAMES[currentContact.role] || 'Usuario'}
        </span>`;

    renderContacts(); // Actualizar active state

    // Limpiar polling anterior
    stopPolling();
    lastMsgTimestamp = null;
    lastMsgId = null;

    // Cargar historial completo inicial
    await loadThread(contactId);

    // Limpiar badge
    unreadByContact[contactId] = 0;
    const badge = document.getElementById(`badge-${contactId}`);
    if (badge) badge.innerHTML = '';

    // Iniciar polling cada 1.5s
    startPolling(contactId);

    // Focus input
    document.getElementById('msgInput').focus();
}

/* ═══════════════════════════════════════════════════
   CARGAR HILO COMPLETO (primera vez)
   ═══════════════════════════════════════════════════ */
async function loadThread(contactId) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '<div class="chat-loader"><div class="dot-anim"><span></span><span></span><span></span></div></div>';

    try {
        const res  = await fetch(`/api/messages/thread?with_user_id=${contactId}`);
        const data = await res.json();
        if (!data.success) return;

        renderMessages(data.data, true);

        // Guardar timestamp y ID del último mensaje para polling
        if (data.data.length > 0) {
            lastMsgTimestamp = data.data[data.data.length - 1].created_at;
            lastMsgId = data.data[data.data.length - 1].id;
        }
    } catch(e) {
        container.innerHTML = '<div class="chat-loader" style="color:var(--primary)">Error al cargar mensajes.</div>';
    }
}

/* ═══════════════════════════════════════════════════
   POLLING EFICIENTE (solo mensajes nuevos)
   ═══════════════════════════════════════════════════ */
function startPolling(contactId) {
    pollTimer = setInterval(() => doPoll(contactId), 1500);
}

function stopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

async function doPoll(contactId) {
    if (document.hidden) return; // No hacer polling si la tab está oculta

    const afterParam = lastMsgId 
        ? `&after_id=${lastMsgId}` 
        : (lastMsgTimestamp ? `&after=${encodeURIComponent(lastMsgTimestamp)}` : '');

    try {
        const res  = await fetch(`/api/messages/poll?with_user_id=${contactId}${afterParam}`);
        const data = await res.json();
        if (!data.success) return;

        // Actualizar timestamp del servidor
        if (data.server_time) lastMsgTimestamp = data.server_time;

        // Si hay mensajes nuevos, agregarlos al DOM
        if (data.messages && data.messages.length > 0) {
            appendMessages(data.messages);
            lastMsgId = data.messages[data.messages.length - 1].id;
            lastMsgTimestamp = data.messages[data.messages.length - 1].created_at;
        }

        // Actualizar badges de no leídos de otros contactos
        if (data.unread_by_contact) {
            let changed = false;
            Object.entries(data.unread_by_contact).forEach(([cid, cnt]) => {
                const id = parseInt(cid);
                if (id !== contactId) {
                    const prev = unreadByContact[id] || 0;
                    unreadByContact[id] = cnt;
                    if (prev !== cnt) changed = true;
                }
            });
            if (changed) renderContacts();
        }

        // Indicador verde estable
        document.getElementById('statusDot')?.setAttribute('title', 'En línea');

    } catch(e) {
        // Silencioso — mostrar indicador rojo temporal
        const dot = document.getElementById('statusDot');
        if (dot) dot.style.background = '#f56565';
        setTimeout(() => { if(dot) dot.style.background = '#68d391'; }, 3000);
    }
}

/* ═══════════════════════════════════════════════════
   RENDER DE MENSAJES
   ═══════════════════════════════════════════════════ */
function renderMessages(messages, scrollToBottom = true) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '';

    let lastDate = null;
    messages.forEach(m => {
        // Separador de fecha
        const msgDate = new Date(m.created_at).toLocaleDateString('es-MX', { day:'2-digit', month:'long', year:'numeric' });
        if (msgDate !== lastDate) {
            const sep = document.createElement('div');
            sep.className = 'date-sep';
            sep.textContent = msgDate;
            container.appendChild(sep);
            lastDate = msgDate;
        }
        
        // Evitar duplicados
        if (!container.querySelector(`.msg-bubble[data-id="${m.id}"]`)) {
            container.appendChild(makeBubble(m));
        }
    });

    if (scrollToBottom) container.scrollTop = container.scrollHeight;
}

function appendMessages(messages) {
    const container = document.getElementById('chatMessages');
    const atBottom  = container.scrollHeight - container.scrollTop <= container.clientHeight + 80;

    messages.forEach(m => {
        // Evitar duplicados
        if (container.querySelector(`.msg-bubble[data-id="${m.id}"]`)) {
            return;
        }

        // Quitar temp bubble si el mensaje es mío
        if (m.sender_id === MY_ID) {
            const temps = container.querySelectorAll('.msg-bubble.msg-me.sending');
            if (temps.length > 0) temps[temps.length - 1].remove();
        }
        container.appendChild(makeBubble(m));
    });

    if (atBottom) container.scrollTop = container.scrollHeight;

    // Actualizar preview en lista de contactos
    const last = messages[messages.length - 1];
    const c = allContacts.find(x => x.id === currentContact?.id);
    if (c) { c.last_message = last.message; c.last_message_time = last.created_at; }
}

async function loadPendingRequests() {
    try {
        const res = await fetch('/api/messages/requests/pending');
        const data = await res.json();
        if (!data.success) return;

        const container = document.getElementById('pendingRequestsList');
        const section = document.getElementById('pendingRequestsSection');
        const countSpan = document.getElementById('pendingCount');

        const requests = data.data || [];
        countSpan.textContent = requests.length;

        if (requests.length > 0) {
            section.style.display = 'block';
            container.innerHTML = requests.map(r => {
                const sender = r.sender;
                const initials = sender.name.charAt(0).toUpperCase();
                const avatarHtml = sender.profile_photo
                    ? `<img src="${sender.profile_photo}" alt="${escHtml(sender.name)}" onerror="this.parentElement.textContent='${initials}'">`
                    : initials;

                return `
                <div class="pending-request-item" id="req-item-${r.id}">
                    <div class="search-result-avatar" style="width:30px;height:30px;font-size:12px;">${avatarHtml}</div>
                    <div class="pending-request-info">
                        <div class="pending-request-name">${escHtml(sender.name)}</div>
                        <div class="pending-request-email">${ROLE_ICONS[sender.role]} ${ROLE_NAMES[sender.role]}</div>
                    </div>
                    <div class="pending-request-actions">
                        <button class="btn-action-accept" onclick="acceptContactRequest(${r.id})">✓</button>
                        <button class="btn-action-decline" onclick="declineContactRequest(${r.id})">×</button>
                    </div>
                </div>`;
            }).join('');
        } else {
            section.style.display = 'none';
            container.style.display = 'none';
            document.getElementById('pendingToggleIcon').style.transform = '';
            isPendingExpanded = false;
        }
    } catch (e) {
        console.warn('Error al cargar solicitudes pendientes:', e);
    }
}

let isPendingExpanded = false;
function togglePendingRequests() {
    const list = document.getElementById('pendingRequestsList');
    const icon = document.getElementById('pendingToggleIcon');
    isPendingExpanded = !isPendingExpanded;
    if (isPendingExpanded) {
        list.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        list.style.display = 'none';
        icon.style.transform = '';
    }
}

async function acceptContactRequest(reqId) {
    try {
        const res = await fetch(`/api/messages/requests/${reqId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        const data = await res.json();
        if (data.success) {
            const item = document.getElementById(`req-item-${reqId}`);
            if (item) item.remove();
            
            await loadPendingRequests();
            await loadConversations();
        }
    } catch (e) {
        console.error(e);
        alert('Error al aceptar solicitud.');
    }
}

async function declineContactRequest(reqId) {
    try {
        const res = await fetch(`/api/messages/requests/${reqId}/decline`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        const data = await res.json();
        if (data.success) {
            const item = document.getElementById(`req-item-${reqId}`);
            if (item) item.remove();
            await loadPendingRequests();
        }
    } catch (e) {
        console.error(e);
        alert('Error al declinar solicitud.');
    }
}

function openAddContactModal() {
    const modal = document.getElementById('addContactModal');
    modal.style.display = 'flex';
    document.getElementById('modalSearchInput').value = '';
    document.getElementById('modalSearchResults').innerHTML = '<div class="results-empty" style="text-align:center; color:var(--muted); font-size:12px; padding:20px;">Escribe el nombre o correo de la persona que deseas agregar.</div>';
    document.getElementById('modalSearchInput').focus();
}

function closeAddContactModal() {
    document.getElementById('addContactModal').style.display = 'none';
}

window.addEventListener('click', (e) => {
    const modal = document.getElementById('addContactModal');
    if (e.target === modal) {
        closeAddContactModal();
    }
});

let searchTimeout = null;
function searchUsersToContact(query) {
    clearTimeout(searchTimeout);
    const container = document.getElementById('modalSearchResults');
    
    if (!query.trim()) {
        container.innerHTML = '<div class="results-empty" style="text-align:center; color:var(--muted); font-size:12px; padding:20px;">Escribe el nombre o correo de la persona que deseas agregar.</div>';
        return;
    }

    container.innerHTML = '<div class="chat-loader" style="justify-content:center;"><div class="dot-anim"><span></span><span></span><span></span></div></div>';

    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/messages/contacts/search?q=${encodeURIComponent(query)}`);
            const data = await res.json();
            if (!data.success) {
                container.innerHTML = '<div class="results-empty" style="text-align:center; color:var(--primary); font-size:12px; padding:20px;">Error al buscar usuarios.</div>';
                return;
            }

            const users = data.data || [];
            if (users.length === 0) {
                container.innerHTML = '<div class="results-empty" style="text-align:center; color:var(--muted); font-size:12px; padding:20px;">No se encontraron usuarios activos con ese nombre o correo.</div>';
                return;
            }

            container.innerHTML = users.map(u => {
                const initials = u.name.charAt(0).toUpperCase();
                const avatarHtml = u.profile_photo
                    ? `<img src="${u.profile_photo}" alt="${escHtml(u.name)}" onerror="this.parentElement.textContent='${initials}'">`
                    : initials;

                let actionBtn = '';
                if (u.request_status === 'accepted') {
                    actionBtn = '<button class="btn-request-send" disabled>Ya es contacto</button>';
                } else if (u.request_status === 'pending') {
                    if (u.is_sender) {
                        actionBtn = '<button class="btn-request-send" disabled style="background:var(--surface); border:1px solid var(--border); color:var(--muted);">Pendiente</button>';
                    } else {
                        actionBtn = `<button class="btn-request-send" onclick="acceptContactRequest(${u.request_id}).then(() => { searchUsersToContact('${query.replace(/'/g, "\\'")}'); })" style="background:#48bb78;">Aceptar</button>`;
                    }
                } else {
                    actionBtn = `<button class="btn-request-send" id="btn-add-${u.id}" onclick="sendContactRequest(${u.id})">Agregar</button>`;
                }

                return `
                <div class="search-result-item">
                    <div class="search-result-avatar">${avatarHtml}</div>
                    <div class="search-result-info">
                        <div class="search-result-name">${escHtml(u.name)}</div>
                        <div class="search-result-role">${ROLE_ICONS[u.role] || ''} ${ROLE_NAMES[u.role] || ''}</div>
                    </div>
                    <div class="search-result-actions">
                        ${actionBtn}
                    </div>
                </div>`;
            }).join('');

        } catch (e) {
            container.innerHTML = '<div class="results-empty" style="text-align:center; color:var(--primary); font-size:12px; padding:20px;">Error de red al buscar.</div>';
        }
    }, 400);
}

async function sendContactRequest(userId) {
    const btn = document.getElementById(`btn-add-${userId}`);
    if (btn) btn.disabled = true;
    try {
        const res = await fetch('/api/messages/requests/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ receiver_id: userId })
        });
        const data = await res.json();
        if (data.success) {
            if (btn) {
                btn.textContent = 'Enviado';
                btn.style.background = 'var(--surface)';
                btn.style.border = '1px solid var(--border)';
                btn.style.color = 'var(--muted)';
            }
        } else {
            alert(data.message || 'Error al enviar solicitud.');
            if (btn) btn.disabled = false;
        }
    } catch (e) {
        alert('Error al enviar solicitud.');
        if (btn) btn.disabled = false;
    }
}

function makeBubble(m) {
    const isMe = m.sender_id === MY_ID;
    const div  = document.createElement('div');
    div.className = `msg-bubble ${isMe ? 'msg-me' : 'msg-other'}`;
    div.dataset.id = m.id;

    const timeStr = fmtTime(m.created_at);
    const checkMark = isMe ? (m.is_read ? ' ✓✓' : ' ✓') : '';

    div.innerHTML = `
        <div>${escHtml(m.message)}</div>
        <div class="msg-time">${timeStr}${checkMark ? `<span style="color:var(--primary);font-size:10px">${checkMark}</span>` : ''}</div>
    `;
    return div;
}

/* ═══════════════════════════════════════════════════
   ENVIAR MENSAJE
   ═══════════════════════════════════════════════════ */
async function sendMessage() {
    const input = document.getElementById('msgInput');
    const text  = input.value.trim();
    if (!text || !currentContact) return;

    const btn = document.getElementById('btnSend');
    btn.disabled = true;
    input.value  = '';
    input.style.height = '';

    // Optimistic UI — burbuja temporal
    const container = document.getElementById('chatMessages');
    const tempBubble = document.createElement('div');
    tempBubble.className = 'msg-bubble msg-me sending';
    tempBubble.innerHTML = `<div>${escHtml(text)}</div><div class="msg-time">enviando...</div>`;
    container.appendChild(tempBubble);
    container.scrollTop = container.scrollHeight;

    try {
        const res  = await fetch('/api/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ receiver_id: currentContact.id, message: text }),
        });
        const data = await res.json();

        if (data.success && data.message) {
            // Reemplazar burbuja temporal con la real
            tempBubble.remove();
            container.appendChild(makeBubble(data.message));
            container.scrollTop = container.scrollHeight;

            // Actualizar timestamp y ID de polling
            lastMsgTimestamp = data.message.created_at;
            lastMsgId = data.message.id;

            // Actualizar preview
            const c = allContacts.find(x => x.id === currentContact.id);
            if (c) { c.last_message = text; c.last_message_time = data.message.created_at; }
        }
    } catch(e) {
        tempBubble.style.opacity = '0.4';
        tempBubble.querySelector('.msg-time').textContent = '⚠ Error al enviar';
    }

    btn.disabled = false;
    input.focus();
}

/* ═══════════════════════════════════════════════════
   UTILIDADES
═══════════════════════════════════════════════════ */
function handleInputKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

function autoResize(el) {
    el.style.height = '';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function escHtml(t) {
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}

function fmtTime(ts) {
    const d = new Date(ts);
    return `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}

function fmtRelTime(ts) {
    const d   = new Date(ts);
    const now = new Date();
    const diff = Math.floor((now - d) / 1000);

    if (diff < 60)       return 'ahora';
    if (diff < 3600)     return `${Math.floor(diff/60)}m`;
    if (diff < 86400)    return `${Math.floor(diff/3600)}h`;
    if (diff < 604800)   return d.toLocaleDateString('es-MX', { weekday:'short' });
    return d.toLocaleDateString('es-MX', { day:'2-digit', month:'short' });
}

/* ═══════════════════════════════════════════════════
   RENDER INICIAL
═══════════════════════════════════════════════════ */
renderContacts();
</script>
@endsection
