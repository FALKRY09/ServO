
const routes = new Map();
let currentUser;

export function setRoutes(user, routeDefinitions) {
    currentUser = user;
    routes.clear();

    routeDefinitions.forEach(route => {
        routes.set(route.path, route);
    });
}

export function navigate(path, replace = false) {
    const target = routes.has(path) || /^\/tickets\/\d+$/.test(path) ? path : '/404';

    if (replace) {
        location.hash = `#${target}`;
    } else {
        location.hash = `#${target}`;
    }
}

export function startRouter(render) {

    const currentRoute = () => location.hash.replace(/^#/, '') || '/dashboard';

    // Hash change handles menu clicks, refresh, Back, and Forward.
    window.addEventListener('hashchange', () => render(currentRoute()));

    // Menangani klik link navigasi
    document.addEventListener('click', event => {
        const link = event.target.closest('[data-route]');

        if (!link) return;

        event.preventDefault();
        navigate(link.dataset.route);
    });

    render(currentRoute());
}

export function canAccess(path) {
    return routes.has(path) &&
        (
            !routes.get(path).roles ||
            routes.get(path).roles.includes(currentUser?.role?.name)
        );
}
