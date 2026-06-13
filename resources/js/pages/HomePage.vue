<template>
    <section class="home">
        <h1 class="home__title">
            Sistema Hospitalario Integrado
        </h1>
        <p class="home__subtitle">
            Infraestructura base: Laravel 12, Vue 3, JWT y multitenancy por cabecera.
        </p>
        <div class="home__summary">
            <span>Sesión activa</span>
            <strong>{{ auth.user?.name }}</strong>
            <small>{{ auth.userRole }} en {{ auth.tenantName }}</small>
        </div>
    </section>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();

onMounted(() => {
    if (! auth.user && auth.token) {
        auth.fetchCurrentUser();
    }
});
</script>

<style scoped>
.home {
    max-width: 720px;
}

.home__title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.home__subtitle {
    color: #475569;
    line-height: 1.6;
}

.home__summary {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin-top: 1.5rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 1rem;
    background: #ffffff;
}

.home__summary span,
.home__summary small {
    color: #475569;
}

.home__summary strong {
    font-size: 1.15rem;
}
</style>
