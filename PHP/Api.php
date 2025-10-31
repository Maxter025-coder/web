<?php
header("Content-Type: application/json");
$archivo = 'datos.xml';

// Crear XML si no existe
if (!file_exists($archivo)) {
    $nuevo = new SimpleXMLElement('<personas></personas>');
    $nuevo->asXML($archivo);
}

$xml = simplexml_load_file($archivo);
$metodo = $_SERVER['REQUEST_METHOD'];

// Función auxiliar
function enviarRespuesta($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($metodo) {
    case 'GET':
        // Si hay un parámetro id, mostrar solo esa persona
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if (isset($xml->persona[$id])) {
                $p = $xml->persona[$id];
                enviarRespuesta([
                    'id' => $id,
                    'nombre' => (string)$p->nombre,
                    'apellido' => (string)$p->apellido,
                    'correo' => (string)$p->correo,
                    'telefono' => (string)$p->telefono,
                    'fecha_nacimiento' => (string)$p->fecha_nacimiento
                ]);
            } else {
                enviarRespuesta(['error' => 'Persona no encontrada'], 404);
            }
        } else {
            // Devolver todos
            $personas = [];
            foreach ($xml->persona as $i => $p) {
                $personas[] = [
                    'id' => $i,
                    'nombre' => (string)$p->nombre,
                    'apellido' => (string)$p->apellido,
                    'correo' => (string)$p->correo,
                    'telefono' => (string)$p->telefono,
                    'fecha_nacimiento' => (string)$p->fecha_nacimiento
                ];
            }
            enviarRespuesta($personas);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) enviarRespuesta(['error' => 'Datos no válidos'], 400);

        $persona = $xml->addChild('persona');
        $persona->addChild('nombre', $data['nombre'] ?? '');
        $persona->addChild('apellido', $data['apellido'] ?? '');
        $persona->addChild('correo', $data['correo'] ?? '');
        $persona->addChild('telefono', $data['telefono'] ?? '');
        $persona->addChild('fecha_nacimiento', $data['fecha_nacimiento'] ?? '');

        $xml->asXML($archivo);
        enviarRespuesta(['mensaje' => 'Persona agregada correctamente']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        if ($id === null || !isset($xml->persona[intval($id)]))
            enviarRespuesta(['error' => 'Persona no encontrada'], 404);

        $p = $xml->persona[intval($id)];
        $p->nombre = $data['nombre'] ?? $p->nombre;
        $p->apellido = $data['apellido'] ?? $p->apellido;
        $p->correo = $data['correo'] ?? $p->correo;
        $p->telefono = $data['telefono'] ?? $p->telefono;
        $p->fecha_nacimiento = $data['fecha_nacimiento'] ?? $p->fecha_nacimiento;

        $xml->asXML($archivo);
        enviarRespuesta(['mensaje' => 'Persona actualizada correctamente']);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id === null || !isset($xml->persona[intval($id)]))
            enviarRespuesta(['error' => 'Persona no encontrada'], 404);

        unset($xml->persona[intval($id)]);
        $xml->asXML($archivo);
        enviarRespuesta(['mensaje' => 'Persona eliminada correctamente']);
        break;

    default:
        enviarRespuesta(['error' => 'Método no permitido'], 405);
}
?>
