<?php
/**
 * view/patients/view_patient.php 
 * Rutas corregidas para apuntar a la carpeta PUBLIC
 */

// Aseguramos que las iniciales se calculen correctamente
$partesNombre = explode(' ', trim($paciente['nombre'] ?? 'P'));
$iniciales = strtoupper(substr($partesNombre[0], 0, 1) . (isset($partesNombre[1]) ? substr($partesNombre[1], 0, 1) : ''));

// IMPORTANTE: Definimos la ruta al listado en public
// Si $baseUrl no está definida, usamos la ruta relativa hacia atrás
$urlListado = (isset($baseUrl)) ? $baseUrl . "pacientes" : "../../public/pacientes";
?>

<div class="p-4 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 pb-5 border-b border-slate-100 gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="<?= (isset($baseUrl)) ? $baseUrl . "dashboard" : "../../public/dashboard" ?>" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-angle-right text-[9px]"></i>
                <a href="<?= $urlListado ?>" class="hover:text-blue-600 transition-colors">Pacientes</a>
                <i class="fa-solid fa-angle-right text-[9px]"></i>
                <span class="text-slate-600 font-medium">Expediente Clínico</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Expediente del Paciente</h1>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="<?= $urlListado ?>" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-100 rounded-full text-sm text-slate-600 font-semibold hover:bg-slate-50 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Volver al Listado
            </a>
            <button onclick="openEditModal()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 rounded-full text-sm text-white font-semibold hover:bg-blue-700 transition-all shadow-md shadow-blue-500/10">
                <i class="fa-solid fa-user-pen text-xs"></i>
                Editar Expediente
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-center relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-50/60 rounded-full"></div>
                <div class="relative z-10">
                    <div class="w-20 h-20 bg-gradient-to-tr from-blue-500 to-cyan-400 rounded-full flex items-center justify-center font-bold text-3xl text-white shadow-lg shadow-blue-500/10 mx-auto mb-4 border-4 border-white">
                        <?= htmlspecialchars($iniciales) ?>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 leading-tight mb-2"><?= htmlspecialchars($paciente['nombre']) ?></h2>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 rounded-full text-xs text-slate-500 font-medium border border-slate-100">
                        <i class="fa-solid fa-id-card text-blue-500"></i> DNI: <?= htmlspecialchars($paciente['dni']) ?>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-blue-500"></i> Contacto
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100/50">
                        <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center text-blue-600 shadow-sm border border-slate-100 text-sm"><i class="fa-solid fa-phone"></i></div>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-wider">Teléfono</span>
                            <p class="text-xs font-semibold text-slate-800"><?= htmlspecialchars($paciente['telefono'] ?? 'No registrado') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm min-h-[380px]">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5 mb-6 pb-4 border-b border-slate-100">
                    <i class="fa-solid fa-notes-medical text-blue-500"></i> Evolución Clínica
                </h3>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 shadow-inner">
                    <p class="text-slate-700 text-sm whitespace-pre-wrap"><?= htmlspecialchars($paciente['historial'] ?? 'Sin antecedentes') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-bold text-slate-800">Editar Expediente</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="" method="POST" class="p-8 space-y-6">
            <input type="hidden" name="action" value="update">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($paciente['nombre']) ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">DNI</label>
                    <input type="text" name="dni" value="<?= htmlspecialchars($paciente['dni']) ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none focus:border-blue-500 transition-all">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Historial</label>
                <textarea name="historial" rows="5" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none focus:border-blue-500 transition-all"><?= htmlspecialchars($paciente['historial']) ?></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="px-6 py-2 text-sm text-slate-500 font-semibold">Cancelar</button>
                <button type="submit" class="px-8 py-2.5 bg-blue-600 rounded-full text-sm text-white font-bold shadow-lg shadow-blue-500/20">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal() { document.getElementById('editModal').classList.remove('hidden'); }
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>