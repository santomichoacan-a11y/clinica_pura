<?php
/**
 * view/patients/index.php
 */

// 1. Cabeceras de seguridad y control de caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Variables de sesión para el sidebar y header
$username = $_SESSION['username'] ?? 'Usuario';
$role = $_SESSION['role'] ?? 'invitado';

// 3. Los datos de $patients vienen del PatientController
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes | Clínica Pura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <!-- Sidebar (Mismo estilo que el Dashboard) -->
    <aside class="w-64 bg-slate-900 text-white p-6 shadow-xl flex flex-col">
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-blue-400 flex items-center">
                <i class="fa-solid fa-house-medical mr-3"></i> Clínica Pura
            </h2>
        </div>
        
        <nav class="space-y-2 flex-1">
            <a href="dashboard" class="flex items-center py-3 px-4 rounded-lg transition hover:bg-slate-800 text-slate-300">
                <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
            </a>
            
            <a href="pacientes" class="flex items-center py-3 px-4 rounded-lg transition bg-blue-600 shadow-lg shadow-blue-900/20">
                <i class="fa-solid fa-user-injured mr-3"></i> Pacientes
            </a>
            <!-- ... otros links ... -->
        </nav>

        <div class="pt-6 border-t border-slate-800">
            <a href="logout" class="flex items-center py-3 px-4 rounded-lg transition text-red-400 hover:bg-red-900/20">
                <i class="fa-solid fa-right-from-bracket mr-3"></i> Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestión de Pacientes</h1>
                <p class="text-slate-500 text-sm">Listado y registro de pacientes activos</p>
            </div>
            
            <!-- Botón para registrar (podrías abrir un modal aquí) -->
            <button onclick="toggleModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg transition flex items-center font-semibold">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Paciente
            </button>
        </header>

        <!-- Mensajes de estado -->
            <?php if(isset($_GET['status'])): ?>
                <?php 
                    $status = $_GET['status'];
                    // Definimos los estados que consideramos "Exitosos"
                    $isSuccess = in_array($status, ['success', 'updated', 'deleted']);
                    
                    // Asignamos el mensaje según el caso
                    $message = 'Hubo un error al procesar la solicitud.';
                    if($status == 'success') $message = 'Paciente registrado correctamente.';
                    if($status == 'updated') $message = 'Paciente actualizado con éxito.';
                    if($status == 'deleted') $message = 'Paciente eliminado correctamente.';
                ?>
                
                <div class="<?= $isSuccess ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-red-100 text-red-700 border-red-200' ?> p-4 rounded-xl mb-6 flex items-center border shadow-sm">
                    <i class="fa-solid <?= $isSuccess ? 'fa-circle-check' : 'fa-triangle-exclamation' ?> mr-3 text-lg"></i>
                    <span class="font-medium"><?= $message ?></span>
                </div>
            <?php endif; ?>

        <!-- Tabla de Pacientes -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold">Nombre</th>
                        <th class="px-6 py-4 font-bold">DNI</th>
                        <th class="px-6 py-4 font-bold">Teléfono</th>
                        <th class="px-6 py-4 font-bold">Email</th>
                        <th class="px-6 py-4 font-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($patients)): ?>
                        <?php foreach ($patients as $p): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800"><?= htmlspecialchars($p['nombre']) ?></p>
                                <p class="text-[10px] text-slate-400">ID: #<?= $p['id'] ?></p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm font-medium"><?= htmlspecialchars($p['dni']) ?></td>
                            <td class="px-6 py-4 text-slate-600 text-sm italic"><?= htmlspecialchars($p['telefono']) ?></td>
                            <td class="px-6 py-4 text-slate-600 text-sm"><?= htmlspecialchars($p['email']) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <!-- BOTÓN VER HISTORIAL -->
                                    <a href="pacientes/ver?id=<?= $p['id'] ?>" 
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                    title="Ver Historial">
                                        <i class="fa-solid fa-notes-medical"></i>
                                    </a>

                                    <button onclick='abrirModalEditar(<?= json_encode($p) ?>)' 
                                            class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition" 
                                            title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- BOTÓN ELIMINAR -->
                                    <button onclick="confirmarEliminar(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre']) ?>')" 
                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" 
                                            title="Eliminar">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center text-slate-400">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 block"></i>
                                No se encontraron pacientes registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- Modal para Nuevo Paciente -->
    <div id="modalPaciente" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200">
            <div class="bg-slate-900 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold font-sans">Registrar Nuevo Paciente</h3>
                <button onclick="toggleModal()" class="text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>
            
            <form action="pacientes/store" method="POST" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre Completo</label>
                        <input type="text" name="nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">DNI / Cédula</label>
                        <input type="text" name="dni" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono</label>
                        <input type="text" name="telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Correo Electrónico</label>
                    <input type="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Historial / Observaciones</label>
                    <textarea name="historial" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition resize-none"></textarea>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="toggleModal()" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 transition">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition">Guardar Paciente</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edición -->
<div id="modalEditar" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden border border-slate-200">
        <!-- Cabecera -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Editar Paciente</h3>
            <button onclick="cerrarModal()" class="text-slate-400 hover:text-slate-600 transition text-2xl">&times;</button>
        </div>
        
        <!-- Formulario -->
        <form action="pacientes/update" method="POST" class="p-6">
            <input type="hidden" name="id" id="edit_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Nombre</label>
                    <input type="text" name="nombre" id="edit_nombre" required
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">DNI</label>
                    <input type="text" name="dni" id="edit_dni" required
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Email</label>
                    <input type="email" name="email" id="edit_email"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Historial</label>
                <textarea name="historial" id="edit_historial" rows="3"
                          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal()" class="px-4 py-2 text-slate-600 font-medium hover:bg-slate-100 rounded-lg transition">Cancelar</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md transition">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('modalPaciente');
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
</script>
</body>
</html>
<script>
/**
 * Función para confirmar la eliminación de un paciente
 * @param {number} id - ID del paciente
 * @param {string} nombre - Nombre del paciente para mostrar en la alerta
 */
function confirmarEliminar(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar al paciente "${nombre}"? Esta acción no se puede deshacer.`)) {
        // Redirige a la ruta de borrado configurada en el controlador
        window.location.href = `pacientes/delete?id=${id}`;
    }
}


function abrirModalEditar(paciente) {
    // Rellenar los campos del modal con los datos del objeto
    document.getElementById('edit_id').value = paciente.id;
    document.getElementById('edit_nombre').value = paciente.nombre;
    document.getElementById('edit_dni').value = paciente.dni;
    document.getElementById('edit_telefono').value = paciente.telefono;
    document.getElementById('edit_email').value = paciente.email;
    document.getElementById('edit_historial').value = paciente.historial;

    // Mostrar el modal
    const modal = document.getElementById('modalEditar');
    modal.classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalEditar').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('modalEditar');
    if (event.target == modal) {
        cerrarModal();
    }
}
</script>

