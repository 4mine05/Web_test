<?php
session_start();
require_once __DIR__ . '/BBDD/config/bbdd.php';

if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

/* Mensajes Error/OK */
$err = $_SESSION['error'] ?? null;
$ok  = $_SESSION['ok'] ?? null;
unset($_SESSION['error'], $_SESSION['ok']);

// Guardar el id del usuario logueado
$idUsuario = $_SESSION["id"];

$categoria = $_GET['categoria'] ?? '';


$accion = $_GET['accion'] ?? '';
$postAcc = $_GET['post'] ?? '';

// Si se ha pulsado alguna accion sobre un post
if ($accion != '' && $postAcc != '') {

  if ($accion == 'ocultar') {
    mysqli_query($conexion, "UPDATE posts SET estado='oculto' WHERE id_post=$postAcc AND id_usuario=$idUsuario");
    if (mysqli_affected_rows($conexion) == 0) {
      $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
    } else {
        $_SESSION["ok"] = "Post ocultado correctamente.";
    }
  }
  if ($accion == 'publicar') {
    mysqli_query($conexion, "UPDATE posts SET estado='publicado' WHERE id_post=$postAcc AND id_usuario=$idUsuario");
    if (mysqli_affected_rows($conexion) == 0) {
      $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
    } else {
        $_SESSION["ok"] = "Post publicado correctamente.";
    }
  }

  if ($accion == 'borrar') {
    mysqli_query($conexion, "UPDATE posts SET estado='borrado' WHERE id_post=$postAcc AND id_usuario=$idUsuario");
    if (mysqli_affected_rows($conexion) == 0) {
      $_SESSION["error"] = "No tienes permisos suficientes para realizar esta acción.";
    } else {
        $_SESSION["ok"] = "Post borrado correctamente.";
    }
  }

  header("Location: feed.php");
  exit;
}

/* Cargar categorías para el filtro */
$categorias = [];
$sqlCat = "SELECT DISTINCT categoria
           FROM posts
           WHERE estado = 'publicado' AND categoria IS NOT NULL AND categoria <> ''
           ORDER BY categoria ASC";
$resCat = mysqli_query($conexion, $sqlCat);
if ($resCat) {
  while ($row = mysqli_fetch_assoc($resCat)) {
    $categorias[] = $row['categoria'];
  }
}

/* Consulta de posts */
$sql = "SELECT p.id_post, p.id_usuario, p.titulo, p.resumen, p.categoria, p.estado, p.fecha_creacion, u.username, p.num_visitas
        FROM posts p
        JOIN usuarios u ON u.id_usuario = p.id_usuario
        WHERE p.estado <> 'borrado' AND (p.estado = 'publicado' OR p.id_usuario = $idUsuario) ";

// Si se ha seleccionado una categoría
if ($categoria !== '') {
  $sql .= " AND p.categoria = '$categoria'";
}

$sql .= " ORDER BY p.num_visitas DESC";

$result = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Feed</title>
  <link rel="stylesheet" href="css/estilos.css">
  <header><img src="css/img.png"></header>
</head>
<body>

  <h1>Feed</h1>

  <p class="nav">
    <a href="perfil.php">Perfil</a> |
    <a href="feed.php">Recargar</a> |
    <a href="crear_post.php">Nuevo post</a> |
    <a href="logout.php">Cerrar sesión</a>
  </p>

  <!-- Mensajes Error/OK -->
  <?php if ($err): ?>
    <p class="error"><?= $err ?></p>
  <?php endif; ?>

  <?php if ($ok): ?>
    <p class="ok"><?= $ok ?></p>
  <?php endif; ?>
  
  <!-- Filtro de categoría -->
  <form method="GET" action="feed.php">
    <label for="categoria">Filtrar por categoría:</label>
    <select name="categoria" id="categoria">
      <option value="">Todas</option>
      <?php foreach ($categorias as $cat): ?>
        <option value="<?= $cat ?>" <?= ($cat === $categoria) ? 'selected' : '' ?>>
          <?= $cat ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Filtrar</button>
  </form>

  <hr>

  <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <?php while ($p = mysqli_fetch_assoc($result)): ?>
      <h2><?= $p['titulo'] ?></h2>
      <p>
        <strong>Autor:</strong> <?= $p['username'] ?> |
        <strong>Fecha:</strong> <?= $p['fecha_creacion'] ?> |
        <strong>Categoría:</strong> <?= $p['categoria'] ?>
      </p>

      <p><?= $p['resumen'] ?></p>
        <?php if ($p['id_usuario'] == $idUsuario): ?>
        <p>
            <strong>Estado:</strong> <?= $p['estado'] ?><br>
            <?php if ($p['estado'] == 'publicado'): ?>
            <a href="feed.php?accion=ocultar&post=<?= $p['id_post'] ?>">Ocultar</a>
            <?php else: ?>
            <a href="feed.php?accion=publicar&post=<?= $p['id_post'] ?>">Publicar</a>
            <?php endif; ?>
            | 
            <a href="feed.php?accion=borrar&post=<?= $p['id_post'] ?>">Borrar</a>
        </p>
        <?php endif; ?>

        <p>
            <strong>Visitas:</strong> <?= $p['num_visitas'] ?>
        </p>

      <p>
        <a href="post.php?id=<?= $p['id_post'] ?>">Ver completo</a>
      </p>

      <hr>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No hay publicaciones.</p>
  <?php endif; ?>

</body>
</html>
