<?php
/**
 * view/dashboard/index.php
 */

// 1. Evitar que el navegador guarde esta página en el historial (Caché)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Validamos la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si NO existe una sesión activa, redirigir al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

// Datos de sesión (username y role vienen del login, totalPacientes viene del index.php)
$username = $_SESSION['username'] ?? 'Usuario';
$role = $_SESSION['role'] ?? 'invitado';
$totalReal = $totalPacientes ?? 0; // Variable enviada desde el enrutador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Clínica Pura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <!-- Sidebar Dinámico -->
    <aside class="w-64 bg-slate-900 text-white p-6 shadow-xl flex flex-col">
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-blue-400 flex items-center">
                <i class="fa-solid fa-house-medical mr-3"></i> Clínica Pura
            </h2>
            <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Gestión Médica</p>
        </div>
        
        <nav class="space-y-2 flex-1">
            <a href="dashboard" class="flex items-center py-3 px-4 rounded-lg transition bg-blue-600 shadow-lg shadow-blue-900/20">
                <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
            </a>
            
            <a href="pacientes" class="flex items-center py-3 px-4 rounded-lg transition hover:bg-slate-800 text-slate-300 hover:text-white">
                <i class="fa-solid fa-user-injured mr-3"></i> Pacientes
            </a>

            <?php if($role === 'admin'): ?>
            <div class="pt-6 mt-6 border-t border-slate-800">
                <p class="text-[10px] text-slate-500 uppercase font-bold mb-3 px-4">Administración</p>
                <a href="usuarios" class="flex items-center py-3 px-4 rounded-lg transition hover:bg-slate-800 text-slate-300 hover:text-white">
                    <i class="fa-solid fa-users-gear mr-3"></i> Gestionar Staff
                </a>
                <a href="reportes" class="flex items-center py-3 px-4 rounded-lg transition hover:bg-slate-800 text-slate-300 hover:text-white">
                    <i class="fa-solid fa-file-invoice-dollar mr-3"></i> Reportes
                </a>
            </div>
            <?php endif; ?>
        </nav>

        <div class="pt-6 border-t border-slate-800">
            <a href="logout" class="flex items-center py-3 px-4 rounded-lg transition text-red-400 hover:bg-red-900/20">
                <i class="fa-solid fa-right-from-bracket mr-3"></i> Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-10 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Panel de Control</h1>
                <p class="text-slate-500 text-sm">Resumen general de la clínica</p>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="text-right">
                    <p class="text-slate-800 font-bold leading-none"><?= htmlspecialchars($username) ?></p>
                    <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-md font-bold uppercase mt-1 inline-block">
                        <i class="fa-solid fa-user-shield text-[8px] mr-1"></i> <?= htmlspecialchars($role) ?>
                    </span>
                </div>
                <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-user-md text-xl"></i>
                </div>
            </div>
        </header>

        <!-- Estadísticas Dinámicas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-blue-300 transition-colors cursor-default">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mb-4">
                    <i class="fa-solid fa-hospital-user"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Pacientes Totales</p>
                <!-- Aquí mostramos el conteo real de la tabla 'patients' -->
                <h3 class="text-2xl font-bold text-slate-800"><?= number_format($totalReal) ?></h3>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-300 transition-colors">
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 mb-4">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Citas Hoy</p>
                <h3 class="text-2xl font-bold text-slate-800">0</h3>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 mb-4">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Pendientes</p>
                <h3 class="text-2xl font-bold text-slate-800">0</h3>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 mb-4">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <p class="text-slate-500 text-sm font-medium">Seguridad</p>
                <a href="perfil" class="text-blue-600 text-xs font-bold hover:underline">Ajustes de cuenta</a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-slate-800">Actividad Reciente</h2>
                <a href="pacientes" class="text-blue-600 text-sm font-semibold hover:text-blue-800">Ver todos</a>
            </div>
            
            <div class="text-slate-400 text-center py-20 border-2 border-dashed border-slate-100 rounded-xl">
                <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                <?php if($totalReal > 0): ?>
                    Hay <?= $totalReal ?> pacientes en el sistema. Haz clic en "Ver todos" para gestionarlos.
                <?php else: ?>
                    No hay pacientes registrados recientemente.
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>