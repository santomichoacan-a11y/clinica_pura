<?php
// Ajustamos la ruta para llegar a app/config desde public/
require_once '../app/config/db.php';

$database = new Database();
$db = $database->getConnection();

// Datos del usuario (Admin por defecto)
$username = "admin";
$plain_password = "admin123"; 
$role = "admin";

// Encriptación BCRYPT
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

try {
    $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":role", $role);

    if($stmt->execute()) {
        echo "✅ Credenciales encriptadas guardadas correctamente.<br>";
        echo "<b>Usuario:</b> admin | <b>Clave:</b> admin123";
    }
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>