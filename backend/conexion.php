<?php
$servername = "PMYSQL189.dns-servicio.com:3306";
$username = "root2"; 
$password = "Ti715un3?";  
$dbname = "10944248_PI"; 

try {
    
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Conexion fallida: " . $e->getMessage());
}
?>