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
    case 'ListarUsuarios':
        listarUsuarios($conn);
        break;
    case 'ModificarUsuario':
        modificarUsuario($conn, $data);
        break;
    case 'EliminarUsuario':
        eliminarUsuario($conn, $data);
        break;
    case 'ObtenerRolUsuario':
        obtenerRolUsuario($conn, $data);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

function listarUsuarios($conn) {
    $sql = "SELECT id_usuario, nombre, email, rol, activo, fecha_creacion FROM usuarios";
    $stmt = $conn->query($sql);
    $usuarios = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $usuarios[] = [
            'id_usuario'=> $row['id_usuario'],
            'nombre' =>$row['nombre'],
            'email' => $row['email'],
            'rol' => $row['rol'],
            'activo' => (bool)$row['activo'],
            'fecha_creacion' => $row['fecha_creacion']
        ];
    }
    echo json_encode(['status' => 'success', 'usuarios' => $usuarios]);
}

function modificarUsuario($conn, $data) {
    $sql = "UPDATE usuarios 
            SET nombre = :nombre, 
                email = :email, 
                rol = :rol, 
                activo = :activo 
            WHERE id_usuario = :id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':rol', $data['rol']);
    $stmt->bindParam(':activo', $data['activo'], PDO::PARAM_BOOL);

    $result = $stmt->execute();

    echo json_encode([
        'status' => $result ? 'success': 'error',
        'message' => $result ? 'Usuario modificado': 'Error al modificar el usuario'
    ]);
}

function eliminarUsuario($conn, $data) {
    if (!isset($data['id'])) {
        echo json_encode(['status'=> 'error', 'message'=> 'ID no proporcionado']);
        return;
    }

    $sql="DELETE FROM usuarios WHERE id_usuario = :id";
    $stmt= $conn->prepare($sql);
    $result =$stmt->execute(['id' => $data['id']]);

    echo json_encode([
        'status'=> $result ? 'success': 'error',
        'message'=> $result ? 'Usuario eliminado': 'Error al eliminar el usuario'
    ]);  
}
function obtenerRolUsuario($conn, $data) {
    if (!isset($data['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
        return;
    }

    $sql = "SELECT rol FROM usuarios WHERE id_usuario = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->execute();
    $rol= $stmt->fetchColumn();

    if ($rol !== false) {
        echo json_encode(['status' => 'success', 'rol' => $rol]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }
}