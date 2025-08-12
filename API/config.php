<?php
// Configuración de la base de datos
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'base_usuarios';
// Establecer conexión a la base de datos
$conn = mysqli_connect($hostname, $username, $password, $database);
// Verificar la conexión
if(!$conn){
	die('Connection failed: ' . mysqli_connect_error());
}
?>