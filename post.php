<?php
session_start();
require_once __DIR__ . '/BBDD/config/bbdd.php';

// Protección
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

/* Mensajes Error/OK */
$errAcc = $_SESSION['error'] ?? null;
$okAcc  = $_SESSION['ok'] ?? null;
$err = $_SESSION['post_error'] ?? null;
$ok  = $_SESSION['post_ok'] ?? null;
unset($_SESSION['post_error'], $_SESSION['post_ok']);
unset($_SESSION['error'], $_SESSION['ok']);

// Guardar el id del usuario logueado
$idUsuario = $_SESSION['id'];

// Guardar el id de post seleccionado
$idPost = $_GET['id'] ?? '';

/* ACCION SOBRE POST */
$accion = $_GET['accion'] ?? '';

/* ACCIÓN SOBRE COMENTARIOS */
$comentAcc = $_GET['comentario'] ?? '';

/* ============================
  ACCIONES SOBRE POST Y COMENTARIOS
================================ */

if ($accion != '') {
    // Acciones sobre el POST (solo si el usuario es el dueño)

    if ($accion == 'ocultar_post') {
        mysqli_query($conexion, "UPDATE posts SET estado='oculto' WHERE id_post=$idPost AND id_usuario=$idUsuario");
        header("Location: post.php?id=$idPost");
        exit;
        if (mysql_affected_rows($conexion) == 0) {
            $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
        } else {
            $_SESSION["ok"] = "Post ocultado correctamente.";
        }
    }

    if ($accion == 'publicar_post') {
        mysqli_query($conexion, "UPDATE posts SET estado='publicado' WHERE id_post=$idPost AND id_usuario=$idUsuario");
        header("Location: post.php?id=$idPost");
        exit;
        if (mysql_affected_rows($conexion) == 0) {
            $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
        } else {
            $_SESSION["ok"] = "Post publicado correctamente.";
        }
    }

    if ($accion == 'borrar_post') {
        mysqli_query($conexion, "UPDATE posts SET estado='borrado' WHERE id_post=$idPost AND id_usuario=$idUsuario");
        header("Location: feed.php");
        exit;
        if (mysql_affected_rows($conexion) == 0) {
            $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
        } else {
            $_SESSION["ok"] = "Post borrado correctamente.";
        }
    }
    // Redirigir si el post no existe
    if ($idPost === '') {
        header(header: "Location: feed.php");
        exit;
    }

    // Acciones sobre COMENTARIOS (solo dueño del comentario)
    if ($comentAcc != '') {

    if ($accion == 'ocultar_com') {
      mysqli_query($conexion, "UPDATE comentarios SET estado='oculto' WHERE id_comentario=$comentAcc AND id_usuario=$idUsuario");
      if (mysqli_affected_rows($conexion) == 0) {
        $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
      } else {
          $_SESSION["ok"] = "Comentario ocultado correctamente.";
      }
    }

    if ($accion == 'mostrar_com') {
      mysqli_query($conexion, "UPDATE comentarios SET estado='visible' WHERE id_comentario=$comentAcc AND id_usuario=$idUsuario");
      if (mysqli_affected_rows($conexion) == 0) {
        $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
      } else {
          $_SESSION["ok"] = "Comentario mostrado correctamente.";
      }
    }

    if ($accion == 'borrar_com') {
      mysqli_query($conexion, "UPDATE comentarios SET estado='borrado' WHERE id_comentario=$comentAcc AND id_usuario=$idUsuario");
      if (mysqli_affected_rows($conexion) == 0) {
        $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
      } else {
          $_SESSION["ok"] = "Comentario borrado correctamente.";
      }
    }

    header("Location: post.php?id=$idPost");
    exit;
  }
}

/* =========================
   INSERTAR COMENTARIO
========================= */
// Verificar si el usuario pulso el boton "Publicar comentario"
if (isset($_POST['comentar'])) {

  $contenido = $_POST['contenido'] ?? '';
  $replyTo = $_POST['reply_to'] ?? '';

  if ($contenido === '') {
    $_SESSION['post_error'] = "El comentario no puede estar vacío.";
    header("Location: post.php?id=$idPost");
    exit;
  }

  if ($replyTo === '' || $replyTo == 0) { // Si el Usuario no esta respondiendo a ningun comentario
    $sql = "INSERT INTO comentarios (id_post, id_usuario, contenido)
                             VALUES ($idPost, $idUsuario, '$contenido')";
  } else {
    $sql = "INSERT INTO comentarios (id_post, id_usuario, id_comentario_padre, contenido)
                              VALUES ($idPost, $idUsuario, $replyTo, '$contenido')";
  }

  if (mysqli_query($conexion, $sql)) {
    $_SESSION['post_ok'] = "Comentario publicado."; // True
  } else {
    $_SESSION['post_error'] = "Error al publicar el comentario."; // False
  }
  
  // Volver al post
  header("Location: post.php?id=$idPost");
  exit;
}

/* =========================
   SUMAR VISITAS AL POST
========================= */
mysqli_query($conexion, "UPDATE posts SET num_visitas = num_visitas + 1 WHERE id_post = $idPost");

/* =========================
   CARGAR POST SELECCIONADO
========================= */
$sql = "SELECT p.id_post, p.id_usuario, p.titulo, p.contenido, p.categoria, p.estado, p.fecha_creacion, u.username
        FROM posts p
        JOIN usuarios u ON u.id_usuario = p.id_usuario
        WHERE p.id_post = $idPost
        AND (p.estado = 'publicado' OR p.id_usuario = $idUsuario) AND p.estado <> 'borrado' ";

