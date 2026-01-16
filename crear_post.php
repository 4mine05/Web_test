<?php
session_start();
require_once __DIR__ . '/BBDD/config/bbdd.php';

/* Protección */
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}
// Guardar el id del usuario logueado
$idUsuario = $_SESSION['id'];

/* Mensajes Error/OK */
$err = $_SESSION['crear_post_error'] ?? null;
$ok  = $_SESSION['crear_post_ok'] ?? null;
unset($_SESSION['crear_post_error'], $_SESSION['crear_post_ok']);

/* =========================
   CREAR POST
========================= */
if (isset($_POST['crear_post'])) {

  $titulo    = $_POST['titulo'] ?? '';
  $resumen   = $_POST['resumen'] ?? '';
  $contenido = $_POST['contenido'] ?? '';
  $categoria = $_POST['categoria'] ?? '';

  if ($titulo === '' || $resumen === '' || $contenido === '') {
    $_SESSION['crear_post_error'] = "Rellena título, resumen y contenido.";
    header("Location: crear_post.php");
    exit;
  }

  $estado = 'publicado';

  $sql = "INSERT INTO posts (id_usuario, titulo, resumen, contenido, categoria, estado)
          VALUES ($idUsuario, '$titulo', '$resumen', '$contenido', '$categoria', '$estado')";

  if (mysqli_query($conexion, $sql)) {
    $idNuevo = mysqli_insert_id($conexion); // Devuelve el id del post creado

    $_SESSION['post_ok'] = "Post publicado.";
    header("Location: post.php?id=$idNuevo");
    exit;
  } else {
    $_SESSION['crear_post_error'] = "Error al crear el post.";
    header("Location: crear_post.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear post</title>
  <link rel="stylesheet" href="css/estilos.css">
  <header><img src="css/img.png"></header>
</head>
<body>

  <h1>Crear post</h1>

  <p class="nav">
    <a href="perfil.php">Perfil</a> |
    <a href="feed.php">Volver al feed</a> |
    <a href="logout.php">Cerrar sesión</a>
  </p>

  <!-- Mensajes Error/OK -->
  <?php if ($err): ?>
    <p class="error"><?= $err ?></p>
  <?php endif; ?>

  <?php if ($ok): ?>
    <p class="ok"><?= $ok ?></p>
  <?php endif; ?>

  <form action="crear_post.php" method="post">
    <label for="titulo">Título</label><br>
    <input type="text" id="titulo" name="titulo" required maxlength="150">
    <br><br>

    <label for="categoria">Categoría</label><br>
    <input type="text" id="categoria" name="categoria" maxlength="50" placeholder="Ej: Noticias, Deportes...">
    <br><br>

    <label for="resumen">Resumen</label><br>
    <textarea id="resumen" name="resumen" rows="3" required maxlength="300"></textarea>
    <br><br>

    <label for="contenido">Contenido</label><br>
    <textarea id="contenido" name="contenido" rows="8" required></textarea>
    <br><br>

    <input type="submit" name="crear_post" value="Publicar">
  </form>

</body>
</html>
