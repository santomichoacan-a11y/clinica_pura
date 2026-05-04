<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: /login"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">

    <!-- Sidebar -->
    <div class="w-64 h-screen bg-slate-900 text-white p-6 shadow-xl">
        <h2 class="text-xl font-bold mb-8 text-blue-400"><i class="fa-solid fa-stethoscopes mr-2"></i> MedAdmin</h2>
        <nav class="space-y-4">
            <a href="/dashboard" class="block py-2.5 px-4 rounded transition bg-blue-600">Dashboard</a>
            <a href="/pacientes" class="block py-2.5 px-4 rounded transition hover:bg-slate-800">Pacientes</a>
            
            <?php if($_SESSION['role'] == 'admin'): ?>
            <div class="pt-4 border-t border-slate-700">
                <p class="text-xs text-slate-500 uppercase mb-2">Administración</p>
                <a href="/roles" class="block py-2.5 px-4 rounded transition hover:bg-slate-800">Gestionar Roles</a>
            </div>
            <?php endif; ?>
            
            <a href="/logout" class="block py-2.5 px-4 rounded transition text-red-400 hover:bg-red-900/20 mt-10">Cerrar Sesión</a>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-slate-800">Panel Principal</h1>
            <div class="flex items-center space-x-4">
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold uppercase">
                    <?= $_SESSION['role'] ?>
                </span>
                <p class="text-slate-600 font-medium">Bienvenido, Médico</p>
            </div>
        </header>

        <!-- Cards Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-slate-500 text-sm">Total Pacientes</p>
                <h3 class="text-2xl font-bold">1,250</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-slate-500 text-sm">Consultas Hoy</p>
                <h3 class="text-2xl font-bold">12</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-slate-500 text-sm">Mi Perfil</p>
                <a href="/change-password" class="text-blue-600 text-sm hover:underline">Cambiar contraseña</a>
            </div>
        </div>
    </main>
</body>
</html>