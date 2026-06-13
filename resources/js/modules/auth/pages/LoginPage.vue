<template>
    <section class="login">
        <div class="login__header">
            <p class="login__eyebrow">Módulo 1</p>
            <h2 class="login__title">
                Iniciar sesión
            </h2>
            <p class="login__description">
                Acceso por tenant, correo y contraseña usando JWT.
            </p>
        </div>
        <form class="login__form" @submit.prevent="handleSubmit">
            <label class="login__label">
                ID de tenant (X-Tenant-ID)
                <input
                    v-model="tenantId"
                    class="login__input"
                    type="text"
                    required
                    autocomplete="off"
                    :aria-invalid="Boolean(fieldErrors.tenantId)"
                >
                <span v-if="fieldErrors.tenantId" class="login__field-error">
                    {{ fieldErrors.tenantId }}
                </span>
            </label>
            <label class="login__label">
                Correo
                <input
                    v-model="email"
                    class="login__input"
                    type="email"
                    required
                    autocomplete="username"
                    :aria-invalid="Boolean(fieldErrors.email)"
                >
                <span v-if="fieldErrors.email" class="login__field-error">
                    {{ fieldErrors.email }}
                </span>
            </label>
            <label class="login__label">
                Contraseña
                <input
                    v-model="password"
                    class="login__input"
                    type="password"
                    required
                    autocomplete="current-password"
                    :aria-invalid="Boolean(fieldErrors.password)"
                >
                <span v-if="fieldErrors.password" class="login__field-error">
                    {{ fieldErrors.password }}
                </span>
            </label>
            <p v-if="errorMessage" class="login__error">
                {{ errorMessage }}
            </p>
            <button class="login__submit" type="submit" :disabled="loading">
                {{ loading ? 'Ingresando...' : 'Entrar' }}
            </button>
        </form>
        <dl class="login__demo">
            <div>
                <dt>Tenant demo</dt>
                <dd>00000000-0000-4000-8000-000000000001</dd>
            </div>
            <div>
                <dt>Correo demo</dt>
                <dd>recepcionista@demo.test</dd>
            </div>
            <div>
                <dt>Contraseña demo</dt>
                <dd>password</dd>
            </div>
        </dl>
    </section>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();

const tenantId = ref(auth.tenantId ?? '00000000-0000-4000-8000-000000000001');
const email = ref('recepcionista@demo.test');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');
const fieldErrors = reactive({
    tenantId: '',
    email: '',
    password: '',
});

function validateForm() {
    fieldErrors.tenantId = tenantId.value.trim() ? '' : 'Ingresa el tenant para enviar X-Tenant-ID.';
    fieldErrors.email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())
        ? ''
        : 'Ingresa un correo válido.';
    fieldErrors.password = password.value ? '' : 'Ingresa la contraseña.';

    return ! fieldErrors.tenantId && ! fieldErrors.email && ! fieldErrors.password;
}

async function handleSubmit() {
    errorMessage.value = '';

    if (! validateForm()) {
        return;
    }

    loading.value = true;

    try {
        auth.setTenantId(tenantId.value);
        await auth.login({
            email: email.value.trim(),
            password: password.value,
        });
        await router.push({ name: 'home' });
    } catch (error) {
        const message = error?.response?.data?.message
            ?? error?.response?.data?.errors?.email?.[0]
            ?? 'No fue posible iniciar sesión.';
        errorMessage.value = message;
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.login {
    max-width: 420px;
    margin: 0 auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
}

.login__header {
    margin-bottom: 1rem;
}

.login__eyebrow {
    margin: 0 0 0.35rem;
    color: #2563eb;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
}

.login__title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
}

.login__description {
    margin: 0.5rem 0 0;
    color: #475569;
    line-height: 1.5;
}

.login__form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.login__label {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    font-size: 0.9rem;
    color: #334155;
}

.login__input {
    border: 1px solid #cbd5f5;
    border-radius: 8px;
    padding: 0.65rem 0.75rem;
    font-size: 1rem;
}

.login__input[aria-invalid="true"] {
    border-color: #dc2626;
    outline-color: #dc2626;
}

.login__submit {
    margin-top: 0.5rem;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    background: #2563eb;
    color: #ffffff;
    font-weight: 600;
    cursor: pointer;
}

.login__submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.login__error {
    margin: 0;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 0.65rem 0.75rem;
    background: #fef2f2;
    color: #b91c1c;
    font-size: 0.9rem;
}

.login__field-error {
    color: #b91c1c;
    font-size: 0.8rem;
}

.login__demo {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 1rem 0 0;
    border-top: 1px solid #e2e8f0;
    padding-top: 1rem;
    color: #475569;
    font-size: 0.85rem;
}

.login__demo div {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.login__demo dt {
    font-weight: 700;
}

.login__demo dd {
    margin: 0;
    text-align: right;
    word-break: break-word;
}
</style>
