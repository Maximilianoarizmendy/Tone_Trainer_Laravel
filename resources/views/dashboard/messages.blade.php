@extends('layouts.dashboard')
@section('title', 'Mensajes')
@section('page-title', '💬 Mensajes')

@section('styles')
<style>
.messages-layout { display: grid; grid-template-columns: 280px 1fr; gap: 0; height: calc(100vh - 120px); background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
.contacts-panel { border-right: 1px solid var(--border); display: flex; flex-direction: column; }
.contacts-search { padding: 12px; border-bottom: 1px solid var(--border); }
.contacts-search input { width: 100%; padding: 9px 14px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-size: 13px; font-family: 'Poppins', sans-serif; }
.contacts-search input:focus { outline: none; border-color: var(--primary); }
.contacts-list { flex: 1; overflow-y: auto; }
.contact-item { display: flex; align-items: center; gap: 12px; padding: 14px; cursor: pointer; border-bottom: 1px solid #1e1e1e; transition: background .15s; position: relative; }
.contact-item:hover { background: var(--surface2); }
.contact-item.active { background: rgba(255,69,0,.08); border-left: 3px solid var(--primary); }
.contact-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--surface3); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: var(--primary); flex-shrink: 0; }
.contact-name { font-size: 13px; font-weight: 600; color: #fff; }
.contact-last { font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; margin-top: 2px; }
.contact-badge { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: var(--primary); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; }
.chat-panel { display: flex; flex-direction: column; }
.chat-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.chat-header-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--surface3); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: var(--primary); }
.chat-header-name { font-size: 14px; font-weight: 600; color: #fff; }
.chat-header-role { font-size: 11px; color: var(--muted); }
.chat-messages { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 10px; }
.chat-messages::-webkit-scrollbar { width: 3px; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
.msg-bubble { max-width: 70%; padding: 10px 14px; border-radius: 14px; font-size: 13px; line-height: 1.5; position: relative; }
.msg-me    { align-self: flex-end; background: rgba(255,69,0,.15); border: 1px solid rgba(255,69,0,.2); color: var(--text); border-bottom-right-radius: 4px; }
.msg-other { align-self: flex-start; background: var(--surface2); border: 1px solid var(--border); color: var(--text); border-bottom-left-radius: 4px; }
.msg-time { font-size: 10px; color: var(--muted); margin-top: 4px; text-align: right; }
.msg-sender { font-size: 11px; color: var(--primary); font-weight: 600; margin-bottom: 4px; }
.chat-input-area { padding: 14px; border-top: 1px solid var(--border); display: flex; gap: 10px; }
.chat-input { flex: 1; padding: 11px 16px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-size: 14px; font-family: 'Poppins', sans-serif; resize: none; }
.chat-input:focus { outline: none; border-color: var(--primary); }
.btn-send { padding: 11px 20px; background: linear-gradient(90deg, var(--primary), #ff6347); border: none; border-radius: 10px; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; }
.no-chat { flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 12px; color: var(--muted); }
.no-chat .icon { font-size: 48px; }
</style>
@endsection

@section('content')
<div class="messages-layout">
    <div class="contacts-panel">
        <div class="contacts-search">
            <input type="text" id="contactSearch" placeholder="Buscar contacto..." oninput="filterContacts(this.value)">
        </div>
        <div class="contacts-list" id="contactsList"></div>
    </div>
    <div class="chat-panel" id="chatPanel">
        <div class="no-chat" id="noChatState">
            <div class="icon">💬</div>
            <p>Selecciona un contacto para chatear</p>
        </div>
        <div id="chatActive" style="display:none; flex-direction:column; height:100%;">
            <div class="chat-header">
                <div class="chat-header-avatar" id="chatAvatar"></div>
                <div>
                    <div class="chat-header-name" id="chatName"></div>
                    <div class="chat-header-role" id="chatRole"></div>
                </div>
            </div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input-area">
                <textarea class="chat-input" id="msgInput" placeholder="Escribe un mensaje..." rows="1"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"></textarea>
                <button class="btn-send" onclick="sendMessage()">Enviar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const myId = {{ auth()->id() }};
const roleNames = {1:'Usuario',2:'Admin',3:'Nutricionista',4:'Entrenador'};
let contacts = [], currentContact = null, pollInterval = null;

async function loadContacts() {
    const r = await fetch('/api/messages/conversations');
    const d = await r.json();
    if (d.success) { contacts = d.data; renderContacts(contacts); }
}

function renderContacts(list) {
    const ul = document.getElementById('contactsList');
    if (!list.length) { ul.innerHTML = '<p style="padding:16px;color:var(--muted);font-size:13px;">No hay contactos.</p>'; return; }
    ul.innerHTML = list.map(c => `
        <div class="contact-item ${currentContact?.id==c.id?'active':''}" onclick="openChat(${c.id})">
            <div class="contact-avatar">${c.name.charAt(0).toUpperCase()}</div>
            <div style="flex:1;overflow:hidden;">
                <div class="contact-name">${c.name}</div>
                <div class="contact-last">${c.last_message||'(sin mensajes)'}</div>
            </div>
            ${c.unread_count>0?`<span class="contact-badge">${c.unread_count}</span>`:''}
        </div>`).join('');
}

function filterContacts(q) {
    renderContacts(contacts.filter(c => c.name.toLowerCase().includes(q.toLowerCase())));
}

async function openChat(contactId) {
    currentContact = contacts.find(c => c.id==contactId);
    if (!currentContact) return;
    document.getElementById('noChatState').style.display = 'none';
    const ca = document.getElementById('chatActive'); ca.style.display = 'flex';
    document.getElementById('chatAvatar').textContent = currentContact.name.charAt(0).toUpperCase();
    document.getElementById('chatName').textContent   = currentContact.name;
    document.getElementById('chatRole').textContent   = roleNames[currentContact.role]||'Usuario';
    renderContacts(contacts);
    await loadMessages(contactId);
    clearInterval(pollInterval);
    pollInterval = setInterval(() => loadMessages(contactId), 5000);
}

async function loadMessages(contactId) {
    const r = await fetch(`/api/messages/thread?with_user_id=${contactId}`);
    const d = await r.json();
    if (!d.success) return;
    const container = document.getElementById('chatMessages');
    const atBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 50;
    container.innerHTML = d.data.map(m => {
        const isMe = m.sender_id == myId;
        return `<div class="msg-bubble ${isMe?'msg-me':'msg-other'}">
            ${!isMe?`<div class="msg-sender">${m.sender?.name||''}</div>`:''}
            <div>${escapeHtml(m.message)}</div>
            <div class="msg-time">${fmtTime(m.created_at)}</div>
        </div>`;
    }).join('');
    if (atBottom) container.scrollTop = container.scrollHeight;
    loadContacts();
}

async function sendMessage() {
    const input = document.getElementById('msgInput');
    const text  = input.value.trim();
    if (!text || !currentContact) return;
    input.value = '';
    await fetch('/api/messages/send', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({receiver_id: currentContact.id, message: text})
    });
    loadMessages(currentContact.id);
}

function escapeHtml(t) { const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }
function fmtTime(ts)  { const d=new Date(ts); return `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`; }

loadContacts();
</script>
@endsection
