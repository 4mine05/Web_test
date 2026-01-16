<?php
session_start();
session_unset(); // Destruye todas las variables de sesión
session_destroy();

session_start();
$_SESSION['logout_ok'] = "Sesión cerrada correctamente.";

header("Location: login.php");
exit;
?>