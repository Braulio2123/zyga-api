# ZYGA API MVP - Auditoría y consolidación backend

## 1. Diagnóstico técnico general

### Hallazgo central
La entidad operativa correcta para el MVP es `assistance_requests`. El flujo más coherente es:

1. autenticación
2. registro de vehículo
3. creación de solicitud de asistencia
4. visibilidad para proveedor compatible
5. aceptación por proveedor
6. actualización de estados por proveedor
7. consulta de timeline por cliente
8. supervisión administrativa
9. registro de pago y cierre

### Problemas detectados antes de la consolidación
- El proyecto mezclaba flujo troncal con módulos secundarios que no cerraban el MVP.
- `service_requests` duplicaba el flujo principal y generaba ambigüedad operativa.
- `ClientPaymentController::store()` regresaba `501`, por lo que el flujo de pago/cierre no quedaba completo.
- `AdminAssistanceController` usaba `cancel_reason`, pero la columna no existía en la migración real de `assistance_requests`.
- `ProviderProfileController` permitía que el proveedor eligiera su propio `status_id`, lo que rompía el control administrativo.
- `ProviderAssistanceController::accept()` era vulnerable a carrera entre proveedores; no había `lockForUpdate()`.
- Las actualizaciones administrativas de solicitudes no alimentaban correctamente `request_history` y `request_events`, dejando el timeline incompleto.
- No se estaban generando suficientes notificaciones operativas para el cliente/proveedor durante el ciclo vivo de la asistencia.

## 2. Decisiones arquitectónicas tomadas

### 2.1 Flujo troncal conservado
Se consolidó el MVP alrededor de `assistance_requests`.

### 2.2 Módulos congelados o sacados del contrato activo
Se quitaron de `routes/api.php` los módulos que no cerraban el flujo troncal:
- `client/service-requests`
- `client/subscriptions`
- `subscription-plans` públicos
- `provider/reviews`
- `admin/legal`

Estos archivos siguen en el repositorio como historial técnico, pero ya no forman parte del contrato activo del MVP.

### 2.3 Matching realista para el código disponible
No se prometió matching geográfico real porque el esquema ejecutable del proyecto no incluye la estructura operativa suficiente para eso (`provider_locations`, `service_areas`, etc.). El matching del MVP consolidado queda así:
- solicitud abierta
- proveedor verificado y activo
- proveedor con servicio compatible
- aceptación transaccional del primero que la tome

### 2.4 Pago MVP
Se implementó pago sandbox/manual:
- registra `payments`
- registra `payment_transactions`
- genera evento de timeline
- notifica a cliente y proveedor

## 3. Contrato final recomendado del MVP

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
- `PATCH /api/v1/me`
- `GET /api/v1/notifications`
- `GET /api/v1/notifications/{id}`
- `PATCH /api/v1/notifications/{id}/read`
- `PATCH /api/v1/notifications/read-all`

### Cliente
- `GET|POST /api/v1/client/vehicles`
- `GET|PUT|PATCH|DELETE /api/v1/client/vehicles/{id}`
- `GET|POST /api/v1/client/addresses`
- `GET|PUT|PATCH|DELETE /api/v1/client/addresses/{id}`
- `GET|POST /api/v1/client/payment-methods`
- `GET|PUT|PATCH|DELETE /api/v1/client/payment-methods/{id}`
- `GET|POST /api/v1/client/assistance-requests`
- `GET /api/v1/client/assistance-requests/{id}`
- `PATCH /api/v1/client/assistance-requests/{id}/cancel`
- `GET /api/v1/client/assistance-requests/{id}/status`
- `GET /api/v1/client/assistance-requests/{id}/timeline`
- `GET|POST /api/v1/client/payments`
- `GET /api/v1/client/payments/{id}`
- `GET /api/v1/client/payments/{id}/receipt`

### Proveedor
- `POST|GET|PATCH|PUT|DELETE /api/v1/provider/profile`
- `GET|PUT|PATCH /api/v1/provider/services`
- `GET|POST /api/v1/provider/schedules`
- `GET|PUT|PATCH|DELETE /api/v1/provider/schedules/{id}`
- `GET|POST /api/v1/provider/documents`
- `GET|DELETE /api/v1/provider/documents/{id}`
- `GET /api/v1/provider/assistance-requests/available`
- `GET /api/v1/provider/assistance-requests`
- `GET /api/v1/provider/assistance-requests/{id}`
- `PATCH /api/v1/provider/assistance-requests/{id}/accept`
- `PATCH /api/v1/provider/assistance-requests/{id}/status`

### Admin
- `GET /api/v1/admin/providers`
- `GET|PATCH|PUT /api/v1/admin/providers/{id}`
- `GET /api/v1/admin/users`
- `GET|PATCH|PUT /api/v1/admin/users/{id}`
- `GET|POST /api/v1/admin/services`
- `GET|PATCH|PUT|DELETE /api/v1/admin/services/{id}`
- `GET|POST /api/v1/admin/vehicle-types`
- `GET|PATCH|PUT|DELETE /api/v1/admin/vehicle-types/{id}`
- `GET|POST /api/v1/admin/statuses`
- `GET|PATCH|PUT|DELETE /api/v1/admin/statuses/{id}`
- `GET|POST /api/v1/admin/payment-method-types`
- `GET|PATCH|PUT|DELETE /api/v1/admin/payment-method-types/{id}`
- `GET /api/v1/admin/audit-logs`
- `GET /api/v1/admin/audit-logs/{id}`
- `GET /api/v1/admin/assistance-requests`
- `GET|PATCH|PUT /api/v1/admin/assistance-requests/{id}`
- `GET /api/v1/admin/finance/payments`
- `GET|PATCH|PUT /api/v1/admin/finance/payments/{id}`
- `GET /api/v1/admin/finance/transactions`
- `GET /api/v1/admin/finance/transactions/{id}`

## 4. Limitaciones honestas del entregable
- No fue posible ejecutar pruebas funcionales end-to-end porque el ZIP no incluye `vendor/` y el entorno no permite instalar dependencias desde internet.
- Sí se validó sintaxis PHP con `php -l` sobre los archivos del proyecto corregido.
- El matching geográfico real no quedó implementado porque el esquema ejecutable del proyecto no trae la estructura necesaria en esta base.
- El pago sigue siendo MVP sandbox/manual, no integración real con pasarela.
