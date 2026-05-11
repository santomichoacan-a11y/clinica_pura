<?php
/**
 * view/dashboard/index.php
 */

// Llamamos al controlador que contiene toda la lógica de consultas y variables
require_once __DIR__ . '/../../app/controllers/dashboard_controller.php';

// Si NO es una petición AJAX, incluimos el layout base
if (!$isAjax) {
    include_once VIEW_PATH . 'layout/header.php';
    include_once VIEW_PATH . 'layout/nav.php';
}
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
    
    <div class="lg:col-span-5 space-y-8">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4">Actividad</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-blue-600 to-blue-500 p-5 rounded-3xl text-white shadow-xl shadow-blue-500/10 relative overflow-hidden">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-hospital-user text-sm"></i>
                    </div>
                    <h3 class="text-3xl font-bold tracking-tight"><?= number_format($totalReal) ?></h3>
                    <p class="text-blue-100/80 text-xs font-medium mt-1">Pacientes Totales</p>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-teal-400 p-5 rounded-3xl text-white shadow-xl shadow-emerald-500/10 relative overflow-hidden">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-notes-medical text-sm"></i>
                    </div>
                    <h3 class="text-3xl font-bold tracking-tight"><?= number_format($totalConsultas) ?></h3>
                    <p class="text-emerald-100/80 text-xs font-medium mt-1">Consultas Realizadas</p>
                </div>

                <div class="bg-gradient-to-br from-indigo-600 to-purple-500 p-5 rounded-3xl text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-user-gear text-sm"></i>
                    </div>
                    <h3 class="text-3xl font-bold tracking-tight"><?= number_format($totalUsuarios) ?></h3>
                    <p class="text-indigo-100/80 text-xs font-medium mt-1">Usuarios Activos</p>
                </div>

                <div class="bg-gradient-to-br from-cyan-500 to-blue-400 p-5 rounded-3xl text-white shadow-xl shadow-cyan-500/10 relative overflow-hidden">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-calendar-day text-sm"></i>
                    </div>
                    <h3 class="text-3xl font-bold tracking-tight"><?= $consultasHoy ?></h3>
                    <p class="text-cyan-100/80 text-xs font-medium mt-1">Consultas de Hoy</p>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-7">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 soft-shadow h-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-slate-100 gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Estadísticas Clínicas</h2>
                    <p class="text-xs text-slate-400">Flujo semanal de pacientes ingresados</p>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider">
                    <?= date('d') . ' ' . date('M') . ' ' . date('Y') ?>
                </span>
            </div>
            
            <div class="h-48 w-full flex items-end justify-between px-2 pt-8 relative gap-2 sm:gap-4">
                <div class="absolute inset-x-0 bottom-0 top-8 border-b border-dashed border-slate-100 flex flex-col justify-between pointer-events-none">
                    <div class="w-full border-t border-dashed border-slate-100/70"></div>
                    <div class="w-full border-t border-dashed border-slate-100/70"></div>
                    <div class="w-full border-t border-dashed border-slate-100/70"></div>
                </div>
                
                <?php foreach($flujoSemanalProcesado as $dia => $data): 
                    $alturaEstetica = $data['porcentaje'];
                    if ($data['conteo'] > 0 && $alturaEstetica < 10) { $alturaEstetica = 10; } 
                ?>
                    <div class="flex flex-col items-center flex-1 h-full justify-end group relative z-10 max-w-[48px]">
                        <?php if($data['es_hoy']): ?>
                            <div class="w-full bg-gradient-to-t from-blue-600 to-cyan-400 rounded-t-xl relative transition-all duration-300 shadow-md shadow-blue-500/10" 
                                 style="height: <?= $alturaEstetica ?>%;">
                                <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-lg shadow-lg z-20 whitespace-nowrap">
                                    <?= $data['conteo'] ?> Pac. (Hoy)
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="w-full bg-slate-100 hover:bg-blue-100 rounded-t-xl transition-all duration-200 cursor-pointer relative" 
                                 style="height: <?= $alturaEstetica > 0 ? $alturaEstetica : 4 ?>%;"> 
                                <div class="absolute -top-7 left-1/2 transform -translate-x-1/2 bg-slate-800 text-white text-[10px] font-medium px-2 py-0.5 rounded shadow opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none whitespace-nowrap z-20">
                                    <?= $data['conteo'] ?> registrados
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
    
            <div class="flex justify-between text-[11px] font-bold px-2 mt-3 border-t border-slate-50 pt-2 gap-2 sm:gap-4">
                <?php foreach($flujoSemanalProcesado as $dia => $data): ?>
                    <span class="flex-1 text-center <?= $data['es_hoy'] ? 'text-blue-600 font-extrabold' : 'text-slate-400 font-medium' ?>">
                        <?= $dia ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="w-full">
    <div class="bg-white p-6 rounded-3xl border border-slate-100 soft-shadow">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Fichas de Pacientes Recientes</h2>
                <p class="text-xs text-slate-400">Últimos registros agregados al sistema</p>
            </div>
            <a href="<?= BASE_URL ?>pacientes" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                Ver historial completo <i class="fa-solid fa-chevron-right text-[9px]"></i>
            </a>
        </div>
        
        <div class="space-y-3">
            <?php if($totalReal > 0): ?>
                <?php foreach($pacientesRecientes as $index => $paciente): 
                    $palabras = explode(' ', trim($paciente['nombre']));
                    $iniciales = strtoupper(substr($palabras[0], 0, 1) . (isset($palabras[1]) ? substr($palabras[1], 0, 1) : ''));
                    $colorAvatar = ($index % 2 === 0) ? 'bg-blue-100 text-blue-600' : 'bg-cyan-100 text-cyan-600';
                ?>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3.5 bg-slate-50/50 hover:bg-slate-50 border border-slate-100 rounded-2xl gap-3 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 <?= $colorAvatar ?> rounded-full flex items-center justify-center font-bold text-sm">
                                <?= $iniciales ?>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800"><?= htmlspecialchars($paciente['nombre']) ?></h4>
                                <span class="text-xs text-slate-400 font-mono">DNI: <?= htmlspecialchars($paciente['dni']) ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                            <a href="<?= BASE_URL ?>../app/controllers/view_patient.php?id=<?= $paciente['id'] ?>" class="text-xs font-bold text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-xl border border-blue-100 transition-all text-center">
                                <i class="fa-solid fa-eye mr-1"></i> Ver Perfil Detallado
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-10 border-2 border-dashed border-slate-100 rounded-2xl bg-slate-50/30">
                    <i class="fa-solid fa-folder-open text-3xl text-slate-300 mb-2 block"></i>
                    <p class="text-xs text-slate-400">No hay pacientes registrados en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
if (!$isAjax) {
    include_once VIEW_PATH . 'layout/footer.php'; 
}
?>