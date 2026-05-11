/**
 * logic: public/js/patients.js
 * Gestión de expedientes y alertas de pacientes
 */

// 1. Objeto Global de Alertas (SweetAlert2)
const PuraAlert = {
    confirmDelete: function(nombre, callback) {
        Swal.fire({
            title: '¿Confirmar eliminación?',
            html: `Estás a punto de eliminar a <b>${nombre}</b>.<br><span style="color: #ef4444; font-size: 0.8rem;">Esta acción no se puede deshacer.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-2"></i> Eliminar ahora',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'rounded-[2rem] border-none',
                confirmButton: 'rounded-full px-6 py-3 font-bold',
                cancelButton: 'rounded-full px-6 py-3 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) callback();
        });
    },

    confirmUpdate: function(callback) {
        Swal.fire({
            title: '¿Actualizar información?',
            text: "Los cambios se guardarán en el expediente del paciente.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, guardar cambios',
            cancelButtonText: 'Seguir editando',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[2rem] border-none',
                confirmButton: 'rounded-full px-6 py-3 font-bold',
                cancelButton: 'rounded-full px-6 py-3 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) callback();
        });
    },

    success: function(msg) {
        Swal.fire({
            icon: 'success',
            title: '¡Completado!',
            text: msg,
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-[2rem]' }
        });
    }
};
function toggleModal() {
    const modal = document.getElementById('modalPaciente');
    if (modal) {
        // En lugar de hidden, alternamos visibilidad y opacidad
        modal.classList.toggle('invisible');
        modal.classList.toggle('opacity-100');
        
        // Efecto opcional de escala en el hijo (el div blanco)
        const content = modal.querySelector('div');
        if(content) {
            content.classList.toggle('scale-100');
            content.classList.toggle('scale-95');
        }
    }
}

function abrirModalEditar(paciente) {
    const modal = document.getElementById('modalEditar');
    if (!modal) return;

    // Rellenar campos
    document.getElementById('edit_id').value = paciente.id || '';
    document.getElementById('edit_nombre').value = paciente.nombre || '';
    document.getElementById('edit_dni').value = paciente.dni || '';
    document.getElementById('edit_telefono').value = paciente.telefono || '';
    document.getElementById('edit_email').value = paciente.email || '';
    document.getElementById('edit_historial').value = paciente.historial || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModal() {
    const modal = document.getElementById('modalEditar');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

function confirmarEliminar(id, nombre) {
    PuraAlert.confirmDelete(nombre, () => {
        // Ajusta esta ruta según tu controlador de pacientes
        window.location.href = `pacientes/delete?id=${id}`;
    });
}

// 3. Inicialización de Eventos
document.addEventListener('DOMContentLoaded', () => {
    
    // Interceptar formulario de edición
    const formEditar = document.querySelector('#modalEditar form');
    if(formEditar) {
        formEditar.addEventListener('submit', function(e) {
            e.preventDefault();
            PuraAlert.confirmUpdate(() => {
                formEditar.submit();
            });
        });
    }

    // Cerrar modales al hacer clic fuera
    window.onclick = function(event) {
        const modalEditar = document.getElementById('modalEditar');
        const modalPaciente = document.getElementById('modalPaciente');
        if (event.target == modalEditar) cerrarModal();
        if (event.target == modalPaciente) toggleModal();
    };

    // Manejo de notificaciones por URL Status
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
        const status = urlParams.get('status');
        const successCases = {
            'success': 'Paciente registrado correctamente.',
            'updated': 'Datos actualizados con éxito.',
            'deleted': 'El expediente ha sido eliminado.'
        };

        if (successCases[status]) {
            PuraAlert.success(successCases[status]);
            // Limpiar URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
});