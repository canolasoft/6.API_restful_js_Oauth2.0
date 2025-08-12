<?php
/* Clase usuario para gestionar con API RESTful
 * Permite operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * Requiere conexión a una base de datos MySQL
 */

// Configuracion del reporte de errores
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

class Test
{
	private $conn;
	private $salt = "sabor";

	// Constructor que recibe la conexión a la base de datos
	public function __construct($conn)
	{
		$this->conn = $conn;
	}

	// Métodos de prueba
	// Verifica si la clave es correcta
	public function testKey($key)
	{
		// verifico si el token existe y esta vencido
		$query = "SELECT * FROM access_token WHERE token = '$key' AND fecha_vencimiento > NOW()";
		$result = mysqli_query($this->conn, $query);
		if(mysqli_num_rows($result) > 0){
			// Si el token es valido, retorna true
			return true;
		} else {
			// Si el token esta vencido, retorna false
			return false;
		}
	}
}