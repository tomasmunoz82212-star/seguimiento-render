-- PostgreSQL schema for Seguimiento CRU 
-- =====================================================
-- BASE DE DATOS COMPLETA - SISTEMA DE SEGUIMIENTO CRU
-- VERSIÓN PARA POSTGRESQL
-- =====================================================

DROP DATABASE IF EXISTS seg_database;
CREATE DATABASE seg_database;
\c seg_database;

-- =====================================================
-- 1. TABLA: ROLES
-- =====================================================
CREATE TABLE roles (
    id     SERIAL PRIMARY KEY,
    sigla  VARCHAR(10)  NOT NULL UNIQUE,
    nombre VARCHAR(50)  NOT NULL UNIQUE
);

INSERT INTO roles (sigla, nombre) VALUES 
    ('ADM', 'administrador'),
    ('DOC', 'docente'),
    ('BIE', 'bienestar'),
    ('COO', 'coordinacion');

-- =====================================================
-- 2. TABLA: PERSONAS
-- =====================================================
CREATE TABLE personas (
    id               SERIAL PRIMARY KEY,
    primer_nombre    VARCHAR(50)  NOT NULL,
    segundo_nombre   VARCHAR(50)  NULL,
    primer_apellido  VARCHAR(50)  NOT NULL,
    segundo_apellido VARCHAR(50)  NULL,
    documento        VARCHAR(20)  NOT NULL UNIQUE,
    correo           VARCHAR(100) NULL,
    telefono         VARCHAR(20)  NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. TABLA: USUARIOS
-- =====================================================
CREATE TABLE usuarios (
    id         SERIAL PRIMARY KEY,
    persona_id INT          NOT NULL,
    usuario    VARCHAR(50)  NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    rol_id     INT          NOT NULL,
    estado     VARCHAR(20) DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (persona_id) REFERENCES personas(id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id)     REFERENCES roles(id)
);

-- =====================================================
-- 4. TABLA: PROGRAMAS
-- =====================================================
CREATE TABLE programas (
    id     SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    tipo   VARCHAR(20) NOT NULL DEFAULT 'profesional' CHECK (tipo IN ('profesional', 'tecnologia'))
);

INSERT INTO programas (nombre, tipo) VALUES
    ('Contaduría Pública',                       'profesional'),
    ('Ingeniería Informática',                   'profesional'),
    ('Tecnología en Desarrollo de Software',     'tecnologia'),
    ('Tecnología en Gestión Logística Integral', 'tecnologia');

-- =====================================================
-- 5. TABLA: MATERIAS
-- =====================================================
CREATE TABLE materias (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    programa_id INT          NOT NULL,
    FOREIGN KEY (programa_id) REFERENCES programas(id) ON DELETE CASCADE
);

-- MATERIAS - CONTADURÍA PÚBLICA (programa_id = 1)
INSERT INTO materias (nombre, programa_id) VALUES
('Introducción al Área Profesional',1),
('Fundamentos de Economía',1),
('Fundamentos de Contabilidad',1),
('Matemáticas',1),
('Humanidades 1 (Cultura Contemporánea)',1),
('Pedagogía Constitucional',1),
('Habilidades Comunicativas',1),
('Economía Política',1),
('Hermenéutica Jurídica',1),
('Contabilidad de Recursos Financieros',1),
('Informática Empresarial',1),
('Humanidades 2 (Cultura Política)',1),
('Ética',1),
('Álgebra y Programación Lineal',1),
('Fundamentos de Administración',1),
('Microeconomía',1),
('Legislación Societaria y Comercial',1),
('Contabilidad de Inversión y Financiación',1),
('Ecología',1),
('Humanidades 3 (Geopolítica)',1),
('Estadística',1),
('Electiva 1',1),
('Matemáticas Financieras',1),
('Macroeconomía',1),
('Legislación Laboral',1),
('Presentación y Revelación de Estados Financieros',1),
('Gestión Humana',1),
('Deporte, Arte y Recreación',1),
('Investigación de Operaciones',1),
('Economía Colombiana',1),
('Contabilidad de Fenómenos Societarios',1),
('Introducción a los Sistemas de Costos',1),
('Finanzas Internacionales',1),
('Administración Contemporánea',1),
('Electiva 2',1),
('Finanzas Públicas',1),
('Contabilidad de Actividades Especiales',1),
('Sistemas Contables',1),
('Sistemas de Gestión de Costos',1),
('Mercado de Capitales',1),
('Teoría Contable',1),
('Contabilidad Ambiental',1),
('Metodología de la Investigación',1),
('Presupuesto',1),
('Finanzas Corporativas',1),
('Fundamentos de Control',1),
('Electiva Práctica',1),
('Optativa: Derecho Procesal Tributario',1),
('Optativa: Habilidades Gerenciales',1),
('Fundamentos de Contabilidad Pública',1),
('Contabilidad de Gestión',1),
('Procesos de Investigación Contable',1),
('Legislación Tributaria',1),
('Aseguramiento y Revisoría Fiscal',1),
('Emprendimiento Empresarial',1),
('Formulación y Evaluación de Proyectos',1),
('Negocios Internacionales',1),
('Contabilidad Pública y Control Social',1),
('Procedimiento Tributario',1),
('Control Fiscal',1),
('Cátedra para la Paz',1),
('Optativa: Régimen Tributario',1),
('Optativa: Estrategias Gerenciales',1),
('Seminario de Grado',1),
('Consultoría Organizacional',1),
('Proyecto de Investigación',1),
('Práctica Profesional',1);

-- MATERIAS - INGENIERÍA INFORMÁTICA (programa_id = 2)
INSERT INTO materias (nombre, programa_id) VALUES
('Introducción al Área Profesional',2),
('Algoritmos y Programación 1',2),
('Lengua Materna',2),
('Cálculo Diferencial',2),
('Humanidades 1',2),
('Matemáticas Discretas 1',2),
('Algoritmos y Programación 2',2),
('Matemáticas Discretas 2',2),
('Física del Movimiento',2),
('Geometría Vectorial',2),
('Cálculo Integral',2),
('Deporte, Arte y Recreación',2),
('Algoritmos y Programación 3',2),
('Taller de Lenguajes de Programación 1',2),
('Semiótica Informática',2),
('Cálculo de Varias Variables',2),
('Electricidad y Magnetismo',2),
('Álgebra Lineal',2),
('Pedagogía Constitucional',2),
('Algoritmos y Programación 4',2),
('Análisis de Software',2),
('Bases de Datos 1',2),
('Ecuaciones Diferenciales',2),
('Electrónica Digital',2),
('Taller de Lenguajes de Programación 2',2),
('Diseño de Software',2),
('Arquitectura de Hardware',2),
('Teoría de Lenguajes y Compiladores',2),
('Estadística Aplicada',2),
('Proyecto de Construcción de SW',2),
('Bases de Datos 2',2),
('Sistemas Operativos',2),
('Emprendimiento Empresarial TI',2),
('Teoría de la Información',2),
('Análisis Numérico',2),
('Inteligencia Artificial',2),
('Pruebas y Gestión de la Configuración',2),
('Sistemas y Organizaciones',2),
('Redes de Comunicación',2),
('Investigación de Operaciones',2),
('Ecología',2),
('Metodología de la Investigación',2),
('Programación Distribuida y Paralela',2),
('Proyecto Integrador',2),
('Formulación y Evaluación de Proyectos de TI',2),
('Gestión de Redes y Servicios',2),
('Profundización 1',2),
('Modelos y Simulación',2),
('Humanidades 2',2),
('Gestión de Proyectos de TI',2),
('Profundización 2',2),
('Electiva 1',2),
('Electiva 2',2),
('Ética',2),
('Trabajo de Grado',2),
('Profundización 3',2),
('Electiva 3',2);

-- MATERIAS - TECNOLOGÍA EN DESARROLLO DE SOFTWARE (programa_id = 3)
INSERT INTO materias (nombre, programa_id) VALUES
('Lengua Materna',3),
('Humanidades 1',3),
('Matemáticas Operativas',3),
('Introducción al Programa',3),
('Fundamentos del Desarrollo de Software',3),
('Algoritmos y Programación 1',3),
('Cálculo Diferencial',3),
('Matemáticas Discretas',3),
('Algoritmos y Programación 2',3),
('Redes de Comunicación de Datos 1',3),
('Bases de Datos 1',3),
('Análisis y Diseño de Software',3),
('Fundamentos de los Sistemas Operativos',3),
('Taller de Lenguajes de Programación',3),
('Estructura de Datos 1',3),
('Estadística Aplicada',3),
('Redes de Comunicación de Datos 2',3),
('Deporte, Arte y Recreación',3),
('Humanidades 2',3),
('Matemáticas Financieras',3),
('Fundamentos de la Electrónica y Circuitos Digitales',3),
('Bases de Datos 2',3),
('Proyecto de Construcción de Software',3),
('Estructuras de Datos 2',3),
('Pedagogía Constitucional',3),
('Sistemas de Información',3),
('Pruebas y Gestión de la Configuración',3),
('Metodología de la Investigación',3),
('Ecología y Desarrollo Sostenible',3),
('Electiva',3),
('Profundización 1',3),
('Emprendimiento Empresarial',3),
('Profundización 2',3),
('Práctica Profesional',3);

-- MATERIAS - TECNOLOGÍA EN GESTIÓN LOGÍSTICA INTEGRAL (programa_id = 4)
INSERT INTO materias (nombre, programa_id) VALUES
('Matemáticas Operativas',4),
('Fundamentos de Administración',4),
('Lengua Materna',4),
('Humanidades I',4),
('Fundamentos de Logística Integral',4),
('Introducción al Área Profesional',4),
('Inglés Técnico',4),
('Cálculo Diferencial',4),
('Contabilidad General',4),
('Humanidades II',4),
('Deporte, Arte y Recreación',4),
('Informática I',4),
('Ética',4),
('Gerencia de Compras',4),
('Emprendimiento Empresarial',4),
('Cálculo Integral',4),
('Costos y Presupuestos',4),
('Informática II',4),
('Empaque y Embalaje',4),
('Logística de Transporte y Distribución I',4),
('Estadística',4),
('Gestión de Mercados y Logística',4),
('Pedagogía Constitucional',4),
('Sistemas de Información Logísticos',4),
('Logística de Transporte y Distribución II',4),
('Almacenamiento y Gestión de Inventarios',4),
('Investigación de Operaciones',4),
('Metodología de la Investigación',4),
('Gestión del Talento Humano',4),
('Electiva I',4),
('DFI',4),
('Modelo de Gestión ISO 9000',4),
('SCM',4),
('Formulación y Evaluación de Proyectos',4),
('Ecología',4),
('Salud Ocupacional',4),
('Gestión Integral del Servicio',4),
('Electiva II',4),
('Práctica Empresarial',4),
('Logística Inversa',4),
('Legislación Aduanera',4);

-- =====================================================
-- 6. TABLA: PERIODOS
-- =====================================================
CREATE TABLE periodos (
    id           SERIAL PRIMARY KEY,
    nombre       VARCHAR(20) NOT NULL UNIQUE,
    fecha_inicio DATE        NOT NULL,
    fecha_fin    DATE        NOT NULL,
    estado       VARCHAR(20) DEFAULT 'activo' CHECK (estado IN ('activo', 'cerrado')),
    creado_en    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 7. TABLA: ESTUDIANTES
-- =====================================================
CREATE TABLE estudiantes (
    id         SERIAL PRIMARY KEY,
    documento  VARCHAR(20)  NOT NULL UNIQUE,
    nombre     VARCHAR(100) NOT NULL,
    correo     VARCHAR(100) NULL,
    telefono   VARCHAR(20)  NULL
);

-- =====================================================
-- 8. TABLA: MATRICULAS
-- =====================================================
CREATE TABLE matriculas (
    id            SERIAL PRIMARY KEY,
    estudiante_id INT     NOT NULL,
    periodo_id    INT     NOT NULL,
    programa_id   INT     NOT NULL,
    semestre      SMALLINT NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
    FOREIGN KEY (periodo_id)    REFERENCES periodos(id) ON DELETE CASCADE,
    FOREIGN KEY (programa_id)   REFERENCES programas(id),
    CONSTRAINT unica_matricula UNIQUE (estudiante_id, periodo_id)
);

-- =====================================================
-- 9. TABLA: REPORTES
-- =====================================================
CREATE TABLE reportes (
    id                        SERIAL PRIMARY KEY,
    estudiante_id             INT  NOT NULL,
    periodo_id                INT  NOT NULL,
    usuario_id                INT  NOT NULL,
    programa_id               INT  NOT NULL,
    materia_id                INT  NULL,
    tipo                      VARCHAR(20) NOT NULL CHECK (tipo IN ('academico', 'asistencia', 'comportamiento')),
    descripcion               TEXT NOT NULL,
    estado                    VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'en_seguimiento', 'cerrado')),
    fecha_limite_seguimiento  TIMESTAMP NULL,
    nivel_alerta              VARCHAR(20) DEFAULT 'verde' CHECK (nivel_alerta IN ('verde', 'naranja', 'rojo', 'expirado')),
    creado_en                 TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id),
    FOREIGN KEY (periodo_id)    REFERENCES periodos(id),
    FOREIGN KEY (usuario_id)    REFERENCES usuarios(id),
    FOREIGN KEY (programa_id)   REFERENCES programas(id),
    FOREIGN KEY (materia_id)    REFERENCES materias(id)
);

