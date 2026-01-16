<?php
session_start();
require_once __DIR__ . '/BBDD/config/bbdd.php';
if (!$conexion) {
  die("Error de conexión");
}
/* =========================
   PROTECCIÓN: requiere login
========================= */
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}
// Guardar el id del usuario logueado
$idUsuario = $_SESSION['id'];

/* =========================
   PROCESAR ACTUALIZAR BIO
========================= */
if (isset($_POST['actualizar_bio'])) {
  $bio = trim(string: $_POST['bio'] ?? ''); // La función "trim" elimina espacios en blanco

  if ($bio === '') {
    $_SESSION['perfil_error'] = "La bio no puede estar vacía.";
    header("Location: perfil.php"); exit;
  }

  if (mb_strlen($bio) > 500) {
    $_SESSION['perfil_error'] = "La bio no puede superar 500 caracteres.";
    header("Location: perfil.php"); exit;
  }

  $sql = "UPDATE usuarios SET bio = '$bio' WHERE id_usuario = $idUsuario";
  mysqli_query($conexion, $sql);

  $_SESSION['perfil_ok'] = "Bio actualizada correctamente.";
 header("Location: perfil.php"); exit;
}

/* =========================
   PROCESAR CAMBIO CONTRASEÑA
========================= */
if (isset($_POST['cambiar_password'])) {
  $actual = $_POST['password_actual'] ?? '';
  $nueva  = $_POST['password_nueva'] ?? '';
  $nueva2 = $_POST['password_nueva2'] ?? '';

  if ($actual === '' || $nueva === '' || $nueva2 === '') {
    $_SESSION['perfil_error'] = "Rellena todos los campos de contraseña.";
    header("Location: perfil.php"); exit;
  }

  if ($nueva !== $nueva2) {
    $_SESSION['perfil_error'] = "La nueva contraseña no coincide con la confirmación.";
    header("Location: perfil.php"); exit;
  }

  if (strlen($nueva) < 8 || mb_strlen($nueva) > 72) {
    $_SESSION['perfil_error'] = "La nueva contraseña debe tener entre 8 y 72 caracteres.";
    header("Location: perfil.php"); exit;
  }

  // 1) Leer hash actual
  $sql = "SELECT password FROM usuarios WHERE id_usuario = $idUsuario";
  $res = mysqli_query($conexion, $sql);
  $row = mysqli_fetch_assoc($res);


  if (!$row) {
    $_SESSION['perfil_error'] = "Usuario no encontrado.";
    header("Location: login.php"); exit;
  }

  // 2) Verificar contraseña actual
  if (!password_verify($actual, $row['password'])) {
    $_SESSION['perfil_error'] = "La contraseña actual no es correcta.";
    header("Location: perfil.php"); exit;
  }

  // Evitar reutilizar la misma contraseña
  if (password_verify($nueva, $row['password'])) {
    $_SESSION['perfil_error'] = "La nueva contraseña no puede ser igual a la actual.";
    header("Location: perfil.php"); exit;
  }

  // 3) Guardar nuevo hash
  $nuevohash = password_hash($nueva, PASSWORD_BCRYPT);

  $sql = "UPDATE usuarios SET password = '$nuevohash' WHERE id_usuario = $idUsuario";
  mysqli_query($conexion, $sql);

  $_SESSION['perfil_ok'] = "Contraseña actualizada correctamente.";
  header("Location: perfil.php"); exit;
}

/* =========================
   CARGAR DATOS DE USUARIO
========================= */
$sql = "SELECT id_usuario, username, email, nombre, apellidos, fecha_nacimiento, fecha_registro, ultimo_login, avatar_url, bio FROM usuarios WHERE id_usuario = $idUsuario";
$resultado = mysqli_query($conexion, $sql);
$u = mysqli_fetch_assoc($resultado);

if (!$u) {
  // Sesión inválida o usuario borrado
  session_destroy();
  header("Location: login.php");
  exit;
}

/* =========================
   MENSAJES FLASH
========================= */
$err = $_SESSION['perfil_error'] ?? null;
$ok  = $_SESSION['perfil_ok'] ?? null;
unset($_SESSION['perfil_error'], $_SESSION['perfil_ok']);

// Avatar fallback
$avatar = $u['avatar_url'] ?: "uploads/avatars/default.png";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil</title>
  <link rel="stylesheet" href="css/estilos.css">
  <header><img src="css/img.png"></header>
</head>
<body>

  <h1>Mi perfil</h1>

  <p class="nav">
    <a href="feed.php">Feed</a> |
    <a href="perfil.php">Recargar</a> |
    <a href="logout.php">Cerrar sesión</a>
  </p>

  <?php if ($err): ?>
    <p class="error"><?= $err ?></p>
  <?php endif; ?>

  <?php if ($ok): ?>
    <p class="ok"><?= $ok ?></p>
  <?php endif; ?>

  <section>
    <div>
      <img class="avatar" src="<?=$avatar ?>" alt="Avatar">
    </div>

    <div>
      <p><strong>Usuario:</strong> <?= $u['username'] ?></p>
      <p><strong>Email:</strong> <?= $u['email'] ?></p>
      <p><strong>Nombre completo:</strong> <?= $u['nombre'] ?> <?= $u['apellidos'] ?></p>
      <p><strong>Fecha de nacimiento:</strong> <?= $u['fecha_nacimiento'] ?></p>
      <p><strong>Registro:</strong> <?= $u['fecha_registro'] ?></p>
      <p><strong>Último login:</strong> <?= $u['ultimo_login'] ?></p>
    </div>
  </section>

  <hr>

  <h2>Bio</h2>
  <form method="post" action="perfil.php">
    <textarea name="bio" rows="5" maxlength="500" required style="width:100%;"><?=$u['bio'] ?></textarea>
    <br>
    <input type="submit" name="actualizar_bio" value="Guardar bio">
  </form>

  <hr>

  <h2>Cambiar contraseña</h2>
  <form method="post" action="perfil.php">
    <label for="password_actual">Contraseña actual</label><br>
    <input type="password" id="password_actual" name="password_actual" required minlength="8" maxlength="72">
    <br>

    <label for="password_nueva">Nueva contraseña</label><br>
    <input type="password" id="password_nueva" name="password_nueva" required minlength="8" maxlength="72">
    <br>

    <label for="password_nueva2">Confirmar nueva contraseña</label><br>
    <input type="password" id="password_nueva2" name="password_nueva2" required minlength="8" maxlength="72">
    <br>

    <input type="submit" name="cambiar_password" value="Actualizar contraseña">
  </form>



</body>
</html>
