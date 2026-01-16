# QuickForum

Foro web desarrollado en **PHP + MySQL** siguiendo la estructura clásica de un foro: **usuarios**, **publicaciones (posts)** y **comentarios (threads)**.

## Funcionalidades

- **Registro de usuarios** e **inicio de sesión** contra base de datos.
- **Perfil**:
  - Visualización de datos del usuario.
  - Edición de bio.
  - Cambio de contraseña.
  - Avatar (subida y guardado en `uploads/avatars/`).
- **Feed**:
  - Listado de publicaciones con **resumen**.
  - **Filtro por categoría**.
  - Acceso al detalle del post en una **página independiente**.
- **Publicaciones (posts)**:
  - Crear post (título, resumen, contenido, categoría).
  - Gestión de estado para el autor: **publicado / oculto / borrado**.
- **Comentarios (threads)**:
  - Comentar publicaciones.
  - Responder a comentarios (hilos) mediante `id_comentario_padre`.
  - Gestión de estado para el autor (según configuración del proyecto).

## Requisitos

- Servidor web: **Apache** (recomendado con XAMPP/WAMP/LAMP).
- **PHP 8.x**
- **MySQL / MariaDB**
- Acceso a **phpMyAdmin** o cliente `mysql` para importar el script SQL.

## Instalación

1. **Descargar o clonar** el repositorio.
2. Copiar la carpeta del proyecto a la ruta del servidor web:
   - XAMPP (Windows): `C:\xampp\htdocs\QuickForum-main\`
   - Linux (Apache): `/var/www/html/QuickForum-main/`

3. Crear la base de datos importando el script:

   - **phpMyAdmin**: Importar `BBDD/script_creacion.sql`
   - **CLI** (opcional):
     ```bash
     mysql -u root -p < BBDD/script_creacion.sql
     ```

   > Nota: el script incluye `DROP DATABASE IF EXISTS foro;` y vuelve a crear la BBDD `foro`.

4. Configurar la conexión en:
   - `BBDD/config/bbdd.php`
   ```php
   $DB_HOST = 'localhost';
   $DB_USER = 'root';
   $DB_PASS = '';
   $DB_NAME = 'foro';
   $DB_PORT = 3306;
   ```

5. Verificar permisos de escritura (si procede) para subida de avatares:
   - `uploads/avatars/`

## Uso

1. Accede a:
   - `http://localhost/QuickForum-main`

2. Entra al **Crear cuenta** para crear un nuevo usuario.

3. Tras iniciar sesión:
   - Entra al **Feed** para ver publicaciones y filtrar por categoría.
   - Entra a **Crear post** para publicar.
   - Entra a un post para ver el contenido completo y comentar.

## Estructura del proyecto

- `login.php` → formulario de login (envía a `index.php`).
- `registro.php` → formulario de registro (envía a `index.php`).
- `index.php` → controlador: procesa login/registro y redirige.
- `feed.php` → listado de posts + filtro + acciones del autor.
- `post.php` → detalle del post + comentarios + acciones del autor.
- `crear_post.php` → creación de publicaciones.
- `perfil.php` → datos de usuario, bio, contraseña y avatar.
- `logout.php` → cierre de sesión.
- `BBDD/script_creacion.sql` → creación completa de la base de datos y tablas.
- `BBDD/config/bbdd.php` → conexión a MySQL.
- `css/estilos.css` → estilos.
- `uploads/avatars/` → imágenes de avatar.

## Notas de permisos (autoría)

Las acciones de **ocultar/publicar/borrar** se aplican únicamente si el registro pertenece al usuario autenticado (autor).  
En caso de intentar modificar contenido ajeno, el sistema devuelve un mensaje de error.

