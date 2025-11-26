

CREATE TABLE rol (

    id SERIAL PRIMARY KEY,

    nombre VARCHAR(255) NOT NULL UNIQUE,

    descripcion VARCHAR(500),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP

);
 
CREATE TABLE capacitacion (

    id SERIAL PRIMARY KEY,

    nombre VARCHAR(255) NOT NULL,

    descripcion VARCHAR(255),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP

);
 
CREATE TABLE necesidad (

    id SERIAL PRIMARY KEY,

    tipo VARCHAR(255) NOT NULL,

    descripcion VARCHAR(255),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP

);
 
CREATE TABLE test (

    id SERIAL PRIMARY KEY,

    nombre VARCHAR(255) NOT NULL,

    categoria VARCHAR(255),

    descripcion VARCHAR(255),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP

);
 
CREATE TABLE universidad (

    id SERIAL PRIMARY KEY,

    nombre VARCHAR(255) NOT NULL,

    direccion VARCHAR(255),

    telefono VARCHAR(255),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP

);
 
-- TABLAS CON DEPENDENCIAS DE NIVEL 1

CREATE TABLE usuario (

    id_usuario SERIAL PRIMARY KEY,

    nombres VARCHAR(255) NOT NULL,

    apellidos VARCHAR(255) NOT NULL,

    ci VARCHAR(255) UNIQUE NOT NULL,

    fecha_nacimiento DATE,

    genero VARCHAR(50),

    telefono VARCHAR(255),

    email VARCHAR(255) UNIQUE,

    direccion_domicilio VARCHAR(255),

    contrasena VARCHAR(255) NOT NULL,

    estado VARCHAR(50) DEFAULT 'activo',

    id_rol INT NOT NULL,

    nivel_entrenamiento VARCHAR(255),

    entidad_pertenencia VARCHAR(255),

    tipo_sangre VARCHAR(10),

    foto_ci VARCHAR(255),

    licencia_conducir VARCHAR(255),

    foto_licencia VARCHAR(255),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_rol

        FOREIGN KEY (id_rol)

        REFERENCES rol(id)

        ON DELETE RESTRICT

        ON UPDATE CASCADE

);
 
