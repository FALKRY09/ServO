export function sidebar(items){return items.map(item=>`<button type="button" class="nav-link text-start" data-route="${item.path}">${item.label}</button>`).join('')}
