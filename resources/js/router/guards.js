export function authGuard(to, from, next) {
    const token = localStorage.getItem('auth_token');
    const tenantId = localStorage.getItem('tenant_id');

    if (to.meta.requiresAuth && (! token || ! tenantId)) {
        next({ name: 'login' });

        return;
    }

    if (to.meta.guest && token) {
        next({ name: 'home' });

        return;
    }

    next();
}
