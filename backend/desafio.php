<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$accion = $data['accion'] ?? '';

switch ($accion) {
    case 'ListarDesafios':
        listarDesafios($conn);
        break;

    case 'CrearDesafio':
        crearDesafio($conn, $data);
        break;

    case 'ModificarDesafio':
        modificarDesafio($conn, data: $data);
        break;

    case 'EliminarDesafio':
        eliminarDesafio($conn, $data);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}


function listarDesafios($conn) {
    $sql = "SELECT * FROM desafios";
    $stmt = $conn->query($sql);
    $desafios = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $desafio = [
            'id_desafio' => $row['id_desafio'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'fecha_inicio' => $row['fecha_inicio'],
            'fecha_fin' => $row['fecha_fin'],
            'foto_url' => $row['foto_url'],
        ];
        $desafios[] = $desafio;
    }

    echo json_encode(['status' => 'success', 'desafios' => $desafios]);
}

function crearDesafio($conn, $data) {
    error_log('');
    $sql = "INSERT INTO desafios (nombre, descripcion, fecha_inicio, fecha_fin, foto_url) 
            VALUES (:nombre, :descripcion, :fecha_inicio, :fecha_fin, :foto_url)";
    
    $stmt = $conn->prepare($sql);
    $imagenBinaria = fopen($data['foto_url'], 'rb'); // abre archivo en modo binario

    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':descripcion', $data['descripcion']);
    $stmt->bindParam(':fecha_inicio', $data['fecha_inicio']);
    $stmt->bindParam(':fecha_fin', $data['fecha_fin']);
    $stmt->bindParam(':foto_url', $imagenBinaria, PDO::PARAM_LOB);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Desafío creado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear el desafío']);
    }
}

function modificarDesafio($conn, $data) {
    $sql = "UPDATE desafios 
        SET nombre = :nombre, 
            descripcion = :descripcion, 
            fecha_inicio = :fecha_inicio, 
            fecha_fin = :fecha_fin 
        WHERE id_desafio = :id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':descripcion', $data['descripcion']);
    $stmt->bindParam(':fecha_inicio', $data['fecha_inicio']);
    $stmt->bindParam(':fecha_fin', $data['fecha_fin']);

    $result = $stmt->execute();

    echo json_encode([
        'status' => $result ? 'success' : 'error',
        'message' => $result ? 'Desafío modificado' : 'Error al modificar el desafío'
    ]);
}

function eliminarDesafio($conn, $data) {
    if (!isset($data['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
        return;
    }

    $sql = "DELETE FROM desafios WHERE id_desafio = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute(['id' => $data['id']]);

    echo json_encode([
        'status' => $result ? 'success' : 'error',
        'message' => $result ? 'Desafío eliminado' : 'Error al eliminar el desafío'
    ]);
}
