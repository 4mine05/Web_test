<?php
// registro.php
session_start();

// Mostrar error recibido desde index.php
$err = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link rel="stylesheet" href="css/estilos.css">
  <header><img src="css/img.png"></header>
</head>
<body>

  <h1>Crear cuenta</h1>

  <!-- Mensage de error-->
  <?php if ($err): ?>
    <p class="error"><?= $err ?></p>
  <?php endif; ?>

  <!-- Formulario de registro-->
  <form action="index.php" method="post" enctype="multipart/form-data" >
    <label for="usuario">Usuario</label>
    <input type="text" id="usuario" name="usuario" value="" required minlength="3" maxlength="15" placeholder="Ej: Username_01Xyz" pattern="^[a-zA-Z0-9_]+$" > <!-- Evitar espacios y carácteres especiales -->
    <br>
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" value="" required minlength="3" maxlength="15" >
    <br>
    <label for="apellido">Apellidos</label>
    <input type="text" id="apellido" name="apellidos" value="" required minlength="3" maxlength="15">
    <br>
    <label for="correo">Correo electrónico</label>
    <input type="email" id="correo" name="correo" value="" required maxlength="120" placeholder="correo@ejemplo.ej">
    <br>
    <label for="fecha">Fecha de nacimiento</label>
    <input type="date" id="fecha" name="fecha" value="" required >
    <br>
    <label for="contraseña">Contraseña</label>
    <input type="password" id="contraseña" name="contraseña" required minlength="8" maxlength="72"> 
    <br>
    <label for="contraseña2">Confirmar contraseña</label>
    <input type="password" id="contraseña2" name="contraseña2" required minlength="8" maxlength="72">
    <br>
    <label for="avatar">Foto de perfil</label>
    <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/webp">
    <br>
    <input type="submit" name="registro" value="Crear cuenta">

    <p><a href="index.php">Volver a login</a></p>
  </form>

</body>
</html>
