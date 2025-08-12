<?php
/* API RESTful de prueba
 * Requiere conexión a una base de datos MySQL
 */

// Importa las dependencias necesarias
require_once 'config.php';
require_once 'test.php';

// Crea la instance de la clase Test
$testObj = new Test($conn);
// Obtiene el método de la solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];
// Obtiene el endpoint de la solicitud
$endpoint = $_SERVER['PATH_INFO'];
// Establece el tipo de contenido de la respuesta (json)
header('Content-Type: application/json');

// Procesa la solicitud según el método HTTP
switch ($method) {
	case 'GET':
		// sin endpoint específico
		break;
	case 'POST':
		if ($endpoint === '/testkey') {
			$data = json_decode(file_get_contents('php://input'), true);
			$result = $testObj->testKey($data['key']);
			if ($result) {
				echo json_encode(['success' => true]);
			} else {
				http_response_code(401);
				echo json_encode(['error' => 'Clave incorrecta']);
			}
		}
		break;
	case 'PUT':
		// sin endpoint específico
		break;
	case 'DELETE':
		// sin endpoint específico
		break;
	default:
		// Maneja métodos no permitidos
		header('Allow: GET, POST, PUT, DELETE');
		http_response_code(405);
		echo json_encode(['error' => 'Método no permitido']);
		break;
}
?>