$resPost = mysqli_query($conexion, $sql);
$post = ($resPost) ? mysqli_fetch_assoc($resPost) : null;

// Redirigir si el post no existe
if (!$post) {
  header("Location: feed.php");
  exit;
}

/* =========================
   CARGAR COMENTARIOS
========================= */
$sql = "SELECT c.id_comentario, c.id_usuario, c.contenido, c.fecha_creacion, c.id_comentario_padre, c.estado, u.username
        FROM comentarios c
        JOIN usuarios u ON u.id_usuario = c.id_usuario
        WHERE c.id_post = $idPost
        AND (c.estado = 'visible' OR c.id_usuario = $idUsuario) AND c.estado <> 'borrado' ";

$resCom = mysqli_query($conexion, $sql);

/* Responder a comentario */
$replyToGet = $_GET['reply_to'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $post['titulo'] ?></title>
  <link rel="stylesheet" href="css/estilos.css">
  <header><img src="css/img.png"></header>
</head>
<body>

  <h1><?= $post['titulo'] ?></h1>

  <p class="nav">
    <a href="perfil.php">Perfil</a> |
    <a href="feed.php">Volver al feed</a> |
    <a href="logout.php">Cerrar sesión</a>
  </p>
<!-- Imprimir mensajes error/OK -->
  <?php if ($err): ?>
    <p class="error"><?= $err ?></p>
  <?php endif; ?>

  <?php if ($ok): ?>
    <p class="ok"><?= $ok ?></p>
  <?php endif; ?>

  <?php if ($errAcc): ?>
    <p class="error"><?= $errAcc ?></p>
  <?php endif; ?>

  <?php if ($okAcc): ?>
    <p class="ok"><?= $okAcc ?></p>
  <?php endif; ?>

  <p>
    <strong>Autor:</strong> <?= $post['username'] ?> |
    <strong>Fecha:</strong> <?= $post['fecha_creacion'] ?> |
    <strong>Categoría:</strong> <?= $post['categoria'] ?>
  </p>

  <hr>

  <p><?= nl2br($post['contenido']) ?></p> <!-- nl2br convierte /n en <br> -->

  <!-- Si el post es del usuario logueado, Se muestran las acciones -->
  <?php if ($post['id_usuario'] == $idUsuario): ?>
  <p>
    <strong>Estado:</strong> <?= $post['estado'] ?> |
    <?php if ($post['estado'] == 'publicado'): ?>
      <a href="post.php?id=<?= $idPost ?>&accion=ocultar_post">Ocultar post</a>
    <?php else: ?>
      <a href="post.php?id=<?= $idPost ?>&accion=publicar_post">Publicar post</a>
    <?php endif; ?>
    | <a href="post.php?id=<?= $idPost ?>&accion=borrar_post">Borrar post</a>
  </p>
<?php endif; ?>

  <hr>

  <h2>Comentarios</h2>

  <?php if ($resCom && mysqli_num_rows($resCom) > 0): ?> <!-- Si hay comentarios -->
    <?php while ($c = mysqli_fetch_assoc($resCom)): ?> <!-- Mientras haya comentarios -->
      <p>
        <strong><?= $c['username'] ?></strong> (<?= $c['fecha_creacion'] ?>) #<?= $c['id_comentario'] ?>
        <?php if ($c['id_comentario_padre']): ?>   <!-- Si el comentario es una respuesta -->
          <i>· Respuesta a #<?= $c['id_comentario_padre'] ?></i>
        <?php endif; ?>
      </p>

      <p><?= nl2br($c['contenido']) ?></p>

      <!-- Si el comentario es del usuario logueado, Se muestran las acciones -->
      <?php if ($c['id_usuario'] == $idUsuario): ?>
        <p>
            <strong>Estado:</strong> <?= $c['estado'] ?> |
            <?php if ($c['estado'] == 'visible'): ?>
            <a href="post.php?id=<?= $idPost ?>&accion=ocultar_com&comentario=<?= $c['id_comentario'] ?>">Ocultar</a>
            <?php else: ?>
            <a href="post.php?id=<?= $idPost ?>&accion=mostrar_com&comentario=<?= $c['id_comentario'] ?>">Mostrar</a>
            <?php endif; ?>
            | <a href="post.php?id=<?= $idPost ?>&accion=borrar_com&comentario=<?= $c['id_comentario'] ?>">Borrar</a>
        </p>
      <?php endif; ?>

      <p>
        <a href="post.php?id=<?= $idPost ?>&reply_to=<?= $c['id_comentario'] ?>">Responder</a>
      </p>

      <hr>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No hay comentarios todavía.</p>
    <hr>
  <?php endif; ?>

  <h3>Escribir comentario</h3>

  <?php if ($replyToGet && $replyToGet != 0): ?><!
    <p>Vas a responder al comentario #<?= $replyToGet ?> · 
    <a href="post.php?id=<?= $idPost ?>">Cancelar</a></p>      <!-- Cancelar la operacion volviendo al post -->
  <?php endif; ?>

  <form method="POST" action="post.php?id=<?= $idPost ?>">
    <textarea name="contenido" rows="3" required></textarea>
    <br>
    <input type="hidden" name="reply_to" value="<?= $replyToGet ?>"> <!-- Id del comentario a responder -->
    <input type="submit" name="comentar" value="Publicar comentario">
  </form>
</body>
</html> 