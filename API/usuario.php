<?php
/* Clase usuario para gestionar con API RESTful
 * Permite operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * Requiere conexión a una base de datos MySQL
 */

// Configuracion del reporte de errores
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

class Usuario
{
	private $conn;

	// Constructor que recibe la conexión a la base de datos
	public function __construct($conn)
	{
		$this->conn = $conn;
	}

	// Métodos para manejar usuarios
	// Obtener todos los usuarios
	public function getAllUsuarios()
	{
		$query = "SELECT * FROM usuario";
		$result = mysqli_query($this->conn, $query);
		$usuarios = [];
		while($row = mysqli_fetch_assoc($result)) {
			$usuarios[] = $row;
		}
		return $usuarios;
	}
	// Obtener un usuario por ID
	public function getUsuarioById($id)
	{
		$query = "SELECT * FROM usuario WHERE id = $id ";
		$result = mysqli_query($this->conn, $query);
		$usuario = mysqli_fetch_assoc($result);
		return $usuario;
	}
	// Agregar un nuevo usuario
	public function addUsuario($data){
		if(!isset($data['usr_name']) || !isset($data['imagen']) || !isset($data['usr_email']) || !isset($data['usr_pass'])) {
			http_response_code(400);
			return json_encode(["error" => "Datos incompletos"]);
		}else{
			$usr_name = $data['usr_name'];
			$usr_email = $data['usr_email'];
			$usr_pass = password_hash($data['usr_pass'], PASSWORD_DEFAULT);
			$img_data = $data['imagen'];
			// Procesar imagen base64
			if (preg_match('/^data:image\/(\w+);base64,/', $img_data, $type)) {
				$img_data = substr($img_data, strpos($img_data, ',') + 1);
				$img_data = base64_decode($img_data);
				$ext = strtolower($type[1]);
				$img_name = uniqid() . "." . $ext;
				$img_path = __DIR__ . "/uploads/" . $img_name;
				if (!is_dir(__DIR__ . "/uploads/")) {
					mkdir(__DIR__ . "/uploads/", 0777, true);
				}
				try{
					$query = "INSERT INTO usuario (usr_name, usr_email, usr_pass, imagen) VALUES ('$usr_name', '$usr_email', '$usr_pass', '$img_name')";
					$result = mysqli_query($this->conn, $query);
				} catch (mysqli_sql_exception $e) {
					http_response_code(500);
					return json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
				}
				if($result){
					if (file_put_contents($img_path, $img_data) === false) {
						http_response_code(500);
						return json_encode(["error" => "No se pudo guardar la imagen"]);
					}
					http_response_code(201);
					return json_encode(["success" => "Usuario registrado con éxito"]);
				} else {
					http_response_code(400);
					return json_encode(["error" => "No se pudo registrar el usuario"]);
				}
			} else {
				http_response_code(400);
				return json_encode(["error" => "Formato de imagen inválido"]);
			}
		}
	}

	// Iniciar sesión de usuario
	public function loginUsuario($data){
		if (!isset($data['usr_email']) || !isset($data['usr_pass'])) {
			http_response_code(400);
			return json_encode(["error" => "Datos incompletos"]);
		} else {
			$usr_email = $data['usr_email'];
			$usr_pass = $data['usr_pass'];
			$query = "SELECT * FROM usuario WHERE usr_email = '$usr_email'";
			$result = mysqli_query($this->conn, $query);
			if (mysqli_num_rows($result) > 0) {
				$usuario = mysqli_fetch_assoc($result);
				if (password_verify($usr_pass, $usuario['usr_pass'])) {
					//return $usuario; // Retorna el usuario si las credenciales son correctas
					$usr_key = md5($usuario['usr_email'] . time()); // Genera una clave de usuario
					// registro la key en la base de datos
					// verifico si ya existe un token para este usuario
					$query = "SELECT * FROM access_token WHERE id_usuario = " . $usuario['id'];
					$result = mysqli_query($this->conn, $query);
					if (mysqli_num_rows($result) > 0) {
						// Si existe, lo actualizo
						$query = "UPDATE access_token SET token = '$usr_key', fecha_creado = NOW(), fecha_vencimiento = DATE_ADD(NOW(), INTERVAL 12 HOUR) WHERE id_usuario = " . $usuario['id'];
					} else {
						// Si no existe, lo inserto
						$query = "INSERT INTO access_token(id_usuario, token) VALUES(" . $usuario['id'] . ", '$usr_key')";
					}
					$result = mysqli_query($this->conn, $query);
					if ($result) {
						http_response_code(200);
						return json_encode(["usuario" => [$usuario['usr_name'], $usr_email, $usr_key]]);
					} else {
						http_response_code(500);
						return json_encode(["error" => "Error al generar el token"]);
					}
				} else {
					http_response_code(400);
					return json_encode(["error" => "Contraseña incorrecta"]);
				}
			} else {
				http_response_code(400);
				return json_encode(["error" => "Usuario no encontrado"]);
			}
		}
	}

	public function logoutUsuario($data){
		if(!isset($data['usr_key'])){
			http_response_code(400);
			return json_encode(["error" => "Datos incompletos"]);
		}else{
			$key = $data['usr_key'];
			// liminar el token de la base de datos
			$query = "DELETE FROM access_token WHERE token = '$key'";
			$result = mysqli_query($this->conn, $query);
			if($result){
				http_response_code(200);
				return json_encode(["success" => "Sesión cerrada correctamente"]);
			} else {
				http_response_code(500);
				return json_encode(["error" => "Error al cerrar sesión"]);
			}
		}
	}

	// Actualizar un usuario por ID
	public function updateUsuario($id, $data)
	{
		$usr_name = $data['usr_name'];
		$usr_email = $data['usr_email'];
		$usr_pass = $data['usr_pass'];
		$query = "UPDATE usuario SET usr_name = '$usr_name', usr_email = '$usr_email', usr_pass = '$usr_pass' WHERE id = ".$id;
		$result = mysqli_query($this->conn, $query);
		if($result){
			return true;
		} else {
			return false;
		}
	}
	// Eliminar un usuario por ID
	public function deleteUsuario($id)
	{
		$query = "DELETE FROM usuario WHERE id = ".$id;
		$result = mysqli_query($this->conn, $query);
		if($result){
			return true;
		} else {
			return false;
		}
	}
}