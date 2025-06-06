<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, OPTIONS');
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json; charset=UTF-8");


include 'conexion.php'; // conexión PDO

$data = json_decode(file_get_contents("php://input"));

if ($data && isset($data->accion) && $data->accion === "SubirFoto") {
  subirFoto($conn, $data);
} else {
  echo json_encode(["result" => "Sin data o acción inválida"]);
}

function subirFoto($conn, $data) {
  $res = new stdClass();

  try {
    $base64 = $data->archivo;
    $nombre = $data->nombre_foto;
    $descripcion = $data->descripcion;
    $id_usuario = $data->id_usuario;
    $id_desafio = $data->id_desafio;

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

    $carpeta = "./imagenes/fotos/";
    $nombreArchivo = uniqid("foto_") . "." . $extension;
    $ruta = $carpeta . $nombreArchivo;
    file_put_contents($ruta, $decoded);

    $rutaGuardada = "https://rallypieaf.es/backend/imagenes/fotos/" . $nombreArchivo;

    $sql = "INSERT INTO fotos (id_usuario, id_desafio, nombre_foto, url_foto, descripcion) 
            VALUES (:id_usuario, :id_desafio, :nombre, :url_foto, :descripcion)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':id_desafio', $id_desafio, PDO::PARAM_INT);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':url_foto', $rutaGuardada, PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

    if ($stmt->execute()) {
      $res->status = "success";
      $res->message = "Foto registrada exitosamente.";
      $res->archivo_guardado = $rutaGuardada;
    } else {
      $res->status = "error";
      $res->message = "Error al registrar la foto.";
    }

  } catch (Exception $e) {
    $res->status = "error";
    $res->message = "Excepción: " . $e->getMessage();
  }

  echo json_encode($res, JSON_UNESCAPED_UNICODE);
}
