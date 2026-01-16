<?php
session_start();

// Guardar el error recibido desde index.php
$err = $_SESSION['login_error'] ?? null;
// Limpiar el error
unset($_SESSION['login_error']);

// Guardar el mensaje recibido desde index.php
$msg = $_SESSION['login_ok'] ?? null;
unset( $_SESSION['login_ok'] );

// Guardar el logout recibido desde logout.php
$logout = $_SESSION['logout_ok'] ?? null;
unset( $_SESSION['logout_ok'] );
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/estilos.css">
  <header><img src="css/img.png"></header>
</head>
<body>

  <h1>Iniciar sesión</h1>
  <!-- Mostrar el mensage de error si existe-->
  <?php if ($err): ?>
    <p class="error"><?= $err ?></p>
  <?php endif; ?>

  <!-- Mostrar el mensage de ok si existe-->
  <?php if ($msg): ?>
    <p class="ok"><?= $msg ?></p>
  <?php endif; ?>

  <!-- Mostrar el mensage de logout si existe-->
  <?php if ($logout): ?>
    <p class="ok"><?= $logout ?></p>
  <?php endif; ?>

  <!-- Formulario de login-->
  <form action="index.php" method="post">
    <label for="usuario">Usuario</label>
    <input type="text" id="usuario" name="usuario" required minlength="3" maxlength="120" placeholder="Email o Usuario">
    <br>
    <label for="contraseña">Contraseña</label>
    <input type="password" id="contraseña" name="contraseña" required minlength="8" maxlength="72">

    <input type="submit" name="inicio" value="Entrar">

    <p><a href="registro.php">Crear cuenta</a></p>
  </form>

</body>
</html>
