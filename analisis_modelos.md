# ANÁLISIS DEL SISTEMA DE GESTIÓN CONTRATISTA

## 🎯 OBJETIVO DEL SISTEMA
Sistema para gestionar servicios de contratista que permite:
- Generar presupuestos a clientes
- Gestionar órdenes de servicio
- Controlar facturación (cobros y pagos)
- Administrar subcontratistas y proveedores
- Reportes financieros y de gestión

---

## 🔄 FLUJO PRINCIPAL DEL NEGOCIO

```
1. CLIENTE solicita servicio
   ↓
2. Se calcula: Materiales + Mano de Obra + Ganancia
   ↓
3. Se genera PRESUPUESTO (solo monto total, sin detalle)
   Estado: "En Revisión"
   ↓
4. Cliente ACEPTA → Estado: "Aprobado"
   ↓
5. Se genera FACTURA (pago único o en partes)
   ↓
6. Cliente paga (total o parcial)
   Estado Presupuesto: "En Ejecución"
   Estado Factura: "Pagada" o "Pago Parcial"
   ↓
7. Se genera ORDEN DE SERVICIO
   Opciones:
   a) Subcontratista con materiales (servicio completo)
   b) Subcontratista solo mano de obra (empresa compra materiales)
   
   **Flujo de estados de Orden de Servicio:**
   - PENDIENTE: Orden creada, esperando aceptación del subcontratista
   - APROBADO: Subcontratista acepta la orden (fecha_aprobacion)
   - EN_EJECUCION: Trabajo iniciado (fecha_inicio_ejecucion)
   - FINALIZADO: Trabajo completado (fecha_finalizacion)
   - PAGADO: Pago realizado al subcontratista
   ↓
8. Se ejecuta el servicio
   ↓
9. Servicio completado
   Estado Presupuesto: "Finalizado"
   Estado Factura: "Pago Completado"
```

---

## 📋 MODELOS DEFINITIVOS

### 🔐 MÓDULO: SEGURIDAD Y USUARIOS

#### 1. **User** (usuarios)
- id
- nombre_completo
- username (unique)
- email (unique)
- password
- rol_id (FK → roles)
- estado (activo/inactivo)
- ultimo_acceso
- timestamps
- softDeletes

#### 2. **Rol** (roles)
- id
- nombre (admin, supervisor, operador, etc.)
- descripcion
- timestamps

#### 3. **Permiso** (permisos)
- id
- modulo (clientes, presupuestos, facturas, etc.)
- accion (crear, editar, ver, eliminar)
- descripcion

#### 4. **RolPermiso** (rol_permisos) - Pivot
- id
- rol_id (FK → roles)
- permiso_id (FK → permisos)

---

### 👥 MÓDULO: CLIENTES

#### 5. **Cliente** (clientes)
- id
- tipo_documento (RUC, DNI)
- numero_documento (unique)
- razon_social
- direccion
- distrito
- provincia
- departamento
- persona_contacto
- cargo_contacto
- telefono
- email
- estado (activo/inactivo)
- observaciones (text)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

---

### 🏢 MÓDULO: SUBCONTRATISTAS

#### 6. **Subcontratista** (subcontratistas)
- id
- tipo (empresa/persona_natural)
- tipo_documento (RUC/DNI)
- numero_documento (unique)
- razon_social_nombre
- direccion
- telefono
- email
- banco
- numero_cuenta
- cci
- numero_cuenta_detraccion
- estado (activo/inactivo)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

---

### 📦 MÓDULO: PROVEEDORES Y ARTÍCULOS

#### 7. **Proveedor** (proveedores)
- id
- tipo_documento (RUC)
- numero_documento (unique)
- razon_social
- rubro
- contacto
- telefono
- email
- estado (activo/inactivo)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

#### 8. **Articulo** (articulos)
- id
- codigo (unique)
- descripcion
- unidad_medida (m2, kg, unidad, etc.)
- proveedor_id (FK → proveedores)
- precio_compra (decimal)
- precio_venta (decimal)
- stock (decimal)
- fecha_vencimiento (nullable)
- estado (activo/inactivo)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

