<?php
/**
 * view/layout/nav.php
 */

if (!defined('BASE_URL')) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    define('BASE_URL', $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/");
}
?>

<div class="md:hidden bg-white border-b border-slate-100 p-4 flex justify-between items-center z-50 sticky top-0 h-[73px]">
    <div class="flex items-center gap-2 text-blue-600 font-bold text-xl tracking-wide uppercase">
        <i class="fa-solid fa-heart-pulse text-cyan-500 text-2xl"></i>
        <span>Clínica <span class="text-slate-800">Pura</span></span>
    </div>
    <button id="menu-toggle" class="p-2 text-slate-500 hover:text-slate-800 focus:outline-none">
        <i class="fa-solid fa-bars-staggered text-2xl"></i>
    </button>
</div>

<aside id="sidebar" 
    class="fixed top-[73px] md:top-0 left-0 z-40 w-64 md:w-20 bg-white border-r border-slate-100 p-4 flex flex-col items-center transform -translate-x-full md:translate-x-0 md:relative transition-transform duration-300 ease-in-out h-[calc(100vh-73px)] md:h-screen md:min-h-screen md:sticky">
    <div class="mb-8 hidden md:flex items-center justify-center w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl shadow-sm">
        <i class="fa-solid fa-heart-pulse text-2xl"></i>
    </div>
    
    <nav class="space-y-3 flex-1 w-full md:flex md:flex-col md:items-center mt-4 md:mt-0">
        <a href="<?= BASE_URL ?>dashboard" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all" title="Inicio">
            <i class="fa-solid fa-chart-pie text-lg md:mr-0 mr-3"></i>
            <span class="md:hidden font-medium">Dashboard</span>
        </a>
        
        <a href="<?= BASE_URL ?>pacientes" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'pacientes') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all group" title="Pacientes">
            <i class="fa-solid fa-user-injured text-lg md:mr-0 mr-3 group-hover:scale-110 transition-transform"></i>
            <span class="md:hidden font-medium">Pacientes</span>
        </a>

        <a href="<?= BASE_URL ?>consultas" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'consultas') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all group" title="Consultas Médicas">
            <i class="fa-solid fa-notes-medical text-lg md:mr-0 mr-3 group-hover:scale-110 transition-transform"></i>
            <span class="md:hidden font-medium">Consultas</span>
        </a>

        <?php if(isset($role) && $role === 'admin'): ?>
            <a href="<?= BASE_URL ?>usuarios" class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'usuarios') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all group" title="Gestionar Usuarios">
                <i class="fa-solid fa-users-gear text-lg md:mr-0 mr-3 group-hover:scale-110 transition-transform"></i>
                <span class="md:hidden font-medium">Gestión Staff</span>
            </a>
            
            <a href="<?= BASE_URL ?>../app/controllers/reports_controller.php?action=index" 
                class="flex md:justify-center items-center p-3.5 rounded-2xl w-full md:w-12 <?= ($activePage === 'reportes') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-blue-600 hover:bg-slate-50' ?> transition-all group" 
                title="Reportes y Sistema">
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

<div id="sidebar-overlay" class="fixed inset-0 top-[73px] bg-slate-900/20 backdrop-blur-sm z-30 hidden md:hidden"></div>

<main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full max-w-[1600px] mx-auto">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div class="relative w-full sm:w-72 md:w-96 z-10">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input type="text" id="search-input" autocomplete="off" placeholder="Buscar pacientes..." 
                class="w-full pl-11 pr-4 py-2.5 bg-white rounded-full text-sm text-slate-700 border border-transparent focus:border-blue-200 focus:outline-none soft-shadow transition-all">
            
            <div id="search-results" class="absolute left-0 right-0 mt-2 bg-white rounded-2xl border border-slate-100 shadow-xl hidden z-20 overflow-hidden max-h-60 overflow-y-auto">
            </div>
        </div>
        
        <div class="relative self-end sm:self-auto" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" 
                class="flex items-center gap-3 bg-white pl-4 pr-2 py-1.5 rounded-full border border-slate-100 soft-shadow hover:bg-slate-50 transition-all outline-none group">
                <div class="text-right">
                    <p class="text-slate-800 font-bold text-sm leading-tight group-hover:text-blue-600 transition-colors">
                        <?= htmlspecialchars($username ?? 'Usuario') ?>
                    </p>
                    <span class="text-[9px] text-blue-500 font-bold uppercase tracking-wider">
                        <?= htmlspecialchars($role ?? 'invitado') ?>
                    </span>
                </div>
                <div class="h-9 w-9 bg-gradient-to-tr from-blue-500 to-cyan-400 rounded-full flex items-center justify-center text-white shadow-sm group-hover:rotate-12 transition-transform">
                    <i class="fa-solid fa-user-md"></i>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-300 transition-transform duration-300" :class="open ? 'rotate-180 text-blue-500' : ''"></i>
            </button>

            <div x-show="open" style="display: none;"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="absolute right-0 mt-3 w-52 bg-white rounded-[1.5rem] border border-slate-100 shadow-2xl z-[110] p-2">
                <a href="<?= BASE_URL ?>logout" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-rose-500 hover:bg-rose-50 rounded-xl transition-all group/logout">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center group-hover/logout:bg-rose-100 transition-colors">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </div>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>