<?php
/**
 * view/reports/index.php
 */
$pageTitle = "Centro de Reportes y Sistema | Clínica Pura";
$activePage = "reportes"; 

include_once VIEW_PATH . 'layout/header.php';
include_once VIEW_PATH . 'layout/nav.php';
?>

<div class="p-4 max-w-7xl mx-auto space-y-8">
    <div class="pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Centro de Sistema</h1>
            <p class="text-slate-500 text-sm">Generación de reportes, auditoría y respaldos de seguridad.</p>
        </div>
        
        <button onclick="toggleModal('historyModal', true)" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 px-5 py-2.5 rounded-2xl font-bold text-xs transition-all shadow-sm">
            <i class="fa-solid fa-clock-rotate-left text-blue-500"></i>
            Ver Historial de Actividad
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 soft-shadow group hover:border-blue-200 transition-all">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <i class="fa-solid fa-database text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Copia de Seguridad</h3>
            <p class="text-sm text-slate-500 mb-6">Genera un archivo .SQL con toda la información actual de la clínica.</p>
            <a href="<?= BASE_URL ?>../app/controllers/reports_controller.php?action=backup" 
               class="inline-flex items-center justify-center w-full py-3 bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs rounded-2xl transition-all shadow-lg shadow-slate-200">
                <i class="fa-solid fa-download mr-2"></i> Descargar SQL
            </a>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 soft-shadow opacity-75">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                <i class="fa-solid fa-file-import text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Restaurar Sistema</h3>
            <p class="text-sm text-slate-500 mb-6">Carga un archivo de respaldo previo para restaurar la base de datos.</p>
            
            <button disabled class="inline-flex items-center justify-center w-full py-3 bg-slate-100 text-slate-400 font-bold text-xs rounded-2xl cursor-not-allowed">
                Próximamente
            </button>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 soft-shadow opacity-75">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                <i class="fa-solid fa-file-pdf text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Reporte de Pacientes</h3>
            <p class="text-sm text-slate-500 mb-6">Resumen detallado de todos los pacientes en PDF.</p>
            <button disabled class="inline-flex items-center justify-center w-full py-3 bg-slate-100 text-slate-400 font-bold text-xs rounded-2xl cursor-not-allowed">
                Próximamente
            </button>
        </div>
    </div>
</div>

<div id="historyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-all duration-300">
    <div class="bg-white w-full max-w-3xl rounded-[2.5rem] shadow-2xl transform scale-95 transition-all duration-300 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h2 class="text-xl font-black text-slate-800 uppercase">Historial del Sistema</h2>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Log de Seguridad y Respaldos</p>
            </div>
            <button onclick="toggleModal('historyModal', false)" class="w-10 h-10 rounded-full bg-white shadow-sm text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-0 max-h-[50vh] overflow-y-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50">
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 text-[11px] uppercase tracking-wider font-bold">
                        <th class="px-8 py-4">Fecha</th>
                        <th class="px-4 py-4">Acción</th>
                        <th class="px-4 py-4">Archivo</th>
                        <th class="px-8 py-4">Usuario</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody" class="divide-y divide-slate-50">
                    <tr>
                        <td colspan="4" class="py-20 text-center text-slate-300 text-xs italic">Cargando registros...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="restoreModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-all duration-300">
    </div>

    <script src="<?= BASE_URL ?>js/reports.js"></script>

<?php include_once VIEW_PATH . 'layout/footer.php'; ?>