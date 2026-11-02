export function escapeHtml(value = '') { return String(value).replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char])); }
export function showFlash(message, type = 'danger') { const el = document.querySelector('#flash'); el.className = `alert alert-${type}`; el.textContent = message; }
