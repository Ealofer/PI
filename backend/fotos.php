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
    case 'ListarFotos':
        listarFotos($conn, $data);
        break;
    case 'CambiarEstadoFoto':
        cambiarEstadoFoto($conn, $data);
        break;
    case 'EliminarFoto':
        eliminarFoto($conn, $data);
        break;
    case 'EditarFoto':
        modificarFoto($conn, $data);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

function listarFotos($conn, $data) {
    $id_desafio = $data['id'] ?? null;

    if (!$id_desafio) {
        echo json_encode(['status' => 'error', 'message' => 'ID del deasafio no proporcionado']);
        return;
    }

    $sql = "SELECT * FROM fotos WHERE id_desafio = :id_desafio";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_desafio', $id_desafio, PDO::PARAM_INT);
    $stmt->execute();
    
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'fotos' => $fotos]);
}

function cambiarEstadoFoto($conn, $data) {
    $id_foto = $data['id_foto'] ?? null;
    $estado = $data['estado'] ?? null;

    $estados_validos = ['pendiente', 'aprobada', 'rechazada'];

    if (!$id_foto || !in_array($estado, $estados_validos)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
        return;
    }

    $sql = "UPDATE fotos SET estado = :estado WHERE id_foto = :id_foto";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id_foto', $id_foto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar estado']);
    }
}

function eliminarFoto($conn, $data) {
    $id_foto = $data['id_foto'] ?? null;

    if (!$id_foto) {
        echo json_encode(['status' => 'error', 'message' => 'ID de la foto no proporcionado']);
        return;
    }

    $sql = "DELETE FROM fotos WHERE id_foto = :id_foto";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_foto', $id_foto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Foto eliminada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la foto']);
    }
}

function modificarFoto($conn, $data) {
    $id_foto = $data['id_foto'] ?? null;
    $nombre = $data['nombre_foto'] ?? null;
    $descripcion = $data['descripcion'] ?? null;

    if (!$id_foto || $nombre === null || $descripcion === null) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos para modificar la foto']);
        return;
    }

    $estado="pendiente";

    $sql = "UPDATE fotos SET nombre_foto = :nombre, descripcion = :descripcion, estado = :estado  WHERE id_foto = :id_foto";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id_foto', $id_foto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Foto modificada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al modificar la foto']);
    }
}
