<?php

$partesNombre = explode(' ', trim($paciente['nombre']));
$iniciales = strtoupper(substr($partesNombre[0], 0, 1) . (isset($partesNombre[1]) ? substr($partesNombre[1], 0, 1) : ''));
?>

<div class="p-4 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 pb-5 border-b border-slate-100 gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="<?= $baseUrl ?>dashboard" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-angle-right text-[9px]"></i>
                
                <a href="<?= $baseUrl ?>pacientes" class="hover:text-blue-600 transition-colors">Pacientes</a>
                <i class="fa-solid fa-angle-right text-[9px]"></i>
                
                <span class="text-slate-600 font-medium">Expediente Clínico</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Expediente del Paciente</h1>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="<?= $baseUrl ?>pacientes" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-100 rounded-full text-sm text-slate-600 font-semibold hover:bg-slate-50 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Volver al Listado
            </a>
            <button class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 rounded-full text-sm text-white font-semibold hover:bg-blue-700 transition-all shadow-md shadow-blue-500/10">
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
                        <i class="fa-solid fa-id-card text-blue-500"></i>
                        DNI: <?= htmlspecialchars($paciente['dni']) ?>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-blue-500"></i>
                    Información de Contacto
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100/50">
                        <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center text-blue-600 shadow-sm border border-slate-100 text-sm">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-wider">Teléfono / Celular</span>
                            <p class="text-xs font-semibold text-slate-800 truncate">
                                <?= !empty($paciente['telefono']) ? htmlspecialchars($paciente['telefono']) : '<span class="text-slate-400 font-normal italic">No registrado</span>' ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100/50">
                        <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center text-blue-600 shadow-sm border border-slate-100 text-sm">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-wider">Correo Electrónico</span>
                            <p class="text-xs font-semibold text-slate-800 truncate">
                                <?= !empty($paciente['email']) ? htmlspecialchars($paciente['email']) : '<span class="text-slate-400 font-normal italic">No registrado</span>' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-2">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm min-h-[380px]">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                        <i class="fa-solid fa-notes-medical text-blue-500"></i>
                        Evolución Clínica / Diagnósticos
                    </h3>
                </div>
                
                <?php if (!empty($paciente['historial'])): ?>
                    <div class="relative pl-6 before:absolute before:inset-y-0 before:left-0 before:w-0.5 before:bg-slate-100">
                        <div class="relative">
                            <div class="absolute -left-[27px] top-1.5 w-3 h-3 bg-blue-500 rounded-full ring-4 ring-blue-50"></div>
                            
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 shadow-inner">
                                <p class="text-slate-700 leading-relaxed text-sm whitespace-pre-wrap font-sans"><?= htmlspecialchars($paciente['historial']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 border-2 border-dashed border-slate-100 rounded-2xl bg-slate-50/50">
                        <i class="fa-solid fa-comment-medical text-5xl text-slate-200 mb-4 block"></i>
                        <h4 class="text-sm font-bold text-slate-700 mb-1">Sin antecedentes registrados</h4>
                        <p class="text-xs text-slate-400 max-w-xs mx-auto">Este expediente aún no cuenta con anotaciones en el historial clínico.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>