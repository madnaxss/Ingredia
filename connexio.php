<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servidor = 'localhost';
$usuari = 'root';
$clau = '';
$bbdd = 'ProyectoFinal';
$connexio = mysqli_connect($servidor, $usuari, $clau, $bbdd);

if (!$connexio) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>

