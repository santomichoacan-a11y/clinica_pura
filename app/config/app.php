<?php
/**
 * app/config/app.php
 * Configuración global del sistema y blindaje de seguridad
 */

// 1. Control de Caché estricto para evitar el "Boton Atrás" tras Logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8');

// 2. Gestión segura de Sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Generación automática y dinámica de la URL Base Absoluta
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
define('BASE_URL', $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/");

// 4. Definición de rutas físicas del servidor para simplificar los 'includes'
define('ROOT_PATH', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
define('VIEW_PATH', ROOT_PATH . 'view' . DIRECTORY_SEPARATOR);

/**
 * Función auxiliar para proteger rutas privadas
 */
function checkAuth() {
    // MODIFICADO: Validamos que exista tanto el ID como el nombre de usuario
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
        
        // Si no existen, destruimos cualquier rastro de sesión vieja por seguridad
        $_SESSION = array();
        if (session_id() != "") { 
            session_destroy(); 
        }
        
        // Redirección limpia usando la constante global BASE_URL
        header("Location: " . BASE_URL . "login");
        exit();
    }
}