---

### 🛠️ MÓDULO: SERVICIOS

#### 9. **Servicio** (servicios)
- id
- codigo (unique)
- descripcion
- unidad_medida
- precio_referencial (decimal)
- estado (activo/inactivo)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

---

### 💰 MÓDULO: PRESUPUESTOS

#### 10. **Presupuesto** (presupuestos)
- id
- numero_presupuesto (auto, unique)
- fecha_emision
- fecha_vencimiento
- cliente_id (FK → clientes)
- persona_contacto
- supervisor_id (FK → users)
- estado (en_revision, aprobado, rechazado, vencido, en_ejecucion, finalizado)
- fecha_aceptacion (nullable)
- fecha_inicio (nullable)
- fecha_finalizacion_estimada (nullable)
- periodo_ejecucion_dias (int)
- base_imponible (decimal)
- igv (decimal)
- descuento_porcentaje (decimal, nullable)
- descuento_monto (decimal, nullable)
- total (decimal)
- observaciones (text)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

#### 11. **PresupuestoDetalle** (presupuesto_detalles)
- id
- presupuesto_id (FK → presupuestos)
- servicio_id (FK → servicios, nullable)
- descripcion
- cantidad (decimal)
- unidad_medida
- precio_unitario (decimal)
- subtotal (decimal)
- orden (int) - para ordenar las líneas
- timestamps

---

### 📋 MÓDULO: ÓRDENES DE SERVICIO

#### 12. **OrdenServicio** (ordenes_servicio)
- id
- numero_orden (auto, unique)
- fecha_emision
- presupuesto_id (FK → presupuestos)
- subcontratista_id (FK → subcontratistas)
- tipo_contrato (servicio_completo, solo_mano_obra)
- estado (pendiente, aprobado, en_ejecucion, finalizado, pagado)
- fecha_aprobacion (nullable) - cuando el subcontratista acepta
- fecha_inicio_ejecucion (nullable) - cuando inicia el trabajo
- fecha_finalizacion (nullable) - cuando termina el trabajo
- base_imponible (decimal)
- igv (decimal)
- total (decimal)
- porcentaje_detraccion (decimal, nullable)
- monto_detraccion (decimal, nullable)
- tipo_documento (factura, recibo_honorarios, boleta)
- observaciones (text)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

#### 13. **OrdenServicioDetalle** (orden_servicio_detalles)
- id
- orden_servicio_id (FK → ordenes_servicio)
- descripcion
- cantidad (decimal)
- unidad_medida
- precio_unitario (decimal)
- subtotal (decimal)
- timestamps

---

### 💵 MÓDULO: FACTURACIÓN - CUENTAS POR COBRAR

#### 14. **FacturaCliente** (facturas_clientes)
- id
- numero_factura
- serie
- fecha_emision
- fecha_vencimiento
- cliente_id (FK → clientes)
- presupuesto_id (FK → presupuestos)
- base_imponible (decimal)
- igv (decimal)
- descuento_porcentaje (decimal, nullable)
- descuento_descripcion (nullable)
- descuento_monto (decimal, nullable)
- total (decimal)
- porcentaje_detraccion (decimal, nullable)
- monto_detraccion (decimal, nullable)
- estado (pendiente, pago_parcial, pagado_completo)
- monto_pagado (decimal)
- monto_pendiente (decimal)
- observaciones (text)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

#### 15. **PagoCliente** (pagos_clientes)
- id
- factura_cliente_id (FK → facturas_clientes)
- numero_pago (auto)
- fecha_pago
- monto (decimal)
- metodo_pago (efectivo, transferencia, cheque, deposito)
- banco (nullable)
- numero_operacion (nullable)
- comprobante (nullable) - ruta del archivo
- observaciones (text)
- usuario_registro_id (FK → users)
- timestamps

