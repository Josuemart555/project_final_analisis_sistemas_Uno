import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? '/api/v1',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    const tenantId = localStorage.getItem('tenant_id');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    if (tenantId) {
        config.headers['X-Tenant-ID'] = tenantId;
    }

    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error?.response?.status;

        if ([401, 403].includes(status)) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            window.dispatchEvent(new CustomEvent('auth:session-expired', {
                detail: {
                    message: error?.response?.data?.message ?? 'La sesión ya no es válida.',
                },
            }));
        }

        return Promise.reject(error);
    },
);

export default api;
