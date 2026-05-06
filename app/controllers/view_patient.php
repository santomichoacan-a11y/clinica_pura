<?php
/**
 * app/controllers/view_patient.php
 * Controlador para visualizar y actualizar la ficha detallada de un paciente
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

// 2. CONFIGURACIÓN DE RUTA ABSOLUTA AUTOMÁTICA
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
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
    header("Location: " . $baseUrl . "index.php?error=id_invalido");
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // =========================================================================
    // NUEVA LÓGICA: PROCESAR EL ENVIÓ DEL MODAL DE EDICIÓN (PETICIÓN POST)
    // =========================================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $nombre = trim($_POST['nombre'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $historial = trim($_POST['historial'] ?? '');

        if (!empty($nombre) && !empty($dni)) {
            $sql_update = "UPDATE patients 
                           SET nombre = :nombre, dni = :dni, telefono = :telefono, email = :email, historial = :historial 
                           WHERE id = :id";
            
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':nombre', $nombre);
            $stmt_update->bindParam(':dni', $dni);
            $stmt_update->bindParam(':telefono', $telefono);
            $stmt_update->bindParam(':email', $email);
            $stmt_update->bindParam(':historial', $historial);
            $stmt_update->bindParam(':id', $id_paciente, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                // Mensaje temporal de éxito para reflejar en el layout
                $status_msg = "✅ Cambios guardados correctamente.";
            } else {
                $status_msg = "❌ Error al intentar actualizar en la base de datos.";
            }
        } else {
            $status_msg = "❌ El nombre y el DNI son campos obligatorios.";
        }
    }
    // =========================================================================

    // Cargar los datos frescos del paciente (Refleja los cambios si se guardó el POST)
    $sql = "SELECT id, nombre, dni, telefono, email, historial 
            FROM patients 
            WHERE id = :id 
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_paciente, PDO::PARAM_INT);
    $stmt->execute();

    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        header("Location: " . $baseUrl . "index.php?error=no_encontrado");
        exit();
    }

    // Configuración de variables requeridas por el Layout
    $activePage = 'pacientes'; 
    $username = $_SESSION['username'] ?? 'Médico';
    $role = $_SESSION['role'] ?? 'invitado'; 

    // 4. Cargar la interfaz
    include __DIR__ . '/../../view/layout/header.php';
    include __DIR__ . '/../../view/layout/nav.php'; 
    
    // Si hay un mensaje de actualización, lo imprimimos rápido arriba
    if (isset($status_msg)) {
        echo "<div class='max-w-5xl mx-auto mt-4 px-4'><div class='p-3 bg-blue-50 border border-blue-200 text-blue-700 text-sm font-semibold rounded-2xl text-center shadow-sm'>{$status_msg}</div></div>";
    }

    include __DIR__ . '/../../view/patients/view_patient.php';  
    include __DIR__ . '/../../view/layout/footer.php';

} catch (PDOException $e) {
    die("Error interno en la base de datos: " . $e->getMessage());
}