<?php
/**
 * app/controllers/users_controller.php
 */
require_once __DIR__ . '/../config/app.php';
checkAuth(); 
require_once ROOT_PATH . 'app/config/db.php';

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? 'list';
$error = '';

// --- PROCESAMIENTO DE FORMULARIOS (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. CREAR USUARIO
    if ($action === 'create') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'] ?? 'user';

        if (!empty($username) && !empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            try {
                $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':username' => $username,
                    ':password' => $hashedPassword,
                    ':role' => $role
                ]);
                // Mensaje en español para SweetAlert
                $_SESSION['flash_success'] = "Usuario registrado correctamente.";
                header("Location: " . BASE_URL . "usuarios");
                exit;
            } catch (PDOException $e) {
                $error = "Username already exists.";
            }
        } else {
            $error = "All fields are required.";
        }
    }

    // 2. ACTUALIZAR USUARIO
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $username = trim($_POST['username']);
        $role = $_POST['role'];
        
        try {
            if (!empty($_POST['password'])) {
                $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $query = "UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id";
                $params = [':username' => $username, ':password' => $hashedPassword, ':role' => $role, ':id' => $id];
            } else {
                $query = "UPDATE users SET username = :username, role = :role WHERE id = :id";
                $params = [':username' => $username, ':role' => $role, ':id' => $id];
            }
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            // Mensaje en español para SweetAlert
            $_SESSION['flash_success'] = "Datos actualizados correctamente.";
            header("Location: " . BASE_URL . "usuarios");
            exit;
        } catch (PDOException $e) {
            $error = "Error updating user database.";
        }
    }
}

// --- PROCESAMIENTO DE ELIMINACIONES (GET) ---
if ($action === 'delete') {
    $id = (int)$_GET['id'];
    
    if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['flash_error'] = "You cannot delete your own active account.";
    } else {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $id]);
        // Mensaje en español para SweetAlert
        $_SESSION['flash_success'] = "Usuario eliminado con éxito.";
    }
    header("Location: " . BASE_URL . "usuarios");
    exit;
}

// --- CONSULTA GENERAL ---
$query = "SELECT id, username, role FROM users ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$userToEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $idEdit = (int)$_GET['id'];
    $queryEdit = "SELECT id, username, role FROM users WHERE id = :id";
    $stmtEdit = $db->prepare($queryEdit);
    $stmtEdit->execute([':id' => $idEdit]);
    $userToEdit = $stmtEdit->fetch(PDO::FETCH_ASSOC);
}

// Carga la vista final
include_once VIEW_PATH . 'users/index.php';