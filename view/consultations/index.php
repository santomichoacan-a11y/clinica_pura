<?php
$pageTitle = "Consultas Médicas | Clínica Pura";
$activePage = "consultas"; 
$ocultar_buscador = true; 
include_once VIEW_PATH . 'layout/header.php';
include_once VIEW_PATH . 'layout/nav.php';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
    .swal2-popup { border-radius: 2.5rem !important; padding: 2rem !important; }
    .swal2-confirm { border-radius: 99px !important; padding: 0.75rem 2rem !important; font-weight: 700 !important; }
    .swal2-cancel { border-radius: 99px !important; padding: 0.75rem 2rem !important; font-weight: 700 !important; }

    /* Ajuste de bordes para que combine con tu diseño */
    .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0 !important; /* slate-200 */
        border-radius: 1rem !important; /* 2xl */
        height: 48px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 10px !important;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
    }
</style>

<div x-data="{ 
    openModal: false, 
    editMode: false,
    formData: { id: '', patient_id: '', reason: '', diagnosis: '' },
    resetForm() {
        this.editMode = false;
        this.formData = { id: '', patient_id: '', reason: '', diagnosis: '' };
    },
    editConsultation(c) {
        this.editMode = true;
        // Mapeamos exactamente lo que viene de la DB al objeto de Alpine
        this.formData = { 
            id: c.id, 
            patient_id: c.patient_id, 
            reason: c.reason, 
            diagnosis: c.diagnosis 
        };
        this.openModal = true;
    }
}" class="space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Gestión de Consultas</h1>
            <p class="text-slate-500 text-sm">Registro y consulta del histórico de atenciones médicas.</p>
        </div>

        <button @click="resetForm(); openModal = true" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full shadow-md shadow-blue-500/10 transition flex items-center font-semibold text-sm">
            <i class="fa-solid fa-plus mr-2 text-xs"></i> Nueva Consulta
        </button>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 soft-shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50">
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Fecha / Hora</th>
                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Paciente</th>
                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Motivo</th>
                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (!empty($consultations)): ?>
                    <?php foreach($consultations as $c): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5 text-sm text-slate-500 font-semibold"><?= date('d/m/Y h:i A', strtotime($c['created_at'])) ?></td>
                        <td class="px-8 py-5 text-sm text-slate-800 font-bold"><?= htmlspecialchars($c['paciente_nombre']) ?></td>
                        <td class="px-8 py-5 text-sm text-slate-600 truncate max-w-xs"><?= htmlspecialchars($c['reason']) ?></td>
                        <td class="px-8 py-5 text-right space-x-2">
                            <button @click="editConsultation(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)" class="text-blue-400 hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button onclick="confirmarEliminar(<?= $c['id'] ?>)" class="text-red-400 hover:text-red-600 transition-colors">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="px-8 py-16 text-center text-slate-400 font-medium">No se han registrado consultas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div x-show="openModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center p-4">
        <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openModal = false"></div>
        <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white w-full max-w-md rounded-[2.5rem] border border-slate-100 shadow-2xl relative z-10 p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                        <i :class="editMode ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-notes-medical'" class="text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900" x-text="editMode ? 'Editar Consulta' : 'Nueva Consulta'"></h3>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold" x-text="editMode ? 'Modificar registro' : 'Registro de ingreso'"></p>
                    </div>
                </div>
                <button type="button" @click="openModal = false" class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:rotate-90 transition-all"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="<?= BASE_URL ?>consultas/guardar" method="POST" class="space-y-4">
                <input type="hidden" name="id" x-model="formData.id">

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Paciente</label>
                    <select name="patient_id" id="select-paciente" x-model="formData.patient_id" required 
                            class="select2-custom w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium transition-all outline-none bg-white">
                        <option value="">Seleccione un paciente...</option>
                        <?php foreach($patients as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Motivo</label>
                    <textarea name="reason" x-model="formData.reason" required rows="2" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Observaciones / Diagnóstico</label>
                    <textarea name="diagnosis" x-model="formData.diagnosis" rows="2" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none resize-none"></textarea>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" @click="openModal = false" class="flex-1 py-3.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-2xl uppercase tracking-widest">Cancelar</button>
                    <button type="submit" class="flex-1 py-3.5 bg-blue-600 text-white font-bold text-xs rounded-2xl shadow-lg shadow-blue-500/20 uppercase tracking-widest" x-text="editMode ? 'Actualizar' : 'Guardar Registro'"></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Eliminar consulta?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444', 
        cancelButtonColor: '#94a3b8',  
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-[2.5rem]',
            confirmButton: 'rounded-2xl px-6 py-3 font-bold',
            cancelButton: 'rounded-2xl px-6 py-3 font-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Usamos la ruta amigable que coincide con tu CASE 'consultas/eliminar'
            // El ID se pasa como parámetro GET tradicional
            window.location.href = "<?= BASE_URL ?>consultas/eliminar?id=" + id;
        }
    });
}

    document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    let config = null;

    if (urlParams.has('success')) {
        config = { icon: 'success', title: '¡Registro Exitoso!', text: 'La consulta se guardó correctamente.' };
    } else if (urlParams.has('updated')) {
        config = { icon: 'success', title: '¡Cambios Guardados!', text: 'La información ha sido actualizada.' };
    } else if (urlParams.has('deleted')) {
        config = { icon: 'success', title: 'Registro Eliminado', text: 'La consulta fue removida.', iconColor: '#ef4444' };
    }

    if (config) {
        Swal.fire({
            ...config,
            showConfirmButton: false, // Sin botón OK
            timer: 1000,              // Desaparece en 1.8 segundos
            timerProgressBar: false,  // Sin barra de carga
            customClass: {
                popup: 'rounded-[2.5rem]'
            }
        }).then(() => {
            // Limpia la URL inmediatamente después de cerrarse
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
});
</script>
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('#select-paciente').select2({
        placeholder: "Buscar paciente por nombre...",
        allowClear: true,
        width: '100%'
    });

    // Sincronizar Select2 con Alpine.js
    $('#select-paciente').on('change', function (e) {
        // Obtenemos el componente de Alpine y actualizamos su formData
        const val = $(this).val();
        const alpineData = document.querySelector('[x-data]').__x.$data;
        alpineData.formData.patient_id = val;
    });

    // Resetear Select2 cuando se abre el modal para "Nueva Consulta"
    // (Opcional: Si tienes un botón que abre el modal, puedes llamar esto)
    window.addEventListener('reset-select', () => {
        $('#select-paciente').val(null).trigger('change');
    });
});
</script>

<?php include_once VIEW_PATH . 'layout/footer.php'; ?>