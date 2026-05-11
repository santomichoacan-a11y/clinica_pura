/**
 * logic: public/js/reports.js
 * Gestión de respaldos y logs del sistema
 */

/**
 * Control dinámico de modales con carga AJAX
 */
async function toggleModal(id, show) {
    const m = document.getElementById(id);
    if (!m) return;
    
    const content = m.querySelector('div');

    if (show) {
        // Cargar historial solo si se abre ese modal
        if (id === 'historyModal') {
            await cargarHistorial();
        }

        m.classList.remove('hidden');
        setTimeout(() => {
            m.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    } else {
        m.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => m.classList.add('hidden'), 300);
    }
}

/**
 * Petición AJAX para obtener el historial en tiempo real
 */
async function cargarHistorial() {
    const tbody = document.getElementById('historyTableBody');
    if (!tbody) return;

    try {
        // Ajustamos la ruta. Como el JS se ejecuta desde el navegador, 
        // usamos una ruta relativa al controlador o la variable global si la tienes.
        const response = await fetch(BASE_URL + 'app/controllers/reports_controller.php?action=get_history');
        const data = await response.json();

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="py-20 text-center text-slate-300 text-xs italic">No hay registros de actividad.</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(log => `
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-8 py-4 text-xs text-slate-500">${log.created_at}</td>
                <td class="px-4 py-4">
                    <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase ${log.action_type === 'backup' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600'}">
                        ${log.action_type}
                    </span>
                </td>
                <td class="px-4 py-4 text-[11px] font-mono text-slate-400">${log.file_name}</td>
                <td class="px-8 py-4 text-sm font-bold text-slate-700">${log.username}</td>
            </tr>
        `).join('');

    } catch (error) {
        console.error('Error al cargar historial:', error);
        tbody.innerHTML = '<tr><td colspan="4" class="py-10 text-center text-rose-400 text-xs">Error al conectar con el servidor.</td></tr>';
    }
}

// Funciones para el Modal de Restaurar (Feedback visual)
function updateFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    if (input.files && input.files[0]) {
        display.innerText = input.files[0].name;
        display.classList.add('text-emerald-600');
    }
}

function confirmarRestauracion(form) {
    if (!confirm('¿Estás absolutamente seguro? Los datos actuales se perderán permanentemente.')) {
        return false;
    }
    const btn = document.getElementById('btnRestore');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Restaurando...';
    return true;
}