<?php

require_once __DIR__ . '/../../app/config/app.php';
checkAuth();
/**
 * view/patients/index.php
 * Vista modularizada para el listado principal y mantenimiento de pacientes
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CONFIGURACIÓN DE RUTA ABSOLUTA UNIFICADA
// Esto garantiza que el nav.php sepa exactamente dónde está parado
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$baseUrl = $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/";

// Variables requeridas por el Layout global
$activePage = 'pacientes'; 
$username = $_SESSION['username'] ?? 'Usuario';
$role = $_SESSION['role'] ?? 'invitado';

// Nota: Los datos de $patients deben venir cargados desde el controlador o enrutador anterior

// 2. Incluir la estructura superior del Layout global
include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php'; // Ahora leerá el $baseUrl correcto con /public/
?>

<div class="p-4 max-w-7xl mx-auto flex-1">
    
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-5 border-b border-slate-100 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Gestión de Pacientes</h1>
            <p class="text-slate-500 text-sm">Administración y seguimiento detallado de expedientes de pacientes.</p>
        </div>
        
        <button onclick="toggleModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full shadow-md shadow-blue-500/10 transition flex items-center font-semibold text-sm">
            <i class="fa-solid fa-plus mr-2 text-xs"></i> Nuevo Paciente
        </button>
    </header>

    <?php if(isset($_GET['status']) && !in_array($_GET['status'], ['success', 'updated', 'deleted'])): ?>
        <?php 
            $status = $_GET['status'];
            // Si llegamos aquí, es porque hubo un error (no es success, ni updated, ni deleted)
            $message = 'Hubo un error al procesar la solicitud.';
            
            // Aquí puedes agregar validaciones de errores específicos que vengan por URL
            if($status == 'error_db') $message = 'Error: Ya existe un registro con esos datos.';
            if($status == 'invalid') $message = 'Error: Datos mal ingresados o incompletos.';
        ?>
        
        <div class="bg-red-50 text-red-700 border-red-100 p-4 rounded-2xl mb-6 flex items-center border shadow-sm text-sm">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mr-3 text-lg"></i>
            <span class="font-semibold"><?= htmlspecialchars($message) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/50">
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 text-[11px] uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">Nombre Completo</th>
                        <th class="px-6 py-4">DNI / Identificación</th>
                        <th class="px-6 py-4">Teléfono</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (!empty($patients)): ?>
                        <?php foreach ($patients as $p): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 leading-snug"><?= htmlspecialchars($p['nombre']) ?></p>
                                
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs font-semibold">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-md text-slate-700 border border-slate-200/40">
                                    <?= htmlspecialchars($p['dni']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                <?= !empty($p['telefono']) ? htmlspecialchars($p['telefono']) : '<span class="text-slate-400 italic text-xs">No provisto</span>' ?>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                <?= !empty($p['email']) ? htmlspecialchars($p['email']) : '<span class="text-slate-400 italic text-xs">No provisto</span>' ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="../app/controllers/view_patient.php?id=<?= $p['id'] ?>" 
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" 
                                    title="Ver Expediente Médico">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>

                                    <button onclick='abrirModalEditar(<?= json_encode($p) ?>)' 
                                            class="p-2 text-sky-500 hover:bg-sky-50 rounded-xl transition-all" 
                                            title="Editar Información">
                                        <i class="fa-solid fa-user-pen text-sm"></i>
                                    </button>

                                    <button onclick="confirmarEliminar(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre']) ?>')" 
                                            class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" 
                                            title="Eliminar del Sistema">
                                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center text-slate-400">
                                <i class="fa-solid fa-user-slash text-4xl mb-4 block text-slate-200"></i>
                                <h3 class="text-sm font-bold text-slate-700 mb-0.5">Base de datos vacía</h3>
                                <p class="text-xs text-slate-400">No se encontraron pacientes registrados en la aplicación.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                            </table>
        </div>
    </div>
</div>

<div id="modalPaciente" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm invisible opacity-0 transition-all duration-300">
    
    <div class="bg-white w-full max-w-md rounded-[2.5rem] border border-slate-100 shadow-2xl transform scale-95 transition-transform duration-300 p-8 relative">
        
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-user-plus text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Nuevo Paciente</h3>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Registro de ingreso</p>
                </div>
            </div>
            <button type="button" onclick="toggleModal()" class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-all hover:rotate-90">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="pacientes/store" method="POST" class="space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Nombre Completo</label>
                <input type="text" name="nombre" required placeholder="Ej: Juan Pérez"
                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">DNI / Cédula</label>
                    <input type="text" name="dni" required 
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Teléfono</label>
                    <input type="text" name="telefono" 
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Correo Electrónico</label>
                <input type="email" name="email" placeholder="ejemplo@correo.com"
                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Historial / Observaciones</label>
                <textarea name="historial" rows="2" 
                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none resize-none"></textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="toggleModal()" class="flex-1 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition-all uppercase tracking-widest">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-blue-500/20 transition-all uppercase tracking-widest">
                    Guardar Registro
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditar" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900">Modificar Datos del Paciente</h3>
            <button onclick="cerrarModal()" class="text-slate-400 hover:text-slate-600 transition-colors text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="pacientes/update" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id" id="edit_id">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nombre Completo</label>
                    <input type="text" name="nombre" id="edit_nombre" required class="w-full px-4 py-2.5 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">DNI / Documento</label>
                    <input type="text" name="dni" id="edit_dni" required class="w-full px-4 py-2.5 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Teléfono / Móvil</label>
                    <input type="text" name="telefono" id="edit_telefono" class="w-full px-4 py-2.5 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                    <input type="email" name="email" id="edit_email" class="w-full px-4 py-2.5 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none border-slate-200 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Evolución Clínica / Notas</label>
                <textarea name="historial" id="edit_historial" rows="3" class="w-full px-4 py-2.5 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none border-slate-200 text-sm resize-none"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="cerrarModal()" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-50 rounded-full transition-all">Cancelar</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-full hover:bg-blue-700 shadow-md shadow-blue-500/10 transition-all">Actualizar Registro</button>
            </div>
        </form>
    </div>
</div>

   <script src="<?= BASE_URL ?>js/patients.js"></script>

<?php 
// 3. Incluir la estructura de scripts y cierre del Layout
include __DIR__ . '/../layout/footer.php'; 
?>