<?php
/**
 * view/layout/footer.php
 */
?>
    </main>

    <script>
        // ==========================================
        // 1. LÓGICA DEL MENÚ LATERAL INTERACTIVO
        // ==========================================
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleMenu() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        if (menuToggle && sidebar && overlay) {
            menuToggle.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);
        }

        // ==========================================
        // 2. MOTOR DE BÚSQUEDA ASÍNCRONA DE PACIENTES
        // ==========================================
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        let debounceTimer;

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    searchResults.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    // CORRECCIÓN MANTENIDA: Apunta al archivo unificado en controllers/
                    fetch(`../app/controllers/search_patients.php?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor');
                            }
                            return response.json();
                        })
                        .then(data => {
                            searchResults.innerHTML = '';

                            if (data.error || data.length === 0) {
                                searchResults.innerHTML = `
                                    <div class="p-4 text-center text-xs text-slate-400">
                                        <i class="fa-solid fa-user-slash mr-1"></i> No se encontraron pacientes
                                    </div>`;
                                searchResults.classList.remove('hidden');
                                return;
                            }

                            data.forEach(paciente => {
                                const iniciales = paciente.nombre ? paciente.nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'P';
                                const avatarBg = paciente.genero === 'Femenino' ? 'bg-pink-100 text-pink-600' : 'bg-blue-100 text-blue-600';

                                const item = document.createElement('div');
                                item.className = "flex items-center justify-between p-3 hover:bg-slate-50 border-b border-slate-50 last:border-0 transition-colors";
                                
                                item.innerHTML = `
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 ${avatarBg} rounded-full flex items-center justify-center font-bold text-xs">
                                            ${iniciales}
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-800">${paciente.nombre}</h4>
                                            <span class="text-[10px] text-slate-400 font-mono">DNI: ${paciente.dni}</span>
                                        </div>
                                    </div>
                                    <button data-id="${paciente.id}" data-nombre="${paciente.nombre}" 
                                        class="btn-seleccionar-paciente text-[11px] font-bold text-blue-600 hover:bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100 transition-all">
                                        Seleccionar
                                    </button>
                                `;
                                searchResults.appendChild(item);
                            });

                            searchResults.classList.remove('hidden');
                        })
                        .catch(err => {
                            console.error("Error en la petición de búsqueda:", err);
                        });
                }, 300);
            });

            searchResults.addEventListener('click', function(e) {
                const boton = e.target.closest('.btn-seleccionar-paciente');
                if (boton) {
                    const id = boton.getAttribute('data-id');
                    const nombre = boton.getAttribute('data-nombre');
                    
                    seleccionarPaciente({ id, nombre });
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        }

        // ==========================================
// 3. ACCIÓN AL SELECCIONAR UN PACIENTE
// ==========================================
function seleccionarPaciente(paciente) {
    // 1. Ocultar la lista flotante de resultados
    if (searchResults) {
        searchResults.classList.add('hidden');
    }
    
    // 2. Opcional: Rellenar el input de búsqueda con el nombre del paciente seleccionado
    if (searchInput) {
        searchInput.value = paciente.nombre;
    }

    // 3. REDIRECCIÓN AL CONTROLADOR: 
    // Como tu buscador corre en 'public/index.php', salimos con '../' e ingresamos a la carpeta de controladores.
    window.location.href = `../app/controllers/view_patient.php?id=${paciente.id}`;
}
    </script>
</body>
</html>