---

### 💸 MÓDULO: FACTURACIÓN - CUENTAS POR PAGAR

#### 16. **FacturaSubcontratista** (facturas_subcontratistas)
- id
- tipo_documento (factura, recibo_honorarios, boleta)
- numero_documento
- serie (nullable)
- fecha_emision
- fecha_vencimiento
- subcontratista_id (FK → subcontratistas)
- orden_servicio_id (FK → ordenes_servicio)
- base_imponible (decimal)
- igv (decimal)
- total (decimal)
- porcentaje_detraccion (decimal, nullable)
- monto_detraccion (decimal, nullable)
- estado (pendiente, pago_parcial, pagado_completo)
- monto_pagado (decimal)
- monto_pendiente (decimal)
- observaciones (text)
- usuario_creacion_id (FK → users)
- usuario_modificacion_id (FK → users)
- timestamps
- softDeletes

#### 17. **PagoSubcontratista** (pagos_subcontratistas)
- id
- factura_subcontratista_id (FK → facturas_subcontratistas)
- numero_pago (auto)
- fecha_pago
- monto (decimal)
- metodo_pago (efectivo, transferencia, cheque, deposito)
- banco (nullable)
- numero_operacion (nullable)
- cuenta_detraccion_usada (boolean)
- comprobante (nullable) - ruta del archivo
- observaciones (text)
- usuario_registro_id (FK → users)
- timestamps

---

### ⚙️ MÓDULO: CONFIGURACIÓN

#### 18. **Empresa** (empresa)
- id
- ruc
- razon_social
- nombre_comercial
- direccion
- telefono
- email
- logo (ruta del archivo)
- timestamps

#### 19. **Parametro** (parametros)
- id
- clave (igv_porcentaje, serie_presupuesto, serie_factura, etc.)
- valor
- descripcion
- tipo_dato (decimal, string, int, boolean)
- timestamps

---

## 📊 RESUMEN DE RELACIONES

### Relaciones principales:
1. **User** → hasMany → Presupuestos (como supervisor)
2. **Cliente** → hasMany → Presupuestos
3. **Presupuesto** → hasMany → PresupuestoDetalles
4. **Presupuesto** → hasMany → OrdenesServicio
5. **Presupuesto** → hasOne → FacturaCliente
6. **Subcontratista** → hasMany → OrdenesServicio
7. **OrdenServicio** → hasMany → OrdenServicioDetalles
8. **OrdenServicio** → hasOne → FacturaSubcontratista
9. **FacturaCliente** → hasMany → PagosClientes
10. **FacturaSubcontratista** → hasMany → PagosSubcontratistas
11. **Rol** → belongsToMany → Permisos (through RolPermiso)
12. **Proveedor** → hasMany → Articulos

---

## 🎯 TOTAL DE MODELOS: 19

### Distribución por módulo:
- **Seguridad**: 4 modelos (User, Rol, Permiso, RolPermiso)
- **Clientes**: 1 modelo
- **Subcontratistas**: 1 modelo
- **Proveedores**: 2 modelos (Proveedor, Articulo)
- **Servicios**: 1 modelo
- **Presupuestos**: 2 modelos (Presupuesto, PresupuestoDetalle)
- **Órdenes de Servicio**: 2 modelos (OrdenServicio, OrdenServicioDetalle)
- **Cuentas por Cobrar**: 2 modelos (FacturaCliente, PagoCliente)
- **Cuentas por Pagar**: 2 modelos (FacturaSubcontratista, PagoSubcontratista)
- **Configuración**: 2 modelos (Empresa, Parametro)

---

## ✅ PRÓXIMOS PASOS

1. ✅ Análisis completado
2. ⏳ Generar `draft.yaml` con Blueprint
3. ⏳ Ejecutar `php artisan blueprint:build`
4. ⏳ Revisar migraciones generadas
5. ⏳ Ejecutar migraciones
6. ⏳ Configurar seeders iniciales
