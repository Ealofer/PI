<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, OPTIONS');
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include 'conexion.php'; // conexión PDO

$data = json_decode(file_get_contents("php://input"));

if ($data && isset($data->accion) && $data->accion === "SubirDesafio") {
  subirDesafio($conn, $data);
} else {
  echo json_encode(["result" => "Sin data o acción inválida"]);
}

function subirDesafio($conn, $data) {
  $res = new stdClass();

  try {
    $base64 = $data->archivo;
    $nombre = $data->nombre;
    $descripcion = $data->descripcion;
    $fecha_inicio = $data->fecha_inicio;
    $fecha_fin = $data->fecha_fin;

    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
      $base64 = substr($base64, strpos($base64, ',') + 1);
      $extension = strtolower($type[1]);
    } else {
      throw new Exception("Formato de imagen inválido");
    }

    $decoded = base64_decode($base64);
    if ($decoded === false) {
      throw new Exception("Contenido Base64 inválido");
    }

    $carpeta = "./imagenes/desafios/";

    $nombreArchivo = uniqid("desafio_") . "." . $extension;
    $ruta = $carpeta . $nombreArchivo;
    file_put_contents($ruta, $decoded);

    $rutaGuardada = "http://localhost/clase/backend/imagenes/desafios/" . $nombreArchivo;

    $sql = "INSERT INTO desafios (nombre, foto_url, descripcion, fecha_inicio, fecha_fin) 
            VALUES (:nombre, :foto_url, :descripcion, :fecha_inicio, :fecha_fin)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':foto_url', $rutaGuardada, PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);

    if ($stmt->execute()) {
      $res->status = "success";
      $res->message = "Desafío registrado exitosamente.";
      $res->foto_url = $rutaGuardada;
    } else {
      $res->status = "error";
      $res->message = "Error al registrar el desafío.";
    }

  } catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}

  echo json_encode($res, JSON_UNESCAPED_UNICODE);
}
