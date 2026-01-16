<?php
session_start();
require_once __DIR__ . '/BBDD/config/bbdd.php';
if (!$conexion) {
  die("Error de conexión");
}

/* =========================
   REGISTRO
========================= */
if (isset($_POST['registro'])) {

  $usuario  = $_POST['usuario'] ?? '';
  $correo   = $_POST['correo'] ?? '';
  $fecha    = $_POST['fecha'] ?? '';
  $pass1    = $_POST['contraseña'] ?? '';
  $pass2    = $_POST['contraseña2'] ?? '';
  $nombre   = $_POST['nombre']??null; 
  $apellidos= $_POST['apellidos']??null;
  $bio      = $_POST['bio']??null;

  // Validación de campos
  if ($usuario === '' || $correo === '' || $fecha === '' || $pass1 === '' || $pass2 === '' || $nombre === '' || $apellidos === '' || $bio === '') {
    $_SESSION['register_error'] = "Rellena todos los campos.";
    header("Location: registro.php"); exit;
  }
 // Confirmación de contraseñas 
  if ($pass1 !== $pass2) {
    $_SESSION['register_error'] = "Las contraseñas no coinciden.";
    header("Location: registro.php"); exit;
  }

  // Encriptación de la contraseña
  $hash = password_hash($pass1, PASSWORD_BCRYPT);

  // Avatar por defecto
  $avatar = "uploads/avatars/default.png";

  // Avatar subido por el usuario

  if (isset($_FILES['avatar']) and $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {  

      // Extraer la extensión de la imagen subida (jpg, png, gif)
      $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION); 
      // Generar un nombre de archivo unico
      $nombreArchivo = uniqid("av_", true) . "." . $ext;
      // Ruta de la imagen en el servidor
      $ruta = "uploads/avatars/" . $nombreArchivo;

      move_uploaded_file(from: $_FILES['avatar']['tmp_name'], to: $ruta);

      $avatar = $ruta;
  }

  // Insertar en la BBDD
  $sql = "INSERT INTO usuarios(username, email, password, nombre, apellidos, fecha_nacimiento, avatar_url, bio)
          VALUES ('$usuario', '$correo', '$hash', '$nombre', '$apellidos', '$fecha', '$avatar', '$bio');";

  if (mysqli_query($conexion, $sql)) {
    $_SESSION['login_ok'] = "Cuenta creada. Ya puedes iniciar sesión.";
    header("Location: login.php"); exit;
  } else {
    $_SESSION['register_error'] = "Error al registrar (posible usuario/email duplicado).";
    header("Location: registro.php"); exit;
  } 
}

/* =========================
   LOGIN
========================= */
if (isset($_POST['inicio'])) {

  $usuario = $_POST['usuario'] ?? '';
  $pass    = $_POST['contraseña'] ?? '';

  if ($usuario === '' || $pass === '') {
    $_SESSION['login_error'] = "Usuario y contraseña obligatorios.";
    header("Location: login.php"); exit;
  }

  // Validación de usuario o email
  $sql = "SELECT id_usuario, email, password FROM usuarios WHERE username = '$usuario' OR email = '$usuario';";
  $resultado = mysqli_query($conexion, $sql);

  if ($resultado && mysqli_num_rows($resultado) > 0) {
    $row = mysqli_fetch_assoc($resultado);

    if (password_verify($pass, $row['password'])) {
      $_SESSION['id'] = $row['id_usuario']; 
      // Actualizar fecha del ultimo login
      $sql = 'UPDATE usuarios SET ultimo_login = NOW() WHERE id_usuario = '.$_SESSION['id'].';';
      mysqli_query($conexion, $sql);

      header("Location: feed.php"); exit;
    } else {
      $_SESSION['login_error'] = "Contraseña incorrecta.";
      header("Location: login.php"); exit;
    }
  } else {
    $_SESSION['login_error'] = "Usuario incorrecto.";
    header("Location: login.php"); exit;
  }
}

// Si el usuario entra directamente a index.php, manda a login
header("Location: login.php");
exit;
