---
id: reference.fieldAndStatus
title: Referencia de campos y estados
slug: referencia-de-campos-y-estados
section: referencia
---

# Referencia de campos y estados

Todos los conjuntos de opciones que encuentras en un formulario de KolleK, en un solo lugar fácil de recorrer. Cada grupo enlaza a la guía que lo usa. Para las definiciones de los propios términos, consulta el @doc(reference.glossary, "glosario").

## Estados de un ejemplar

Se define en cada ejemplar que registras. Se usa en @doc(copies.track).

| Estado | Significado |
| --- | --- |
| En posesión | Tienes este ejemplar. El valor predeterminado para un ejemplar nuevo. |
| Pedido | Comprado o reservado, en camino hacia ti. |
| Prestado | Con otra persona por ahora. La custodia cambió, la propiedad no. |
| Vendido | Lo vendiste y ya no lo posees. |
| Regalado | Lo regalaste. |
| Perdido | No lo encuentras y no esperas hacerlo. |
| Robado | Te lo quitaron. |
| Desechado | Descartado o reciclado, con una fecha de desecho opcional. |
| Otro | Cualquier cosa que la lista anterior no cubra. |

:::note
En posesión, Pedido y Prestado cuentan como aún en tu poder. Un ejemplar prestado sigue siendo tuyo, simplemente está en otro lugar.
:::

## Tipos de transacción

Se define en cada transacción. Se usa en @doc(copies.recordPaymentsAndValue). Los tipos marcados como de adquisición traen un ejemplar a tus manos, y la transacción de adquisición más antigua proporciona la fecha de adquisición del ejemplar.

| Tipo | Significado |
| --- | --- |
| Compra | Compraste el ejemplar. Adquisición. |
| Venta | Vendiste el ejemplar. |
| Intercambio | Cambiaste algo por él. Adquisición. |
| Regalo recibido | Alguien te lo dio. Adquisición. |
| Regalo dado | Se lo diste a alguien. |
| Herencia | Pasó a ser tuyo. Adquisición. |
| Reembolso | Dinero devuelto de una transacción anterior. |
| Comisión | Un coste asociado al ejemplar, como una comisión de subasta. |
| Impuesto | Un impuesto pagado sobre el ejemplar. |
| Envío | Un coste de entrega registrado por separado. |
| Otro | Cualquier evento de dinero que la lista no cubra. |

## Tipos y confianza de una valoración

Se define en cada valoración. Se usa en @doc(copies.recordPaymentsAndValue).

| Tipo de valoración | Significado |
| --- | --- |
| Estimación propia | Tu propio juicio sobre el valor. |
| Tasación profesional | Una tasación formal realizada por un profesional. |
| Estimación de mercado | Derivada de datos actuales de mercado o de ventas. |
| Valor de seguro | El valor usado para fines de seguro. |
| Estimación de subasta | Una estimación dada por una casa de subastas. |
| Estimación automatizada | Generada por un servicio o herramienta de tasación. |
| Otro | Cualquier otra base para el valor. |

| Confianza | Significado |
| --- | --- |
| Baja | Una estimación aproximada. |
| Media | Razonablemente fundamentada. |
| Alta | Bien respaldada, como una tasación profesional reciente. |
| Desconocida | La confianza no se registró. |

## Estados de un registro de seguro

Se define en cada registro de seguro. Se usa en @doc(copies.insure). El tipo de cobertura en un registro de seguro es texto libre, así que no tiene una lista fija de opciones.

| Estado | Significado |
| --- | --- |
| Activo | La póliza cubre actualmente el ejemplar. |
| Vencido | El período de cobertura ha terminado. |
| Cancelado | La póliza se canceló antes de su fecha de fin. |
| Pendiente | La cobertura está acordada pero aún no está en vigor. |

## Direcciones y estados de un préstamo

Se define en cada préstamo. Se usa en @doc(loans.lendAndBorrow).

| Dirección | Significado |
| --- | --- |
| Prestado a otros | Tu ejemplar salió de tus manos, por ejemplo a un amigo o a una exhibición. |
| Recibido en préstamo | La pieza de otra persona está en tus manos. |

| Estado | Significado |
| --- | --- |
| Planeado | Acordado pero aún no entregado. |
| Activo | El ejemplar está actualmente fuera (o dentro). |
| Vencido | Sigue fuera después de su fecha límite. KolleK marca esto automáticamente cada día. |
| Devuelto | El préstamo terminó y el ejemplar volvió. |
| Cancelado | El préstamo nunca se llevó a cabo. |
| Perdido | El ejemplar no volvió. |

