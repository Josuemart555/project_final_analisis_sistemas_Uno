import { defineStore } from 'pinia';
import api from '@/plugins/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('auth_token'),
        tenantId: localStorage.getItem('tenant_id') ?? '00000000-0000-4000-8000-000000000001',
        user: JSON.parse(localStorage.getItem('auth_user') ?? 'null'),
    }),
    getters: {
        isAuthenticated: (state) => Boolean(state.token),
        userRole: (state) => state.user?.roles?.[0]?.name ?? 'Sin rol asignado',
        tenantName: (state) => state.user?.tenant?.name ?? state.tenantId,
    },
    actions: {
        setTenantId(tenantId) {
            const normalizedTenantId = tenantId.trim();

            this.tenantId = normalizedTenantId;

            if (normalizedTenantId) {
                localStorage.setItem('tenant_id', normalizedTenantId);
            }
        },
        persistSession(payload) {
            this.token = payload.access_token;
            this.user = payload.user ?? null;

            if (payload.access_token) {
                localStorage.setItem('auth_token', payload.access_token);
            }

            localStorage.setItem('auth_user', JSON.stringify(this.user));
        },
        async login(credentials) {
            const { data } = await api.post('/auth/login', credentials);

            this.persistSession(data);

            return data;
        },
        async fetchCurrentUser() {
            const { data } = await api.get('/auth/me');

            this.user = data.user ?? null;
            localStorage.setItem('auth_user', JSON.stringify(this.user));

            return data;
        },
        async logout() {
            try {
                await api.post('/auth/logout');
            } catch {
                // Ignorar errores de red al cerrar sesión.
            }

            this.clearSession();
        },
        clearSession() {
            this.token = null;
            this.user = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
        },
    },
});
