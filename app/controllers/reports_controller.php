<?php
/**
 * app/controllers/reports_controller.php
 */
require_once __DIR__ . '/../config/app.php';
checkAuth(); 
require_once ROOT_PATH . 'app/config/db.php';

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? 'index';
$activePage = 'reportes'; 

switch ($action) {
    case 'index':
        include_once VIEW_PATH . 'reports/index.php';
        break;

    case 'backup':
        generarBackup($db);
        break;

    // --- NUEVO ENDPOINT PARA AJAX ---
    case 'get_history':
        header('Content-Type: application/json');
        try {
            $query = "SELECT l.*, u.username FROM system_logs l 
                      JOIN users u ON l.user_id = u.id 
                      ORDER BY l.created_at DESC LIMIT 20";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Formatear fechas para que se vean bien en el JS
            foreach ($logs as &$log) {
                $log['created_at'] = date('d/m/Y H:i', strtotime($log['created_at']));
            }

            echo json_encode($logs);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit; // Cortamos aquí para que no devuelva HTML
        break;

    case 'restore':
        // Lógica de restauración pendiente
        break;

        // Dentro del switch ($action) en reports_controller.php

    case 'restore':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
            restaurarSistema($db, $_FILES['backup_file']);
        } else {
            $_SESSION['flash_error'] = "No se recibió ningún archivo.";
            header("Location: " . BASE_URL . "reports");
        }
        break;
}

function restaurarSistema($db, $file) {
    // 1. Validaciones básicas
    $fileName = $file['name'];
    $fileTmp  = $file['tmp_name'];
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

    if ($extension !== 'sql') {
        $_SESSION['flash_error'] = "Solo se permiten archivos .sql";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    try {
        // 2. Leer el contenido del archivo
        $sql = file_get_contents($fileTmp);
        
        // 3. Desactivar revisión de llaves foráneas para poder limpiar la BD
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");

        // 4. Obtener todas las tablas para limpiarlas (DROP)
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $db->exec("DROP TABLE IF EXISTS `$table` shadow-slate-200");
        }

        // 5. Ejecutar el SQL del respaldo
        // Usamos exec directamente para el string completo de tablas e inserts
        $db->exec($sql);

        // 6. Reactivar llaves foráneas
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");

        // 7. Registrar en el historial
        $logQuery = "INSERT INTO system_logs (user_id, action_type, file_name, description) 
                     VALUES (:u, :t, :f, :d)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([
            ':u' => $_SESSION['user_id'],
            ':t' => 'restore',
            ':f' => $fileName,
            ':d' => 'Restauración completa del sistema realizada con éxito.'
        ]);

        $_SESSION['flash_success'] = "Sistema restaurado correctamente.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;

    } catch (Exception $e) {
        $db->exec("SET FOREIGN_KEY_CHECKS = 1"); // Asegurar que se reactiven si falla
        $_SESSION['flash_error'] = "Error en restauración: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

function generarBackup($db) {
    if (!$db) {
        $_SESSION['flash_error'] = "Error de conexión.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    try {
        $tables = [];
        $result = $db->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) { $tables[] = $row[0]; }

        $return = "-- Backup Clinica Pura\n-- " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $res = $db->query("SHOW CREATE TABLE $table");
            $row = $res->fetch(PDO::FETCH_NUM);
            $return .= "\n\n" . $row[1] . ";\n\n";

            $resultData = $db->query("SELECT * FROM $table");
            while ($rowD = $resultData->fetch(PDO::FETCH_ASSOC)) {
                $return .= "INSERT INTO $table VALUES(";
                $values = array_map(function($v) use ($db) {
                    return ($v === null) ? 'NULL' : $db->quote($v);
                }, array_values($rowD));
                $return .= implode(',', $values) . ");\n";
            }
        }

        $fileName = 'backup_' . date('Ymd_His') . '.sql';

        $logQuery = "INSERT INTO system_logs (user_id, action_type, file_name, description) 
                     VALUES (:u, :t, :f, :d)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([
            ':u' => $_SESSION['user_id'],
            ':t' => 'backup',
            ':f' => $fileName,
            ':d' => 'Respaldo manual generado con éxito.'
        ]);

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename=' . $fileName);
        echo $return;
        exit;

    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}