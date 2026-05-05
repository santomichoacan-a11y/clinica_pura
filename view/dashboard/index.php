<?php
/**
 * view/dashboard/index.php
 */

// 1. CARGA ÚNICA DE CONFIGURACIÓN GLOBAL Y ZONA HORARIA
require_once __DIR__ . '/../../app/config/app.php';
checkAuth(); // Bloquea el acceso si no hay sesión

// Forzamos la zona horaria para que coincida exactamente con tu entorno local
date_default_timezone_set('America/El_Salvador'); 

$pageTitle = "Dashboard | Clínica Pura";
$activePage = "dashboard";

// 2. CONEXIÓN A BASE DE DATOS Y LÓGICA DE NEGOCIO
require_once ROOT_PATH . 'app/config/db.php';

// Obtener el número del día actual correctamente (1 para Lunes, 2 para Martes...)
$numeroDiaActual = (int)date('N'); 

// Inicializamos la estructura limpia con los días de Lunes a Viernes
$flujoSemanalProcesado = [
    'Lun' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 1)],
    'Mar' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 2)],
    'Mié' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 3)],
    'Jue' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 4)],
    'Vie' => ['conteo' => 0, 'porcentaje' => 0, 'es_hoy' => ($numeroDiaActual === 5)]
];

$pacientesRecientes = [];
$totalReal = 0;

try {
    $database = new Database();
    $db = $database->getConnection();

    // A. Obtener los últimos 3 pacientes agregados recientemente
    $queryRecientes = "SELECT id, nombre, dni FROM patients ORDER BY id DESC LIMIT 3";
    $stmtRecientes = $db->prepare($queryRecientes);
    $stmtRecientes->execute();
    $pacientesRecientes = $stmtRecientes->fetchAll(PDO::FETCH_ASSOC);
    
    // Conteo total real de la tabla para la tarjeta azul de la izquierda
    $stmtTotal = $db->query("SELECT COUNT(id) as total FROM patients");
    $totalReal = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // B. Lógica de consulta dinámica usando WEEKDAY() (0=Lunes, 1=Martes, 2=Miércoles...)
    $diasMapeo = [
        0 => 'Lun',
        1 => 'Mar',
        2 => 'Mié',
        3 => 'Jue',
        4 => 'Vie'
    ];

    $fechaLunes = date('Y-m-d', strtotime('monday this week'));
    $fechaViernes = date('Y-m-d', strtotime('friday this week'));

    // Usamos WEEKDAY() para asegurar compatibilidad exacta estándar
    $queryEstadisticas = "
        SELECT WEEKDAY(created_at) as dia_indice, COUNT(id) as total 
        FROM patients 
        WHERE DATE(created_at) BETWEEN :inicio AND :fin
        GROUP BY WEEKDAY(created_at)
    ";
    
    $stmtEst = $db->prepare($queryEstadisticas);
    $stmtEst->execute([':inicio' => $fechaLunes, ':fin' => $fechaViernes]);
    $resultadosEst = $stmtEst->fetchAll(PDO::FETCH_ASSOC);

    $maxPacientesEnUnDia = 0;
    foreach ($resultadosEst as $row) {
        $indice = (int)$row['dia_indice'];
        
        // Si el índice corresponde a Lunes-Viernes (0 al 4)
        if (isset($diasMapeo[$indice])) {
            $nombreDiaCorto = $diasMapeo[$indice];
            $flujoSemanalProcesado[$nombreDiaCorto]['conteo'] = (int)$row['total'];
            
            if ((int)$row['total'] > $maxPacientesEnUnDia) {
                $maxPacientesEnUnDia = (int)$row['total'];
            }
        }
    }

    // Calcular los porcentajes proporcionales para las alturas de las barras
    foreach ($flujoSemanalProcesado as $dia => $datos) {
        if ($maxPacientesEnUnDia > 0) {
            $flujoSemanalProcesado[$dia]['porcentaje'] = round(($datos['conteo'] / $maxPacientesEnUnDia) * 100);
        }
    }

} catch (PDOException $e) {
    die("Error en el Dashboard: " . $e->getMessage());
}

