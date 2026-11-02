import { escapeHtml } from '../utils/dom.js';

export function renderShell(user, routes) {
  const links = routes.filter(route => !route.roles || route.roles.includes(user.role?.name));
  const primary = links.filter(route => ['/dashboard','/tickets','/notifications','/profile'].includes(route.path));
  const management = links.filter(route => ['/users','/departments','/categories','/priorities','/sla-policies'].includes(route.path));
  const reporting = links.filter(route => ['/reports','/activity-log'].includes(route.path));
  const render = items => items.map(route => `<button type="button" class="nav-link text-start" data-route="${route.path}"><span class="icon">•</span>${escapeHtml(route.label)}</button>`).join('');
  document.querySelector('#sideNav').innerHTML = `<div class="sidebar-section">Workspace</div>${render(primary)}${management.length ? `<div class="sidebar-section">Management</div>${render(management)}` : ''}${reporting.length ? `<div class="sidebar-section">Insights</div>${render(reporting)}` : ''}`;
}

export function setActiveRoute(path) { document.querySelectorAll('#sideNav [data-route]').forEach(link => link.classList.toggle('active', link.dataset.route === path)); }
