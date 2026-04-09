# ZYGA API - Backend final profesional consolidado

## Enfoque adoptado
La API se consolidó alrededor de `assistance_requests` como entidad operativa central. La meta fue dejar un backend coherente para una operación tipo Uber de asistencia vial básica, no una suma de CRUDs sin flujo.

## Núcleo del flujo activo
1. autenticación por token
2. cliente registra vehículo
3. cliente crea solicitud
4. proveedor compatible la visualiza
5. proveedor la acepta
6. proveedor actualiza estados
7. cliente consulta estado y timeline
8. admin supervisa y corrige
9. cliente registra pago

## Decisiones de consolidación
- Se eliminó del contrato activo lo que duplicaba o rompía el flujo principal.
- `service_requests` quedó fuera del contrato activo del MVP.
- `subscriptions`, `provider/reviews` y `admin/legal` quedaron fuera de rutas activas.
- Se fortaleció la transición de estados y la trazabilidad por timeline.
- Se agregó soporte coherente para `cancel_reason` en `assistance_requests`.
- Se implementó pago sandbox/manual para permitir cierre funcional del servicio.

## Contrato activo final
### Público
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `GET /api/v1/services`
- `GET /api/v1/payment-method-types`
- `GET /api/v1/payment-method-types/{id}`

### Protegido común
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/logout`
- `POST /api/v1/auth/logout-all`
- `GET /api/v1/me`
- `PUT|PATCH /api/v1/me`
- `GET /api/v1/notifications`
- `GET /api/v1/notifications/{id}`
- `PATCH /api/v1/notifications/{id}/read`
- `PATCH /api/v1/notifications/read-all`

### Cliente
- vehículos
- direcciones
- métodos de pago
- solicitudes de asistencia
- pagos

### Proveedor
- perfil
- servicios
- horarios
- documentos
- operación viva de solicitudes

### Admin
- usuarios
- proveedores
- catálogos
- asistencia
- finanzas
- auditoría

## Criterios de operación profesional alcanzados
- guardado con Sanctum por Bearer token
- separación por rol con middleware
- ownership por usuario en recursos sensibles
- timeline de solicitud vía `request_history` + `request_events`
- auditoría administrativa y operativa mínima
- bloqueo de doble asignación por aceptación transaccional
- pago MVP registrable y trazable

## Limitaciones honestas
- El ZIP no permite certificar ejecución end-to-end en este entorno porque no se instalaron dependencias externas aquí.
- La validación realizada fue de auditoría técnica, coherencia de contrato y sintaxis PHP.
- El pago actual es sandbox/manual, no integración real con gateway.
- No existe matching geográfico real por cercanía en el backend actual consolidado.

## Entregables incluidos
- proyecto backend consolidado
- auditoría MVP previa
- documento final backend
- colección Postman MVP compacta
- colección Postman profesional completa
- guía Postman profesional
