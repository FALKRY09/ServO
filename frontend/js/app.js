import { api, download } from './api/client.js';
import { renderShell, setActiveRoute } from './components/layout.js';
import { canAccess, navigate, setRoutes, startRouter } from './router.js';
import { escapeHtml, showFlash } from './utils/dom.js';

const $ = s => document.querySelector(s);
let user = null;
let ticketModal = null;
let routerStarted = false;
const routes = [
  {path:'/dashboard',label:'Dashboard'}, {path:'/tickets',label:'Tiket'},
  {path:'/notifications',label:'Notifikasi'}, {path:'/profile',label:'Profil'},
  {path:'/users',label:'Pengguna',roles:['Admin']}, {path:'/departments',label:'Departemen',roles:['Admin']},
  {path:'/categories',label:'Kategori',roles:['Admin']}, {path:'/priorities',label:'Prioritas',roles:['Admin']},
  {path:'/sla-policies',label:'SLA',roles:['Admin']}, {path:'/reports',label:'Laporan',roles:['Admin','Koordinator']},
  {path:'/activity-log',label:'Activity Log',roles:['Admin']}
];

$('#loginForm').addEventListener('submit', async e => {
  e.preventDefault();
  const button=e.target.querySelector('button'); button.disabled=true; $('#loginError').textContent='';
  try { const data=await api('/auth/login',{method:'POST',body:JSON.stringify({email:$('#email').value.trim(),password:$('#password').value})});
    if(!data.token) throw new Error('Token autentikasi tidak diterima.');
    sessionStorage.setItem('servo_token',data.token); await boot('/dashboard');
  } catch(error) { console.error('[ServO Auth] login gagal',error); sessionStorage.removeItem('servo_token'); $('#loginError').textContent=error.message; }
  finally { button.disabled=false; }
});
$('#logoutButton').addEventListener('click',async()=>{try{await api('/auth/logout',{method:'POST'});}finally{sessionStorage.removeItem('servo_token');location.hash='';location.reload();}});
$('#newTicketButton').addEventListener('click',()=>ticketModal?.show());
$('#ticketForm').addEventListener('submit',async e=>{e.preventDefault();try{await api('/tickets',{method:'POST',body:JSON.stringify({title:$('#ticketTitle').value,description:$('#ticketDescription').value,category_id:Number($('#ticketCategory').value),priority_id:Number($('#ticketPriority').value)})});ticketModal?.hide();showFlash('Tiket berhasil dibuat.','success');navigate('/tickets');}catch(error){showFlash(error.message);}});
$('#ticketSearch').addEventListener('input',()=>renderPage(location.hash.replace(/^#/,'')||'/tickets'));

async function boot(target=null) {
  console.info('[ServO Auth] boot', {hasToken:Boolean(sessionStorage.getItem('servo_token'))});
  try {
    user=await api('/auth/me'); if(!user?.id) throw new Error('Profil pengguna tidak valid.');
    $('#loginView').classList.add('d-none'); $('#appView').classList.remove('d-none'); $('#logoutButton').classList.remove('d-none');
    $('#userInfo').textContent=user.name||'User'; $('.role-text').textContent=user.role?.name||'User';
    ticketModal=window.bootstrap?.Modal?new bootstrap.Modal($('#ticketModal')):{show(){},hide(){}};
    setRoutes(user,routes); renderShell(user,routes);
    if(!routerStarted){startRouter(renderPage);routerStarted=true;}
    if(target) navigate(target,true); else await renderPage(location.hash.replace(/^#/,'')||'/dashboard');
  } catch(error) { console.error('[ServO Auth] boot gagal',error); $('#loginView').classList.remove('d-none'); $('#appView').classList.add('d-none'); $('#loginError').textContent=error.message; if(error.status===401)sessionStorage.removeItem('servo_token'); }
}

async function renderPage(path) {
  if(path==='/404') return render404();
  if(!canAccess(path)&&!path.startsWith('/tickets/')){navigate('/404',true);return;}
  setActiveRoute(path.startsWith('/tickets')?'/tickets':path); $('#pageTitle').textContent=routes.find(r=>r.path===path)?.label||(path.startsWith('/tickets/')?'Detail Tiket':'Halaman'); const detail=path.startsWith('/tickets/'); const contentTitle=document.querySelector('#ticketList')?.closest('.card')?.querySelector('h2'); if(contentTitle) contentTitle.textContent=detail?'Detail Tiket':(path==='/tickets'?'Daftar Tiket':'Tiket terbaru'); const search=document.querySelector('#ticketSearch'); if(search) search.classList.toggle('d-none',detail);
  try { if(path==='/dashboard')return renderDashboard(); if(path==='/tickets')return renderTickets(); if(path.startsWith('/tickets/'))return renderTicketDetail(path.split('/')[2]); if(path==='/notifications')return renderNotifications(); if(path==='/profile')return renderProfile(); if(path==='/reports')return renderReports(); if(path==='/activity-log')return renderActivityLogs(); if(['/users','/departments','/categories','/priorities','/sla-policies'].includes(path))return renderResource(path); } catch(error){showFlash(error.message);}
}
async function renderDashboard(){try{const d=await api('/dashboard/summary');$('#summary').innerHTML=[['Total tiket',d.total],['Resolved',d.resolved],['Closed',d.closed],['Pengguna',d.users]].map(x=>`<div class="col-6 col-xl-3"><div class="metric-card p-3"><div class="text-muted small">${x[0]}</div><div class="metric-value">${escapeHtml(x[1])}</div></div></div>`).join('');$('#ticketList').innerHTML='<div class="text-muted py-4">Pilih menu Tiket untuk melihat daftar lengkap.</div>';}catch(e){showFlash('Dashboard gagal dimuat: '+e.message);}}
async function renderTickets(){const d=await api('/tickets');$('#summary').innerHTML='';$('#ticketList').innerHTML=d.data?.length?`<table class="table"><thead><tr><th>Nomor</th><th>Judul</th><th>Status</th></tr></thead><tbody>${d.data.map(t=>`<tr><td>${escapeHtml(t.ticket_number)}</td><td><a data-route="/tickets/${t.id}" href="#/tickets/${t.id}">${escapeHtml(t.title)}</a></td><td>${escapeHtml(t.status)}</td></tr>`).join('')}</tbody></table>`:'<div class="text-muted py-5">Belum ada tiket.</div>';}
async function renderTicketDetail(id) {
  const ticket=await api(`/tickets/${id}`); const attachments=await api(`/tickets/${id}/attachments`); const canStatus=['Admin','Koordinator','Petugas'].includes(user.role?.name); const canAssign=['Admin','Koordinator'].includes(user.role?.name); const assignees=canAssign?await api('/users/assignees'):[]; const assigneeList=Array.isArray(assignees)?assignees:(assignees.data||[]); const active=(ticket.assignments||[]).find(x=>!x.unassigned_at);
  const statuses=['Open','Assigned','In Progress','Pending','Resolved','Closed'];
  $('#summary').innerHTML='';
  $('#ticketList').innerHTML=`<div class="row g-3"><div class="col-12"><div class="panel p-3"><div class="d-flex justify-content-between align-items-start"><div><div class="small text-muted">${escapeHtml(ticket.ticket_number||'—')}</div><h2 class="h5">${escapeHtml(ticket.title)}</h2></div><a class="btn btn-sm btn-outline-secondary" href="#/tickets" data-route="/tickets">Kembali</a></div><p>${escapeHtml(ticket.description)}</p><div class="small text-muted">Kategori: ${escapeHtml(ticket.category?.name||'—')} · Prioritas: ${escapeHtml(ticket.priority?.name||'—')} · Dibuat: ${ticket.created_at?new Date(ticket.created_at).toLocaleString('id-ID'):'—'}</div><div class="mt-2">Status: <strong>${escapeHtml(ticket.status)}</strong></div><div>Pelapor: <strong>${escapeHtml(ticket.requester?.name||'—')}</strong></div><div>Petugas: <strong>${escapeHtml(active?.assignee?.name||'Belum ditugaskan')}</strong></div></div></div><div class="col-12"><div class="panel p-3"><h3 class="h6">Perbarui Status</h3>${canStatus?`<form id="statusForm" class="d-flex gap-2"><select id="statusValue" class="form-select">${statuses.map(s=>`<option ${s===ticket.status?'selected':''}>${s}</option>`).join('')}</select><button class="btn btn-primary">Simpan</button></form>`:'<p class="text-muted mb-0">Tidak memiliki izin mengubah status.</p>'}</div></div><div class="col-12"><div class="panel p-3"><h3 class="h6">Lampiran</h3>${attachments.data?.length?attachments.data.map(x=>`<div class="d-flex justify-content-between border-bottom py-2"><span>${escapeHtml(x.original_name)}</span><button type="button" class="btn btn-sm btn-outline-secondary" data-file="${x.id}">Unduh</button></div>`).join(''):'<p class="text-muted">Belum ada lampiran.</p>'}<form id="attachmentForm" class="d-flex gap-2 mt-3"><input id="attachmentFile" class="form-control" type="file" required><button class="btn btn-outline-secondary">Upload</button></form></div></div><div class="col-12"><div class="panel p-3"><h3 class="h6">Penugasan</h3>${canAssign?`<form id="assignmentForm" class="d-flex gap-2"><select id="assigneeId" class="form-select" required><option value="">Pilih petugas</option>${assigneeList.map(x=>`<option value="${x.id}">${escapeHtml(x.name)} — ${escapeHtml(x.email)}</option>`).join('')}</select><button class="btn btn-outline-primary">Tugaskan</button></form>`:'<p class="text-muted mb-0">Penugasan dikelola oleh Koordinator atau Admin.</p>'}</div></div></div>`;
  if(canStatus)$('#statusForm').addEventListener('submit',async e=>{e.preventDefault();const b=e.target.querySelector('button');b.disabled=true;try{await api(`/tickets/${id}/status`,{method:'PATCH',body:JSON.stringify({status:$('#statusValue').value})});showFlash('Status berhasil diperbarui.','success');await renderTicketDetail(id);}catch(x){b.disabled=false;showFlash(x.message);}});
  $('#attachmentForm').addEventListener('submit',async e=>{e.preventDefault();const b=e.target.querySelector('button');b.disabled=true;try{const f=new FormData();f.append('file',$('#attachmentFile').files[0]);await api(`/tickets/${id}/attachments`,{method:'POST',body:f});showFlash('Lampiran berhasil diunggah.','success');await renderTicketDetail(id);}catch(x){b.disabled=false;showFlash(x.message);}});
  document.querySelectorAll('[data-file]').forEach(b=>b.addEventListener('click',async()=>{b.disabled=true;try{const x=await download(`/attachments/${b.dataset.file}/download`);const u=URL.createObjectURL(x.blob);const a=document.createElement('a');a.href=u;a.download=x.filename;a.click();URL.revokeObjectURL(u);}catch(x){showFlash(x.message);}finally{b.disabled=false;}}));
  if(canAssign)$('#assignmentForm').addEventListener('submit',async e=>{e.preventDefault();const b=e.target.querySelector('button');b.disabled=true;try{await api(`/tickets/${id}/assignments`,{method:'POST',body:JSON.stringify({assignee_id:Number($('#assigneeId').value)})});showFlash('Petugas berhasil ditugaskan.','success');await renderTicketDetail(id);}catch(x){b.disabled=false;showFlash(x.message);}});
}
async function renderNotifications(){const d=await api('/notifications');$('#summary').innerHTML='';$('#ticketList').innerHTML=d.data?.map(x=>`<div class="panel p-3 mb-2">${escapeHtml(x.title)}<p>${escapeHtml(x.body)}</p></div>`).join('')||'<div class="text-muted py-5">Tidak ada notifikasi.</div>';}
function renderProfile(){$('#summary').innerHTML=`<div class="panel p-3"><strong>${escapeHtml(user.name)}</strong><p>${escapeHtml(user.email)} Â· ${escapeHtml(user.role?.name)}</p></div>`;$('#ticketList').innerHTML='';}
const resourceConfig={ '/users':{title:'Pengguna',endpoint:'/users',fields:['name','email','is_active']},'/departments':{title:'Departemen',endpoint:'/departments',fields:['name','description']},'/categories':{title:'Kategori',endpoint:'/categories',fields:['name','description','is_active']},'/priorities':{title:'Prioritas',endpoint:'/priorities',fields:['name','level','is_active']},'/sla-policies':{title:'SLA',endpoint:'/sla-policies',fields:['name','response_target_minutes','resolution_target_minutes','is_active']}};
async function renderResource(path){const c=resourceConfig[path];const d=await api(c.endpoint);$('#summary').innerHTML=`<div class="col-12"><div class="panel p-3"><h2 class="h5">${escapeHtml(c.title)}</h2><p class="text-muted">Data tersinkronisasi dari backend.</p></div></div>`;$('#ticketList').innerHTML=d.data?.length?`<div class="table-responsive"><table class="table"><thead><tr>${c.fields.map(f=>`<th>${escapeHtml(f)}</th>`).join('')}</tr></thead><tbody>${d.data.map(row=>`<tr>${c.fields.map(f=>`<td>${escapeHtml(row[f]??'â€”')}</td>`).join('')}</tr>`).join('')}</tbody></table></div>`:'<div class="text-muted py-5">Belum ada data.</div>';}
async function renderReports(){const d=await api('/reports/tickets?group_by=status');$('#summary').innerHTML='';$('#ticketList').innerHTML=`<div class="panel p-3"><h2 class="h5">Laporan tiket</h2><table class="table"><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody>${(d.data||[]).map(x=>`<tr><td>${escapeHtml(x.status)}</td><td>${escapeHtml(x.total)}</td></tr>`).join('')}</tbody></table></div>`;}
async function renderActivityLogs(){const d=await api('/dashboard/summary');$('#summary').innerHTML='<div class="panel p-3"><h2 class="h5">Aktivitas terbaru</h2><p class="text-muted">Aktivitas sistem terbaru dari dashboard.</p></div>';$('#ticketList').innerHTML=(d.recent_activity||[]).map(x=>`<div class="panel p-3 mb-2"><strong>${escapeHtml(x.action)}</strong><div class="small text-muted">${escapeHtml(x.created_at)}</div></div>`).join('')||'<div class="text-muted py-5">Belum ada aktivitas.</div>';}
function render404(){$('#pageTitle').textContent='404';$('#summary').innerHTML='';$('#ticketList').innerHTML='<div class="text-muted py-5">Halaman tidak ditemukan.</div>';}
if(sessionStorage.getItem('servo_token'))boot();









