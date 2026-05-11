<?php
/**
 * app/controllers/dashboard_controller.php
 */
require_once __DIR__ . '/../config/app.php';
checkAuth();

date_default_timezone_set('America/El_Salvador'); 

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
$pageTitle = "Dashboard | Clínica Pura";
$activePage = "dashboard";

require_once ROOT_PATH . 'app/config/db.php';

// Configuración inicial del flujo semanal para la gráfica
$numeroDiaActual = (int)date('N'); 
$flujoSemanalProcesado = [
    'Lun' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 1)],
    'Mar' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 2)],
    'Mié' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 3)],
    'Jue' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 4)],
    'Vie' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 5)]
];

$pacientesRecientes = [];
$totalReal = 0;
$totalConsultas = 0;
$totalUsuarios = 0;
$consultasHoy = 0;

try {
    $database = new Database();
    $db = $database->getConnection();

    // 1. Obtener pacientes recientes (para la lista/tabla)
    $stmtRecientes = $db->prepare("SELECT id, nombre, dni FROM patients ORDER BY id DESC LIMIT 3");
    $stmtRecientes->execute();
    $pacientesRecientes = $stmtRecientes->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Contador: Pacientes Totales
    $stmtTotal = $db->query("SELECT COUNT(id) as total FROM patients");
    $totalReal = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 3. Contador: Consultas Totales (Tabla consultations)
    $stmtCountConsultas = $db->query("SELECT COUNT(id) as total FROM consultations");
    $totalConsultas = $stmtCountConsultas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 4. Contador: Usuarios Totales (Tabla users)
    $stmtCountUsuarios = $db->query("SELECT COUNT(id) as total FROM users");
    $totalUsuarios = $stmtCountUsuarios->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 5. Contador: Consultas de Hoy
    $fechaHoy = date('Y-m-d');
    $stmtHoy = $db->prepare("SELECT COUNT(id) as total FROM consultations WHERE DATE(created_at) = :hoy");
    $stmtHoy->execute([':hoy' => $fechaHoy]);
    $consultasHoy = $stmtHoy->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 6. Lógica de la Gráfica de Flujo Semanal
    $diasMapeo = [0 => 'Lun', 1 => 'Mar', 2 => 'Mié', 3 => 'Jue', 4 => 'Vie'];
    $fechaLunes = date('Y-m-d', strtotime('monday this week'));
    $fechaViernes = date('Y-m-d', strtotime('friday this week'));

    $queryEst = "SELECT WEEKDAY(created_at) as dia_indice, COUNT(id) as total 
                 FROM patients WHERE DATE(created_at) BETWEEN :inicio AND :fin
                 GROUP BY WEEKDAY(created_at)";
    
    $stmtEst = $db->prepare($queryEst);
    $stmtEst->execute([':inicio' => $fechaLunes, ':fin' => $fechaViernes]);
    $resultadosEst = $stmtEst->fetchAll(PDO::FETCH_ASSOC);

    $maxPacientes = 0;
    foreach ($resultadosEst as $row) {
        $indice = (int)$row['dia_indice'];
        if (isset($diasMapeo[$indice])) {
            $dia = $diasMapeo[$indice];
            $flujoSemanalProcesado[$dia]['conteo'] = (int)$row['total'];
            $maxPacientes = max($maxPacientes, (int)$row['total']);
        }
    }

    // Calcular porcentajes para las barras de la gráfica
    foreach ($flujoSemanalProcesado as $dia => $datos) {
        if ($maxPacientes > 0) {
            $flujoSemanalProcesado[$dia]['porcentaje'] = round(($datos['conteo'] / $maxPacientes) * 100);
        }
    }

} catch (PDOException $e) {
    die("Error en el Dashboard: " . $e->getMessage());
}