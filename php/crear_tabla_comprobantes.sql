-- Script SQL para crear la tabla de comprobantes
-- Ejecuta este script en tu base de datos MySQL

CREATE TABLE IF NOT EXISTS `comprobantes` (
  `id_comprobante` int(11) NOT NULL AUTO_INCREMENT,
  `id_departamento` int(11) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `anio` int(4) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `nombre_firmante` varchar(255) NOT NULL,
  `puesto_firmante` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_comprobante`),
  KEY `id_departamento` (`id_departamento`),
  UNIQUE KEY `unique_comprobante` (`id_departamento`, `mes`, `anio`),
  CONSTRAINT `fk_comprobante_departamento` FOREIGN KEY (`id_departamento`) 
    REFERENCES `ct_departamentos` (`id_departamento`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Explicación de la tabla:
-- - id_comprobante: ID único del comprobante (auto-incrementable)
-- - id_departamento: ID del departamento (relación con ct_departamentos)
-- - mes: Mes del comprobante (Enero, Febrero, etc.)
-- - anio: Año del comprobante (2023, 2024, etc.)
-- - cantidad: Cantidad a pagar (decimal con 2 decimales)
-- - nombre_firmante: Nombre de la persona que firma
-- - puesto_firmante: Puesto del firmante
-- - fecha_creacion: Fecha y hora en que se creó el comprobante
-- 
-- La restricción UNIQUE KEY `unique_comprobante` asegura que no se puedan
-- crear dos comprobantes para el mismo departamento, mes y año.



