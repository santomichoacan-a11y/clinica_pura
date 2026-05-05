<?php
/**
 * view/layout/nav.php
 * Barra de navegación y sidebar unificados utilizando la constante de ruta global optimizada
 */

// Respaldo de seguridad en caso de que la constante no se haya inicializado en el bootstrap
if (!defined('BASE_URL')) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    define('BASE_URL', $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/");
}
?>
    <div class="md:hidden bg-white border-b border-slate-100 p-4 flex justify-between items-center z-50 sticky top-0">
        <div class="flex items-center gap-2 text-blue-600 font-bold text-xl tracking-wide uppercase">
            <i class="fa-solid fa-heart-pulse text-cyan-500 text-2xl"></i>
            <span>Clínica <span class="text-slate-800">Pura</span></span>
        </div>
        <button id="menu-toggle" class="p-2 text-slate-500 hover:text-slate-800 focus:outline-none">
            <i class="fa-solid fa-bars-staggered text-2xl"></i>
        </button>
    </div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 md:w-20 bg-white border-r border-slate-100 p-4 flex flex-col items-center transform -translate-x-full md:translate-x-0 md:relative transition-transform duration-300 ease-in-out">
        
        <div class="mb-8 hidden md:flex items-center justify-center w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl shadow-sm">
            <i class="fa-solid fa-heart-pulse text-2xl"></i>
        </div>
        
        <nav class="space-y-3 flex-1 w-full md:flex md:flex-col md:items-center mt-12 md:mt-0">
            <a href="<?= BASE_URL ?>dashboard" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all" title="Dashboard">
                <i class="fa-solid fa-chart-pie text-lg md:mr-0 mr-3"></i>
                <span class="md:hidden font-medium">Dashboard</span>
            </a>
            
            <a href="<?= BASE_URL ?>pacientes" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'pacientes') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all group" title="Pacientes">
                <i class="fa-solid fa-user-injured text-lg md:mr-0 mr-3 group-hover:scale-110 transition-transform"></i>
                <span class="md:hidden font-medium">Pacientes</span>
            </a>

            <?php if(isset($role) && $role === 'admin'): ?>
                <a href="<?= BASE_URL ?>usuarios" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'usuarios') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all group" title="Gestionar Staff">
                    <i class="fa-solid fa-users-gear text-lg md:mr-0 mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="md:hidden font-medium">Gestionar Staff</span>
                </a>
                
                <a href="<?= BASE_URL ?>reportes" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'reportes') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all group" title="Reportes">
                    <i class="fa-solid fa-file-invoice-dollar text-lg md:mr-0 mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="md:hidden font-medium">Reportes</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="w-full md:w-auto pt-4 border-t border-slate-100 flex md:justify-center">
            <a href="<?= BASE_URL ?>logout" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 text-red-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Cerrar Sesión">
                <i class="fa-solid fa-arrow-right-from-bracket text-lg md:mr-0 mr-3"></i>
                <span class="md:hidden font-medium">Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-30 hidden md:hidden"></div>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full max-w-[1600px] mx-auto">
        
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="relative w-full sm:w-72 md:w-96 z-50">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input type="text" id="search-input" autocomplete="off" placeholder="Buscar pacientes por nombre o DNI..." 
                    class="w-full pl-11 pr-4 py-2.5 bg-white rounded-full text-sm text-slate-700 placeholder-slate-400 border border-transparent focus:border-blue-200 focus:outline-none soft-shadow transition-all">
                
                <div id="search-results" class="absolute left-0 right-0 mt-2 bg-white rounded-2xl border border-slate-100 shadow-xl hidden overflow-hidden max-h-60 overflow-y-auto">
                </div>
            </div>
            
            <div class="flex items-center gap-3 bg-white pl-4 pr-2 py-1.5 rounded-full border border-slate-100 soft-shadow self-end sm:self-auto">
                <div class="text-right">
                    <p class="text-slate-800 font-bold text-sm leading-tight"><?= htmlspecialchars($username ?? 'Usuario') ?></p>
                    <span class="text-[9px] text-blue-500 font-bold uppercase tracking-wider"><?= htmlspecialchars($role ?? 'invitado') ?></span>
                </div>
                <div class="h-9 w-9 bg-gradient-to-tr from-blue-500 to-cyan-400 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    <i class="fa-solid fa-user-md"></i>
                </div>
            </div>
        </header>