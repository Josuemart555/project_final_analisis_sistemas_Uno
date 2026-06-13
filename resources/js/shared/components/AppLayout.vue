<template>
    <div class="layout">
        <header class="layout__header">
            <div class="layout__brand">
                Hospital HIS
            </div>
            <nav class="layout__nav">
                <router-link class="layout__link" to="/">
                    Inicio
                </router-link>
                <router-link v-if="!auth.isAuthenticated" class="layout__link" to="/login">
                    Acceso
                </router-link>
            </nav>
            <div v-if="auth.isAuthenticated" class="layout__session" aria-label="Sesión activa">
                <div class="layout__session-text">
                    <strong>{{ auth.user?.name }}</strong>
                    <span>{{ auth.userRole }} · {{ auth.tenantName }}</span>
                </div>
                <button class="layout__logout" type="button" @click="handleLogout">
                    Cerrar sesión
                </button>
            </div>
        </header>
        <main class="layout__main">
            <p v-if="sessionMessage" class="layout__notice">
                {{ sessionMessage }}
            </p>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();
const sessionMessage = ref('');

async function handleLogout() {
    await auth.logout();
    await router.push({ name: 'login' });
}

async function handleExpiredSession(event) {
    auth.clearSession();
    sessionMessage.value = event.detail?.message ?? 'La sesión ya no es válida.';

    if (router.currentRoute.value.name !== 'login') {
        await router.push({ name: 'login' });
    }
}

onMounted(() => {
    window.addEventListener('auth:session-expired', handleExpiredSession);
});

onBeforeUnmount(() => {
    window.removeEventListener('auth:session-expired', handleExpiredSession);
});
</script>

<style scoped>
.layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f8fafc;
    color: #0f172a;
}

.layout__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

.layout__brand {
    font-weight: 600;
    letter-spacing: 0.02em;
}

.layout__nav {
    display: flex;
    gap: 1rem;
    margin-left: auto;
}

.layout__link {
    color: #0f172a;
    text-decoration: none;
    font-weight: 500;
}

.layout__link.router-link-active {
    color: #2563eb;
}

.layout__main {
    flex: 1;
    padding: 1.5rem;
}

.layout__session {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.layout__session-text {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    text-align: right;
    font-size: 0.85rem;
    color: #475569;
}

.layout__session-text strong {
    color: #0f172a;
}

.layout__logout {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.55rem 0.75rem;
    background: #ffffff;
    color: #0f172a;
    font-weight: 600;
    cursor: pointer;
}

.layout__notice {
    max-width: 720px;
    margin: 0 auto 1rem;
    border: 1px solid #fed7aa;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    background: #fff7ed;
    color: #9a3412;
}

@media (max-width: 720px) {
    .layout__header {
        align-items: flex-start;
        flex-direction: column;
    }

    .layout__nav {
        margin-left: 0;
    }

    .layout__session {
        width: 100%;
        align-items: flex-start;
        justify-content: space-between;
    }

    .layout__session-text {
        text-align: left;
    }
}
</style>
