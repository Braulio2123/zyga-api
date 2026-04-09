# Guía de prueba en Postman - ZYGA API MVP consolidado

## Requisito previo
Ejecutar base limpia con seeders:

```bash
php artisan migrate:fresh --seed
```

## Usuarios demo esperados
- client@zyga.com / password123
- provider@zyga.com / password123
- admin@zyga.com / password123

## Flujo recomendado

### 1. Catálogos públicos
1. `GET /api/v1/services`
2. `GET /api/v1/payment-method-types`

### 2. Cliente
1. Login cliente
2. Crear vehículo
3. Crear solicitud de asistencia
4. Consultar status/timeline inicial

### 3. Proveedor
1. Login proveedor
2. Consultar perfil
3. Consultar solicitudes disponibles
4. Aceptar solicitud
5. Cambiar a `in_progress`
6. Cambiar a `completed`

### 4. Cliente
1. Consultar status actualizado
2. Consultar timeline
3. Registrar pago sandbox/manual
4. Consultar recibo

### 5. Admin
1. Login admin
2. Revisar solicitud
3. Revisar pagos
4. Revisar transacciones
5. Revisar auditoría

## Notas
- Si el proveedor no está verificado o no está en estado `active`, el backend bloqueará operación.
- El pago solo puede registrarse cuando la solicitud ya está en `completed`.
- Un cliente no puede abrir varias solicitudes activas al mismo tiempo.
