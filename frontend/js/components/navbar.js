export function navbar(user){return `<span class="text-muted small">${user?.name || ''} · ${user?.role?.name || ''}</span>`}