## Tipos de mantenimiento

Se define en cada registro de mantenimiento. Se usa en @doc(copies.recordMaintenance).

| Tipo | Significado |
| --- | --- |
| Limpieza | Limpieza rutinaria. |
| Reparación | Arreglo de daños. |
| Revisión | Mantenimiento periódico, como la revisión de un reloj. |
| Conservación | Trabajo para estabilizar y preservar. |
| Restauración | Trabajo para devolver el ejemplar a un estado anterior. |
| Sustitución | Sustitución de una pieza o componente. |
| Inspección | Una comprobación sin intervención. |

## Tipos de evento de procedencia y precisión de fecha

Se define en cada evento de procedencia. Se usa en @doc(copies.traceProvenance).

| Tipo de evento | Significado |
| --- | --- |
| Adquisición | El ejemplar entró en una colección. |
| Venta | El ejemplar fue vendido. |
| Regalo | El ejemplar cambió de manos como regalo. |
| Herencia | El ejemplar pasó a través de una herencia. |
| Transferencia de propiedad | La propiedad cambió de otra manera. |
| Transferencia de custodia | El ejemplar se movió sin cambiar de propietario. |
| Préstamo | El ejemplar salió en préstamo. |
| Devolución | El ejemplar volvió de un préstamo. |
| Exhibición | El ejemplar se mostró públicamente. |
| Autenticación | El ejemplar se verificó como genuino. |
| Tasación | El ejemplar se valoró formalmente. |
| Restauración significativa | Trabajo importante que forma parte de la historia. |
| Origen | Dónde y cuándo se hizo el ejemplar. |
| Descubrimiento | El ejemplar fue encontrado o redescubierto. |
| Otro | Cualquier otro capítulo de la historia. |

Las fechas de procedencia suelen ser inciertas, así que cada evento lleva una precisión:

| Precisión | Significado |
| --- | --- |
| Fecha exacta | Se conoce la fecha completa. |
| Mes | Se conoce hasta el mes. |
| Año | Se conoce hasta el año. |
| Aproximada | Una mejor estimación. Léela como circa. |
| Desconocida | No hay fecha registrada. |

## Tipos de documento

Se define en cada documento. Se usa en @doc(copies.attachDocuments).

| Tipo | Significado |
| --- | --- |
| Recibo | Prueba de una compra. |
| Factura | Una factura por el ejemplar o por trabajo realizado en él. |
| Certificado | Un certificado que venía con el ejemplar. |
| Tasación | Una valoración escrita. |
| Seguro | Documentación de la póliza. |
| Fotografía | Una foto guardada como registro y no como imagen de galería. |
| Informe de estado | Una evaluación escrita del estado. |
| Informe de restauración | Un registro del trabajo de restauración. |
| Catálogo | Una entrada o listado de catálogo. |
| Correspondencia | Cartas o correos sobre el ejemplar. |
| Registro de propiedad | Documentación que prueba la propiedad. |
| Registro de autenticidad | Documentación que prueba que el ejemplar es genuino. |
| Otro | Cualquier otra cosa que valga la pena guardar. |

## Tipos de campo personalizado

Se elige al definir un campo personalizado en un tipo de colección. Se usa en @doc(collectionTypes.setup).

| Tipo de campo | Significado |
| --- | --- |
| Texto | Texto libre, como un autor o una editorial. |
| Número | Un valor numérico, como un número de edición. |
| Fecha | Una fecha del calendario, como una fecha de lanzamiento. |
| Sí / No | Una casilla de verificación, como "Firmado". |
| Selección | Una opción entre una lista de opciones que tú defines. |
| Valoración con estrellas | Una valoración con estrellas, hasta cinco. |

## Visibilidad de la colección

Se define en cada colección. Se usa en @doc(collections.share). El ajuste se registra hoy y se aplicará en cuanto llegue la función de compartir; ver @doc(troubleshooting.featureStatus).

| Visibilidad | Significado |
| --- | --- |
| Privada | Destinada solo para ti. |
| Compartida | Destinada para todos en tu cuenta. |
| Pública | Destinada para cualquiera con el enlace, solo lectura, sin iniciar sesión. |

## A dónde ir ahora

- Qué significan los términos: @doc(reference.glossary).
- Los registros donde viven estas opciones: @doc(copyHistory.concept, "El historial de un ejemplar explicado").