CREATE TABLE curso (

    id SERIAL PRIMARY KEY,

    nombre VARCHAR(255) NOT NULL,

    descripcion VARCHAR(255),

    id_capacitacion INT NOT NULL,

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_curso_capacitacion

        FOREIGN KEY (id_capacitacion)

        REFERENCES capacitacion(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
CREATE TABLE pregunta (

    id SERIAL PRIMARY KEY,

    texto VARCHAR(255) NOT NULL,

    tipo VARCHAR(255),

    id_test INT NOT NULL,

    orden INT,

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pregunta_test

        FOREIGN KEY (id_test)

        REFERENCES test(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
CREATE TABLE historial_clinico (

    id SERIAL PRIMARY KEY,

    id_usuario INT NOT NULL UNIQUE,

    fecha_inicio TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_historial_usuario

        FOREIGN KEY (id_usuario)

        REFERENCES usuario(id_usuario)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
-- TABLA REPORTE

CREATE TABLE reporte (

    id SERIAL PRIMARY KEY,

    estado_general VARCHAR(255),

    fecha_generado TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    observaciones VARCHAR(2000),

    recomendaciones VARCHAR(255),

    resumen_emocional VARCHAR(1000),

    resumen_fisico VARCHAR(1000)

);
 
-- TABLAS CON DEPENDENCIAS DE NIVEL 3

CREATE TABLE etapa (

    id SERIAL PRIMARY KEY,

    nombre VARCHAR(255) NOT NULL,

    orden INT NOT NULL,

    id_curso INT NOT NULL,

    descripcion VARCHAR(255),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_etapa_curso

        FOREIGN KEY (id_curso)

        REFERENCES curso(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
CREATE TABLE progreso_voluntario (

    id SERIAL PRIMARY KEY,

    id_usuario INT NOT NULL,

    id_etapa INT NOT NULL,

    estado VARCHAR(255) DEFAULT 'en_progreso',

    fecha_inicio TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    fecha_finalizacion TIMESTAMP(6),

    UNIQUE(id_usuario, id_etapa),

    CONSTRAINT fk_progreso_usuario

        FOREIGN KEY (id_usuario)

        REFERENCES usuario(id_usuario)

        ON DELETE CASCADE

        ON UPDATE CASCADE,

    CONSTRAINT fk_progreso_etapa

        FOREIGN KEY (id_etapa)

        REFERENCES etapa(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
CREATE TABLE evaluacion (

    id SERIAL PRIMARY KEY,

    id_reporte INT NOT NULL,

    id_test INT NOT NULL,

    id_universidad INT,

    fecha TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_evaluacion_reporte

        FOREIGN KEY (id_reporte)

        REFERENCES reporte(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE,

    CONSTRAINT fk_evaluacion_test

        FOREIGN KEY (id_test)

        REFERENCES test(id)

        ON DELETE RESTRICT

        ON UPDATE CASCADE,

    CONSTRAINT fk_evaluacion_universidad

        FOREIGN KEY (id_universidad)

        REFERENCES universidad(id)

        ON DELETE SET NULL

        ON UPDATE CASCADE

);
 
-- RELACIONES DEL REPORTE 

-- Lo que el reporte resume: progresos concretos

CREATE TABLE reporte_progreso_voluntario (

    id_reporte  INT NOT NULL,

    id_progreso INT NOT NULL,

    PRIMARY KEY (id_reporte, id_progreso),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_rpv_reporte

        FOREIGN KEY (id_reporte)

        REFERENCES reporte(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE,

    CONSTRAINT fk_rpv_progreso

        FOREIGN KEY (id_progreso)

        REFERENCES progreso_voluntario(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
-- Necesidades asociadas al reporte (no derivables)

CREATE TABLE reporte_necesidad (

    id_reporte INT NOT NULL,

    id_necesidad INT NOT NULL,

    PRIMARY KEY (id_reporte, id_necesidad),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reporte_necesidad_reporte

        FOREIGN KEY (id_reporte)

        REFERENCES reporte(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE,

    CONSTRAINT fk_reporte_necesidad_necesidad

        FOREIGN KEY (id_necesidad)

        REFERENCES necesidad(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
-- VISTA que reemplaza a la tabla redundante reporte_capacitacion

CREATE OR REPLACE VIEW vw_reporte_capacitacion AS

SELECT DISTINCT

    rpv.id_reporte,

    c.id AS id_capacitacion

FROM reporte_progreso_voluntario rpv

JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso

JOIN etapa e               ON e.id = pv.id_etapa

JOIN curso cu              ON cu.id = e.id_curso

JOIN capacitacion c        ON c.id = cu.id_capacitacion;
 
 
-- RESPUESTAS DE EVALUACIÓN

CREATE TABLE respuesta (

    id SERIAL PRIMARY KEY,

    id_evaluacion INT NOT NULL,

    texto_pregunta VARCHAR(255),

    respuesta_texto VARCHAR(255),

    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_respuesta_evaluacion

        FOREIGN KEY (id_evaluacion)

        REFERENCES evaluacion(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

);
 
-- ÍNDICES

CREATE INDEX idx_usuario_rol           ON usuario(id_rol);

CREATE INDEX idx_curso_capacitacion    ON curso(id_capacitacion);

CREATE INDEX idx_etapa_curso           ON etapa(id_curso);

CREATE INDEX idx_pregunta_test         ON pregunta(id_test);

CREATE INDEX idx_historial_usuario     ON historial_clinico(id_usuario);

CREATE INDEX idx_progreso_usuario      ON progreso_voluntario(id_usuario);

CREATE INDEX idx_progreso_etapa        ON progreso_voluntario(id_etapa);

CREATE INDEX idx_evaluacion_reporte    ON evaluacion(id_reporte);

CREATE INDEX idx_evaluacion_test       ON evaluacion(id_test);

CREATE INDEX idx_respuesta_evaluacion  ON respuesta(id_evaluacion);

CREATE INDEX idx_usuario_ci            ON usuario(ci);

CREATE INDEX idx_usuario_email         ON usuario(email);

CREATE INDEX idx_usuario_estado        ON usuario(estado);

CREATE INDEX idx_reporte_fecha         ON reporte(fecha_generado);

CREATE INDEX idx_evaluacion_fecha      ON evaluacion(fecha);

CREATE INDEX idx_rpv_reporte           ON reporte_progreso_voluntario(id_reporte);

CREATE INDEX idx_rpv_progreso          ON reporte_progreso_voluntario(id_progreso);
 
-- TRIGGERS

-- 1) Para tablas con columna 'updated_at' (usuario)

CREATE OR REPLACE FUNCTION set_updated_at()

RETURNS TRIGGER AS $$

BEGIN

    NEW.updated_at = CURRENT_TIMESTAMP;

    RETURN NEW;

END;

$$ LANGUAGE plpgsql;
 
CREATE TRIGGER trg_usuario_set_updated

BEFORE UPDATE ON usuario

FOR EACH ROW

EXECUTE FUNCTION set_updated_at();
 
-- 2) Para historial_clinico (columna 'fecha_actualizacion')

CREATE OR REPLACE FUNCTION set_fecha_actualizacion()

RETURNS TRIGGER AS $$

BEGIN

    NEW.fecha_actualizacion = CURRENT_TIMESTAMP;

    RETURN NEW;

END;

$$ LANGUAGE plpgsql;
 
CREATE TRIGGER trg_historial_set_fecha

BEFORE UPDATE ON historial_clinico

FOR EACH ROW

EXECUTE FUNCTION set_fecha_actualizacion();
 
-- DATOS INICIALES

INSERT INTO rol (nombre, descripcion) VALUES

    ('Administrador', 'Acceso total al sistema'),

    ('Voluntario', 'Usuario voluntario del sistema'),

    ('Instructor', 'Instructor de capacitaciones'),

    ('Evaluador', 'Responsable de evaluaciones');

 