<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

try {
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            echo json_encode([
                "status" => "success",
                "message" => "Login exitoso",
                "user" => [
                    "id_usuario" => $user['id_usuario'],
                    "nombre" => $user['nombre'],
                    "email" => $user['email'],
                    "rol" => $user['rol'],
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Correo o contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Correo o contraseña incorrecta"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error en la base de datos: " . $e->getMessage()]);
}
?>
