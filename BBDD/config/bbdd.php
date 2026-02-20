<?php

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'foro_pass';
$DB_NAME = getenv('DB_NAME') ?: 'foro';
$DB_PORT = 3306;

$conexion = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
?>