-- =====================================================
-- 10. TABLA: SEGUIMIENTOS_BIENESTAR
-- =====================================================
CREATE TABLE seguimientos_bienestar (
    id                      SERIAL PRIMARY KEY,
    reporte_id              INT NOT NULL,
    usuario_id              INT NOT NULL,
    dificultad_economica    BOOLEAN DEFAULT FALSE,
    trabaja_y_estudia       BOOLEAN DEFAULT FALSE,
    falta_apoyo_familiar    BOOLEAN DEFAULT FALSE,
    ansiedad_estres         BOOLEAN DEFAULT FALSE,
    depresion_tristeza      BOOLEAN DEFAULT FALSE,
    baja_autoestima         BOOLEAN DEFAULT FALSE,
    desmotivacion           BOOLEAN DEFAULT FALSE,
    problema_salud_fisica   BOOLEAN DEFAULT FALSE,
    problema_salud_mental   BOOLEAN DEFAULT FALSE,
    conflicto_pares         BOOLEAN DEFAULT FALSE,
    conflicto_docentes      BOOLEAN DEFAULT FALSE,
    bullying_acoso          BOOLEAN DEFAULT FALSE,
    dificultad_aprendizaje  BOOLEAN DEFAULT FALSE,
    problema_adaptacion     BOOLEAN DEFAULT FALSE,
    falta_habitos_estudio   BOOLEAN DEFAULT FALSE,
    problema_familiar       BOOLEAN DEFAULT FALSE,
    responsabilidad_hogar   BOOLEAN DEFAULT FALSE,
    otro                    BOOLEAN DEFAULT FALSE,
    detalle_otro            TEXT NULL,
    razon_cierre            TEXT NULL,
    estado                  VARCHAR(20) DEFAULT 'en_proceso' CHECK (estado IN ('en_proceso', 'cerrado')),
    creado_en               TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporte_id)  REFERENCES reportes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id) ON DELETE CASCADE
);

