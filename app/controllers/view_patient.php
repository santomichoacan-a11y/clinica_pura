<?php
/**
 * app/controllers/view_patient.php
 * Controlador para visualizar la ficha detallada de un paciente
 */

header('Content-Type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verificar autenticación de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../public/index.php");
    exit();
}

// 2. CONFIGURACIÓN DE RUTA ABSOLUTA AUTOMÁTICA (CON EL CORTE DE PUBLIC)
// Dado que tu .htaccess tiene la directiva: RewriteBase /clinica_pura/public/
// Todos tus enlaces en las vistas deben apuntar directamente a esa carpeta pública.
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// Forzamos que termine estrictamente en /clinica_pura/public/
$baseUrl = $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/";

// 3. Incluir el archivo de conexión
$rutaConexion = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php';

if (file_exists($rutaConexion)) {
    require_once $rutaConexion;
} else {
    die("Error crítico: No se encontró el archivo de conexión.");
}

$id_paciente = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_paciente <= 0) {
    // Usamos la URL absoluta corregida para redirigir en caso de error
    header("Location: " . $baseUrl . "index.php?error=id_invalido");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sql = "SELECT id, nombre, dni, telefono, email, historial 
            FROM patients 
            WHERE id = :id 
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_paciente, PDO::PARAM_INT);
    $stmt->execute();

    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        // Usamos la URL absoluta corregida para redirigir
        header("Location: " . $baseUrl . "index.php?error=no_encontrado");
        exit();
    }

    // Configuración de variables requeridas por el Layout
    $activePage = 'pacientes'; 
    $username = $_SESSION['username'] ?? 'Médico';
    $role = $_SESSION['role'] ?? 'invitado'; // Aseguramos que la variable de rol pase al nav

    // 4. Cargar la interfaz
    include __DIR__ . '/../../view/layout/header.php';
    include __DIR__ . '/../../view/layout/nav.php'; // Recibe el $baseUrl con /public/
    include __DIR__ . '/../../view/patients/view_patient.php';  
    include __DIR__ . '/../../view/layout/footer.php';

} catch (PDOException $e) {
    die("Error interno en la base de datos: " . $e->getMessage());
}