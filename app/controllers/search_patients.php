<?php
/**
 * app/controllers/search_patients.php
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verificar autenticación de sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// 2. ENCONTRAR E INCLUIR TU ARCHIVO DB.PHP
$rutaConexion = "C:\\xampp\\htdocs\\clinica_pura\\app\\config\\db.php";

if (file_exists($rutaConexion)) {
    require_once $rutaConexion;
} else {
    $rutaConexion = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php';
    if (file_exists($rutaConexion)) {
        require_once $rutaConexion;
    } else {
        echo json_encode(['error' => 'Error crítico: El archivo no existe en la ruta interna.']);
        exit();
    }
}

// 3. INSTANCIAR LA CLASE Y OBTENER EL OBJETO PDO
if (class_exists('Database')) {
    $database = new Database();
    $conn = $database->getConnection();
} else {
    echo json_encode(['error' => 'La clase Database no existe dentro de db.php']);
    exit();
}

if (!$conn) {
    echo json_encode(['error' => 'La conexión a la base de datos devolvió null']);
    exit();
}

// 4. PROCESAR LA BÚSQUEDA POR NOMBRE O DNI
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($query === '') {
    echo json_encode([]);
    exit();
}

try {
    // CORRECCIÓN: Seleccionamos únicamente id, nombre y dni. 
    // Añadimos 'Masculino' AS genero como un valor simulado para mantener el color azul estético en el diseño.
    $sql = "SELECT id, nombre, dni, 'Masculino' AS genero FROM patients 
            WHERE nombre LIKE :query_nombre OR dni LIKE :query_dni 
            LIMIT 5";
            
    $stmt = $conn->prepare($sql);
    
    $searchTerm = "%" . $query . "%";
    
    $stmt->bindValue(':query_nombre', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':query_dni', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retornamos los resultados en formato JSON listos para JavaScript
    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error interno en la base de datos: ' . $e->getMessage()]);
}