-- =====================================================
-- 11. TABLA: OBSERVACIONES_SEGUIMIENTO
-- =====================================================
CREATE TABLE observaciones_seguimiento (
    id                 SERIAL PRIMARY KEY,
    seguimiento_id     INT NOT NULL,
    usuario_id         INT NOT NULL,
    medio_contacto     VARCHAR(20) NOT NULL,
    contacto_fallido   BOOLEAN DEFAULT FALSE,
    motivo_no_contacto VARCHAR(255) NULL,
    observacion        TEXT NOT NULL,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seguimiento_id) REFERENCES seguimientos_bienestar(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)     REFERENCES usuarios(id)
);

-- =====================================================
-- 12. TABLA: CONFIGURACION_SISTEMA
-- =====================================================
CREATE TABLE configuracion_sistema (
    id                       INT PRIMARY KEY DEFAULT 1,
    dias_limite_seguimiento  INT DEFAULT 5,
    dias_alerta_naranja      INT DEFAULT 3,
    dias_alerta_roja         INT DEFAULT 1,
    modo_prueba_minutos      BOOLEAN DEFAULT FALSE,
    CONSTRAINT check_id CHECK (id = 1)
);

INSERT INTO configuracion_sistema (id, modo_prueba_minutos) VALUES (1, TRUE);

