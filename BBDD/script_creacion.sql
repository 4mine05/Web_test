/* =========================================================
   FORO (Usuarios, Posts, Comentarios)
   ========================================================= */

-- Recomendado para evitar errores de FK al recrear
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS foro;
CREATE DATABASE foro;
USE foro;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- TABLA: usuarios
-- - Login por email o username
-- - Password guardada como hash
-- =========================================================
CREATE TABLE usuarios (
  id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(30)  NOT NULL,
  email VARCHAR(120) NOT NULL,
  password VARCHAR(255) NOT NULL,    -- hash
  nombre VARCHAR(60)  NULL,
  apellidos VARCHAR(90)  NULL,
  fecha_nacimiento DATE NULL,
  avatar_url VARCHAR(255) NULL,
  bio VARCHAR(500) NULL,
  rol ENUM('user','admin') NOT NULL DEFAULT 'user',
  fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ultimo_login TIMESTAMP NULL DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,

  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuarios_username (username), -- Evitar repitición del username
  UNIQUE KEY uq_usuarios_email (email)      -- Evitar repitición del email
);

-- =========================================================
-- TABLA: posts
-- - Vinculada al creador
-- - Campos para feed (título, resumen, contenido completo)
-- - Estado y timestamps para ordenar/filtrar
-- =========================================================
CREATE TABLE posts (
  id_post INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT UNSIGNED NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  resumen VARCHAR(300) NULL,              -- para mostrar en feed
  contenido TEXT NOT NULL,                -- post completo
  categoria VARCHAR(50) NULL,             -- para filtrado simple
  estado ENUM('publicado','borrado','oculto') NOT NULL DEFAULT 'publicado',
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_edicion TIMESTAMP NULL DEFAULT NULL,
  num_visitas INT UNSIGNED NOT NULL DEFAULT 0,

  CONSTRAINT fk_posts_usuario             -- Relación Usuario - Post 
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

-- =========================================================
-- TABLA: comentarios (threads)
-- - Vinculada a post y usuario
-- - Permite replies (hilo) con id_comentario_padre (opcional)
-- =========================================================
CREATE TABLE comentarios (
    id_comentario INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_post INT UNSIGNED NOT NULL,
    id_usuario INT UNSIGNED NOT NULL,
    id_comentario_padre INT UNSIGNED NULL,      -- Respuestas a comentarios
    contenido VARCHAR(1000) NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_edicion TIMESTAMP NULL DEFAULT NULL,
    estado ENUM('visible','oculto', 'borrado') NOT NULL DEFAULT 'visible',

    CONSTRAINT fk_comentarios_post              -- Relación Post - Comentario
        FOREIGN KEY (id_post) REFERENCES posts(id_post)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_comentarios_usuario           -- Relación Usuario - Comentario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_comentarios_padre             -- Relación Comentario - Comentario (Respuestas)
        FOREIGN KEY (id_comentario_padre) REFERENCES comentarios(id_comentario)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

/* Post para comprobar el feed */
/*
INSERT INTO posts (id_usuario, titulo, resumen, contenido, categoria, estado)
VALUES (1, 'Bienvenidos a QuickForum', 'Este es el primer post de prueba para comprobar el feed.', 'Contenido completo del post. Aquí puedes escribir varias líneas y probar el salto de línea. \nSegunda línea del contenido.', 'General', 'publicado');
*/

INSERT INTO posts (id_usuario, titulo, resumen, contenido, categoria, estado)
VALUES (1, 'Bienvenidos a QuickForum', 'Este es el primer post de prueba para comprobar el feed.', 'Contenido completo del post. Aquí puedes escribir varias líneas y probar el salto de línea. \nSegunda línea del contenido.', 'General', 'publicado');
select * from posts;
select * from comentarios;
select * from usuarios;