// 3. INCLUSIÓN DE COMPONENTES DE INTERFAZ
include_once VIEW_PATH . 'layout/header.php';
include_once VIEW_PATH . 'layout/nav.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
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
                    <span class="absolute bottom-4 right-4 text-xs bg-white/20 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> +2.3%
                    </span>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-teal-400 p-5 rounded-3xl text-white shadow-xl shadow-emerald-500/10 relative overflow-hidden">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                    </div>
                    <h3 class="text-3xl font-bold tracking-tight">12</h3>
                    <p class="text-emerald-100/80 text-xs font-medium mt-1">Citas Hoy</p>
                    <span class="absolute bottom-4 right-4 text-xs bg-white/20 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> +1.0%
                    </span>
                </div>

                <div class="bg-gradient-to-br from-indigo-600 to-purple-500 p-5 rounded-3xl text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    </div>
                    <h3 class="text-3xl font-bold tracking-tight">4</h3>
                    <p class="text-indigo-100/80 text-xs font-medium mt-1">Pendientes</p>
                    <span class="absolute bottom-4 right-4 text-xs bg-white/20 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-down text-[9px]"></i> -0.4%
                    </span>
                </div>

                <div class="bg-gradient-to-br from-cyan-500 to-blue-400 p-5 rounded-3xl text-white shadow-xl shadow-cyan-500/10 relative overflow-hidden">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>
                    <h3 class="text-3xl font-bold tracking-tight">85%</h3>
                    <p class="text-cyan-100/80 text-xs font-medium mt-1">Eficiencia de Alta</p>
                    <span class="absolute bottom-4 right-4 text-xs bg-white/20 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> +3.3%
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 soft-shadow">
            <h2 class="text-lg font-bold text-slate-800 mb-5 flex items-center justify-between">
                <span>Análisis de Satisfacción</span>
                <i class="fa-solid fa-sliders text-slate-400 text-sm"></i>
            </h2>
            
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1.5">
                        <span class="text-slate-500">Reseñas Positivas</span>
                        <span class="text-blue-600">88%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-400 h-full rounded-full" style="width: 88%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1.5">
                        <span class="text-slate-500">Tiempo de Espera Óptimo</span>
                        <span class="text-emerald-500">74%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full" style="width: 74%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-7 space-y-8">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-100 soft-shadow">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-slate-100 gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Estadísticas Clínicas</h2>
                    <p class="text-xs text-slate-400">Flujo semanal de pacientes ingresados</p>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider">
                    <?= date('d') . ' ' . date('M') . ' ' . date('Y') ?>
                </span>
            </div>
            
            <div class="h-44 w-full flex items-end justify-between px-4 pt-6 relative">
                
                <div class="absolute inset-x-0 bottom-0 top-6 border-b border-dashed border-slate-100 flex flex-col justify-between pointer-events-none">
                    <div class="w-full border-t border-dashed border-slate-100/70"></div>
                    <div class="w-full border-t border-dashed border-slate-100/70"></div>
                    <div class="w-full border-t border-dashed border-slate-100/70"></div>
                </div>
                
                <?php foreach($flujoSemanalProcesado as $dia => $data): 
                    $alturaEstetica = $data['porcentaje'];
                    if ($data['conteo'] > 0 && $alturaEstetica < 10) { $alturaEstetica = 10; } 
                ?>
                    <div class="flex flex-col items-center w-12 group relative">
                        
                        <?php if($data['es_hoy']): ?>
                            <div class="w-full bg-gradient-to-t from-blue-600 to-cyan-400 rounded-t-xl relative transition-all duration-300" style="height: <?= $alturaEstetica ?>%;">
                                <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-lg shadow-lg z-10 whitespace-nowrap">
                                    <?= $data['conteo'] ?> Pac. (Hoy)
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="w-full bg-slate-100 hover:bg-blue-100 rounded-t-xl transition-all duration-200 cursor-pointer relative" style="height: <?= $alturaEstetica ?>%;">
                                <div class="absolute -top-7 left-1/2 transform -translate-x-1/2 bg-slate-800 text-white text-[10px] font-medium px-2 py-0.5 rounded shadow opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none whitespace-nowrap">
                                    <?= $data['conteo'] ?> registrados
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-between text-[11px] font-bold px-4 mt-3 border-t border-slate-50 pt-2">
                <?php foreach($flujoSemanalProcesado as $dia => $data): ?>
                    <span class="w-12 text-center <?= $data['es_hoy'] ? 'text-blue-600 font-extrabold' : 'text-slate-400 font-medium' ?>">
                        <?= $dia ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 soft-shadow">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Fichas de Pacientes</h2>
                    <p class="text-xs text-slate-400">Registros agregados recientemente</p>
                </div>
                <a href="<?= BASE_URL ?>pacientes" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                    Ver todos <i class="fa-solid fa-chevron-right text-[9px]"></i>
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
                                    <span class="text-xs text-slate-400">DNI: <?= htmlspecialchars($paciente['dni']) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                <span class="text-xs font-medium text-slate-500 bg-white border border-slate-100 px-3 py-1 rounded-full">
                                    <i class="fa-solid fa-file-medical text-blue-400 mr-1"></i> Expediente Activo
                                </span>
                                <a href="<?= BASE_URL ?>../app/controllers/view_patient.php?id=<?= $paciente['id'] ?>" class="text-xs font-bold text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100 transition-all text-center">
                                    Ver Perfil
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
</div>

<?php 
// 4. INCLUSIÓN DEL CIERRE DEL LAYOUT
include_once VIEW_PATH . 'layout/footer.php'; 
?>