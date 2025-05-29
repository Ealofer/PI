<?php
//Configuracion de la base de datos
$servername = "localhost";  // La dirección del servidor de tu base de datos
$username = "root";         // Tu usuario de la base de datos
$password = "";             // Tu contraseña de la base de datos
$dbname = "PI";             // El nombre de la base de datos

try {
    
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>