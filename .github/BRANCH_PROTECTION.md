# 🔒 Configuración de Branch Protection — Guía para Admins

> **Acción requerida por**: `joshuacirilo` o `Josuemart555`  
> **Dónde**: Settings → Branches → Add branch protection rule  
> **Cuándo**: Inmediatamente después del primer push al repo

---

## Protección de `main`

Ir a: `https://github.com/compilations-teams/Sistema-Hospitalario-Integrado/settings/branches`

### Regla para `main`

| Opción | Valor |
|--------|-------|
| Branch name pattern | `main` |
| ✅ Require a pull request before merging | **ON** |
| ↳ Required approvals | **1** |
| ↳ Dismiss stale reviews | **ON** |
| ↳ Require review from Code Owners | **ON** ← CRÍTICO |
| ✅ Require status checks to pass | **ON** |
| ↳ Status check requerido | `Verificar que solo admins mergeen a main` |
| ↳ Status check requerido | `Validar formato del PR` |
| ✅ Require branches to be up to date | **ON** |
| ✅ Restrict who can push to matching branches | **ON** |
| ↳ Allowed: | `joshuacirilo`, `Josuemart555` |
| ✅ Do not allow bypassing the above settings | **ON** |
| ✅ Allow force pushes | **OFF** |
| ✅ Allow deletions | **OFF** |

---

## Protección de `develop`

### Regla para `develop`

| Opción | Valor |
|--------|-------|
| Branch name pattern | `develop` |
| ✅ Require a pull request before merging | **ON** |
| ↳ Required approvals | **1** |
| ↳ Dismiss stale reviews | **ON** |
| ✅ Require status checks to pass | **ON** |
| ↳ Status check requerido | `Validar formato del PR` |
| ✅ Allow force pushes | **OFF** |
| ✅ Allow deletions | **OFF** |

---

## Configurar Roles del Equipo

Ir a: `Settings → Collaborators and teams`

| GitHub Handle | Rol en el repo |
|---------------|---------------|
| `joshuacirilo` | **Admin** |
| `Josuemart555` | **Admin** |
| Todos los demás 32 estudiantes | **Write** (pueden pushear ramas propias y crear PRs) |

> ⚠️ Con rol **Write**, los colaboradores pueden crear ramas y PRs pero  
> **NO** pueden mergear a `main` gracias a la branch protection rule.

---

## Etiquetas a crear en Issues

Ir a: `Issues → Labels → New label`

| Etiqueta | Color | Descripción |
|----------|-------|-------------|
| `open` | `#0075ca` | Issue abierto, pendiente de resolución |
| `bug` | `#d73a4a` | Error en el sistema |
| `enhancement` | `#a2eeef` | Nueva funcionalidad |
| `needs-triage` | `#e4e669` | Requiere revisión de admin |
| `in-progress` | `#fbca04` | Alguien está trabajando en esto |
| `blocked` | `#b60205` | Bloqueado por dependencia |
| `ready-for-review` | `#0e8a16` | PR listo para revisión |
| `admision` | `#c5def5` | Módulo Admisión |
| `emr` | `#bfdadc` | Módulo EMR |
| `laboratorio` | `#d4c5f9` | Módulo Laboratorio |
| `websocket` | `#f9d0c4` | Feature WebSocket |
| `docs` | `#e4e4e4` | Documentación |

---

## Verificación Post-Configuración

Una vez configurado, verificar con estos comandos:

```bash
# Intentar push directo a main (debe fallar para no-admins)
git checkout main
git commit --allow-empty -m "test: push directo a main"
git push origin main
# → Expected: ERROR: push declined due to repository rule violations

# Crear un PR como colaborador hacia main (debe requerir admin approval)
# → Expected: "Review required from joshuacirilo or Josuemart555"
```

---

*Configuración válida para el proyecto Sistema Hospitalario Integrado — UMG 2026*
