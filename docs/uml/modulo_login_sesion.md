# Diagramas UML - Modulo 1 Login y sesion

Alumno: 1890-17-12512 - JOSUE MANUEL MARTINEZ PEDROZA

## Caso de uso

```text
                 +-----------------------------+
                 | Sistema Hospitalario HIS    |
                 | Modulo Login y sesion       |
                 +-----------------------------+
        Usuario  |                             |
          o----->| Iniciar sesion              |
         /|\     |                             |
         / \---->| Consultar sesion activa     |
                 |                             |
                 | Cerrar sesion               |<-----o Administrador
                 |                             |      /|\
                 | Validar tenant              |      / \
                 | Validar token JWT           |
                 +-----------------------------+
```

## Clases

```text
+-------------------+       +-------------------+
| AuthController    |       | TenantMiddleware  |
+-------------------+       +-------------------+
| register()        |       | handle()          |
| login()           |       +---------+---------+
| me()              |                 |
| refresh()         |                 v
| logout()          |       +-------------------+
+---------+---------+       | Tenant            |
          |                 +-------------------+
          | usa             | id                |
          v                 | name              |
+-------------------+       | slug              |
| User              |       +-------------------+
+-------------------+
| tenant_id         |       +-------------------+
| name              |       | JwtAuth           |
| email             |       +-------------------+
| password          |       | handle()          |
| getJWTIdentifier()|       +---------+---------+
| getJWTCustomClaims()       |
+-------------------+       v
                    +-------------------+
                    | JWTAuthFacade     |
                    +-------------------+
                    | fromUser()        |
                    | parseToken()      |
                    | refresh()         |
                    | invalidate()      |
                    +-------------------+
```

## Secuencia

```text
Usuario        Vue Login        Axios        TenantMiddleware       AuthController        JWTAuthFacade
  |                |              |                 |                     |                     |
  | tenant/email   |              |                 |                     |                     |
  | password ----->|              |                 |                     |                     |
  |                | POST /login  |                 |                     |                     |
  |                |------------->| X-Tenant-ID     |                     |                     |
  |                |              |---------------->| valida tenant       |                     |
  |                |              |                 |-------------------->| valida credenciales |
  |                |              |                 |                     | fromUser(user) ---->|
  |                |              |                 |                     |<---- token JWT      |
  |                |<-------------| access_token + user                   |                     |
  | guarda token   |              |                 |                     |                     |
  | redirige home  |              |                 |                     |                     |
  |                | POST /logout | Authorization   |                     |                     |
  |                |------------->| X-Tenant-ID     | valida tenant       | invalidate(token) -->|
  | limpia sesion  |<-------------| mensaje OK      |                     |                     |
```