-- =====================================================
-- 13. TABLA: NOTIFICACIONES
-- =====================================================
CREATE TABLE notificaciones (
    id          SERIAL PRIMARY KEY,
    usuario_id  INT NOT NULL,
    reporte_id  INT NULL,
    periodo_id  INT NULL,
    tipo        VARCHAR(50) NOT NULL,
    mensaje     TEXT NOT NULL,
    leida       BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (reporte_id) REFERENCES reportes(id) ON DELETE CASCADE,
    FOREIGN KEY (periodo_id) REFERENCES periodos(id) ON DELETE SET NULL
);

-- =====================================================
-- 14. TABLA: PASSWORD_RESET_TOKENS
-- =====================================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    codigo VARCHAR(6) NULL,
    created_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL
);

-- =====================================================
-- 15. ÍNDICES
-- =====================================================

CREATE INDEX idx_personas_documento ON personas(documento);
CREATE INDEX idx_usuarios_usuario ON usuarios(usuario);
CREATE INDEX idx_usuarios_estado ON usuarios(estado);
CREATE INDEX idx_usuarios_rol ON usuarios(rol_id);
CREATE INDEX idx_estudiantes_documento ON estudiantes(documento);
CREATE INDEX idx_estudiantes_nombre ON estudiantes(nombre);
CREATE INDEX idx_periodos_estado ON periodos(estado);
CREATE INDEX idx_matriculas_estudiante ON matriculas(estudiante_id);
CREATE INDEX idx_matriculas_periodo ON matriculas(periodo_id);
CREATE INDEX idx_matriculas_estudiante_periodo ON matriculas(estudiante_id, periodo_id);
CREATE INDEX idx_reportes_estado ON reportes(estado);
CREATE INDEX idx_reportes_periodo ON reportes(periodo_id);
CREATE INDEX idx_reportes_estudiante ON reportes(estudiante_id);
CREATE INDEX idx_reportes_nivel_alerta ON reportes(nivel_alerta);
CREATE INDEX idx_notificaciones_usuario ON notificaciones(usuario_id);
CREATE INDEX idx_notificaciones_periodo ON notificaciones(periodo_id);
CREATE INDEX idx_notificaciones_leida ON notificaciones(leida);
CREATE INDEX idx_materias_programa ON materias(programa_id);
CREATE INDEX idx_password_reset_email ON password_reset_tokens(email);

-- =====================================================
-- ACTIVAR MODO PRUEBA
-- =====================================================
UPDATE configuracion_sistema SET modo_prueba_minutos = TRUE;

-- =====================================================
-- CREAR USUARIO ADMINISTRADOR
-- =====================================================

-- 1. Insertar la persona (administrador)
INSERT INTO personas (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, documento, correo, telefono) VALUES (
    'Admin', 
    NULL, 
    'CRU', 
    NULL, 
    '1000000000', 
    'admin@politecnicojic.edu.co', 
    '3000000000'
);

-- 2. Insertar el usuario administrador
-- Contraseña: password (en producción cámbiala)
INSERT INTO usuarios (
    persona_id, 
    usuario, 
    contraseña, 
    rol_id, 
    estado
) VALUES (
    (SELECT id FROM personas WHERE documento = '1000000000'),
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    (SELECT id FROM roles WHERE sigla = 'ADM'),
    'activo'
);