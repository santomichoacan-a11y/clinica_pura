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
            if (sidebar && overlay) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }

        if (menuToggle && sidebar && overlay) {
            menuToggle.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);
        }

        // ==========================================
        // 2. MOTOR DE BÚSQUEDA ASÍNCRONA DE PACIENTES
        // ==========================================
        (function() {
            // Identificamos los elementos
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            
            // --- BLOQUEO DE VISTAS ESPECÍFICAS ---
            // Obtenemos la URL completa y la pasamos a minúsculas
            const currentUrl = window.location.href.toLowerCase();
            
            // Definimos las palabras clave que disparan el bloqueo
            const isRestricted = currentUrl.includes('/consultas') || 
                                 currentUrl.includes('/usuarios') || 
                                currentUrl.includes('/reports') ||
                                currentUrl.includes('view_patient') || 
                                currentUrl.includes('/patients/view');

            // Si no hay input o estamos en vista restringida, salimos
            if (!searchInput || !searchResults || isRestricted) {
                // Si existe el input pero la vista es restringida, lo desactivamos visualmente
                if (searchInput && isRestricted) {
                    searchInput.disabled = true;
                    searchInput.placeholder = "Buscador no disponible aquí";
                    searchInput.classList.add('opacity-50', 'cursor-not-allowed');
                }
                return; 
            }

            let debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    searchResults.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    // Ruta relativa mantenida de tu código original
                    fetch(`../app/controllers/search_patients.php?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Error en servidor');
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
                                <button onclick="seleccionarPaciente('${paciente.id}', '${paciente.nombre}')" 
                                    class="text-[11px] font-bold text-blue-600 border border-blue-100 px-4 py-2 rounded-xl transition-all hover:bg-blue-600 hover:text-white hover:border-blue-600">
                                    <i class="fa-solid fa-eye mr-1"></i> Ver Perfil Detallado
                                </button>
                            `;
                            searchResults.appendChild(item);
                            });

                            searchResults.classList.remove('hidden');
                        })
                        .catch(err => console.error("Error en búsqueda:", err));
                }, 300);
            });

            // Cerrar resultados al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        })();

        // ==========================================
        // 3. FUNCIÓN DE REDIRECCIÓN (Global)
        // ==========================================
        function seleccionarPaciente(id, nombre) {
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            
            if (searchResults) searchResults.classList.add('hidden');
            if (searchInput) searchInput.value = nombre;

            // Redirección al controlador de vista de paciente
            window.location.href = `../app/controllers/view_patient.php?id=${id}`;
        }
    </script>
</body>
</html>