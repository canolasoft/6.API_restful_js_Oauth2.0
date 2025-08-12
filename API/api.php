<?php
/* API RESTful para gestionar usuarios
 * Permite operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * Requiere conexión a una base de datos MySQL
 */

// Importa las dependencias necesarias
require_once 'config.php';
require_once 'usuario.php';

// Crea la instance de la clase Usuario
$usuarioObj = new Usuario($conn);
// Obtiene el método de la solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];
// Obtiene el endpoint de la solicitud
$endpoint = $_SERVER['PATH_INFO'];
// Establece el tipo de contenido de la respuesta (json)
header('Content-Type: application/json');

// Procesa la solicitud según el método HTTP
switch ($method) {
	case 'GET':
		if($endpoint === '/usuarios'){
			// Obtiene todos los usuarios
			$usuarios = $usuarioObj->getAllUsuarios();
			echo json_encode($usuarios);
		} elseif (preg_match('/^\/usuarios\/(\d+)$/', $endpoint, $matches)) {
			// Obtiene un usuario por ID
			$usuarioId = $matches[1];
			$usuario = $usuarioObj->getUsuarioById($usuarioId);
			echo json_encode($usuario);			
		}
		break;
	case 'POST':
		if($endpoint === '/usuarios'){
			// Añade un nuevo usuario
			$data = json_decode(file_get_contents('php://input'), true);
			$result = $usuarioObj->addUsuario($data);
			if ($result === true) {
				http_response_code(201);
				echo json_encode(['success' => $result]);
			}else{
				http_response_code(400);
				echo json_encode(['error' => 'Datos incompletos o error al registrar usuario']);
			}
		}elseif ($endpoint === '/login') {
			$data = json_decode(file_get_contents('php://input'), true);
			$result = $usuarioObj->loginUsuario($data['usr_email'], $data['usr_pass']);
			if ($result != false) {
				echo json_encode(['success' => true, 'usuario' => $result]);
			} else {
				http_response_code(401);
				echo json_encode(['error' => 'Credenciales incorrectas']);
			}
		}elseif ($endpoint === '/logout') {
			// Cierra sesión del usuario
			$data = json_decode(file_get_contents('php://input'), true);
			if (isset($data['usr_key'])) {
				// Aquí podrías implementar la lógica para cerrar sesión, como invalidar la clave de sesión
				$result = $usuarioObj->logoutUsuario( $data['usr_key']);
				if($result === true) {
					http_response_code(200);
					echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
				} else {
					http_response_code(400);
					echo json_encode(['error' => 'Error al cerrar sesión']);
				}
			} else {
				http_response_code(400);
				echo json_encode(['error' => 'Datos incompletos']);
			}
		}
		break;
	case 'PUT':
		if (preg_match('/^\/usuarios\/(\d+)$/', $endpoint, $matches)) {
			// Actualiza un usuario por ID
			$usuarioId = $matches[1];
			parse_str(file_get_contents('php://input'), $data);
			$result = $usuarioObj->updateUsuario($usuarioId, $data);
			echo json_encode(['success' => $result]);
		}
		break;
	case 'DELETE':
		if (preg_match('/^\/usuarios\/(\d+)$/', $endpoint, $matches)) {
			// Elimina un usuario por ID
			$usuarioId = $matches[1];
			$result = $usuarioObj->deleteUsuario($usuarioId);
			echo json_encode(['success' => $result]);
		}
		break;
	default:
		// Maneja métodos no permitidos
		header('Allow: GET, POST, PUT, DELETE');
		http_response_code(405);
		echo json_encode(['error' => 'Método no permitido']);
		break;
}
?>