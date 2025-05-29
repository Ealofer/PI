<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json");

include 'conexion.php'; // conexión PDO

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'] ?? '';
$email = $data['email'] ?? '';
$password = password_hash($data['password'] ?? '', PASSWORD_DEFAULT);
$isParticipante = $data['isParticipante'] ?? false;

if (empty($nombre) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit;
}

try {
    $sql1 = "SELECT id_usuario FROM usuarios WHERE email = :email";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bindParam(':email', $email);
    $stmt1->execute();
    //Si el email existe error
    if ($stmt1->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "El correo ya está registrado."]);
        exit;
    }else{
        $rol = $isParticipante ? "Participante" : "Usuario";
        $sql2 = "INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (:nombre, :email, :password, :rol)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':nombre', $nombre);
        $stmt2->bindParam(':email', $email);
        $stmt2->bindParam(':password', $password);
        $stmt2->bindParam(':rol', $rol);
        if ($stmt2->execute()) {
            echo json_encode(["status" => "success", "message" => "Usuario registrado exitosamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrar el usuario."]);
        }
    }    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()]);
}
?>
