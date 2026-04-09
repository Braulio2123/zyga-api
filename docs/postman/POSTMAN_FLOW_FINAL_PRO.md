# ZYGA API - Guía profesional de pruebas en Postman

## Objetivo
Esta colección no está pensada solo para demostrar un CRUD. Está organizada para validar el backend como una operación tipo marketplace de asistencia vial, con flujo cliente -> proveedor -> admin -> pago.

## Colección final incluida
- `ZYGA_API_FINAL_PRO.postman_collection.json`

## Requisitos previos
1. Ejecutar base limpia y semilla completa:

```bash
php artisan migrate:fresh --seed
```

2. Confirmar usuarios demo:
- `client@zyga.com / password123`
- `provider@zyga.com / password123`
- `admin@zyga.com / password123`

3. Confirmar catálogos seed:
- servicios activos (`grua`, `paso_corriente`, `cambio_llanta`, etc.)
- tipos de vehículo
- tipos de método de pago
- estatus de proveedor (`active`, `pending`, `suspended`)

## Estructura profesional de la colección
1. `00 Bootstrap & Public Catalogs`
2. `01 Auth`
3. `02 Common Profile & Notifications`
4. `03 Client Vehicles`
5. `04 Client Addresses`
6. `05 Client Payment Methods`
7. `06 Client Assistance Requests`
8. `07 Client Payments`
9. `08 Provider Profile`
10. `09 Provider Services, Schedules & Documents`
11. `10 Provider Live Operations`
12. `11 Admin Users & Providers`
13. `12 Admin Catalogs`
14. `13 Admin Assistance, Finance & Audit`
15. `14 Role Tests & Security Guards`

## Orden recomendado de ejecución

### A. Bootstrap
Ejecutar primero:
- `00 Bootstrap & Public Catalogs/01 List Services`
- `00 Bootstrap & Public Catalogs/02 List Payment Method Types`

Esto llena variables base como `service_id` y `payment_method_type_code`.

### B. Autenticación
Ejecutar:
- login client
- login provider
- login admin

Esto deja cargados:
- `client_token`
- `provider_token`
- `admin_token`

### C. Flujo cliente
Ejecutar en este orden:
1. Client vehicles
2. Client addresses
3. Client payment methods
4. Client assistance requests

Puntos importantes:
- El cliente no puede crear una nueva solicitud si ya tiene una activa.
- El vehículo debe pertenecer al usuario autenticado.
- El servicio debe estar activo.

### D. Flujo proveedor
Ejecutar:
1. Provider profile
2. Provider services / schedules / documents
3. Provider live operations

Puntos importantes:
- El proveedor solo opera si está verificado y con estatus `active`.
- La aceptación de una solicitud está protegida para que el primer proveedor que la tome bloquee la operación.
- El proveedor solo puede mover estados válidos.

### E. Cierre y pago
Ejecutar:
1. `Client Assistance Requests/Timeline`
2. `Client Payments/Register Payment for Completed Request`
3. `Client Payments/Receipt`

Puntos importantes:
- El pago solo se permite cuando la solicitud ya está en `completed`.
- El pago actual es sandbox/manual, no gateway real.

### F. Supervisión administrativa
Ejecutar:
1. Admin users & providers
2. Admin catalogs
3. Admin assistance, finance & audit

Puntos importantes:
- Admin puede supervisar, reasignar o cancelar según reglas de transición.
- Las actualizaciones administrativas alimentan auditoría y timeline.

### G. Seguridad
Ejecutar al final:
- `14 Role Tests & Security Guards`

Esto valida:
- acceso permitido por rol
- acceso prohibido por rol
- rechazo a rutas protegidas sin token válido

## Criterios mínimos para declarar backend “operativo”
El backend queda aceptable como MVP profesional si estas pruebas pasan:

1. Client login exitoso
2. Provider login exitoso
3. Admin login exitoso
4. Cliente puede crear vehículo
5. Cliente puede crear solicitud
6. Proveedor puede ver solicitud disponible
7. Proveedor puede aceptar solicitud
8. Proveedor puede pasar a `in_progress`
9. Proveedor puede pasar a `completed`
10. Cliente puede consultar timeline
11. Cliente puede registrar pago
12. Admin puede ver solicitud, pago, transacción y auditoría
13. Guards por rol devuelven `403` donde corresponde
14. Rutas protegidas devuelven `401` sin token válido

## Lo que esta colección sí cubre
- contrato activo del backend
- flujo troncal de asistencia
- pago MVP
- supervisión administrativa
- pruebas positivas y de seguridad base

## Lo que esta colección no promete por sí sola
- pruebas de carga
- pruebas de concurrencia real multiusuario
- integración con gateway productivo
- matching geográfico real por cercanía
- notificaciones push reales

## Recomendación operativa
Usar esta colección como:
- colección maestra de QA funcional
- base del handoff al equipo móvil
- referencia contractual del backend activo
