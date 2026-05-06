<?php if ($total_paginas > 1): ?>
<div class="flex items-center justify-between border-t border-slate-100 bg-white px-4 py-4 sm:px-6 mt-6 rounded-2xl shadow-sm">
    <div class="flex flex-1 justify-between sm:hidden">
        <a href="?page=<?= $pagina_actual - 1 ?>" class="<?= $pagina_actual <= 1 ? 'pointer-events-none opacity-40' : '' ?> relative inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">Anterior</a>
        <a href="?page=<?= $pagina_actual + 1 ?>" class="<?= $pagina_actual >= $total_paginas ? 'pointer-events-none opacity-40' : '' ?> relative ml-3 inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">Siguiente</a>
    </div>
    
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-500">
                Mostrando página <span class="font-bold text-slate-800"><?= $pagina_actual ?></span> de <span class="font-bold text-slate-800"><?= $total_paginas ?></span> (<span class="font-medium"><?= $total_pacientes ?></span> pacientes en total)
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-full gap-1" aria-label="Pagination">
                <a href="?page=<?= $pagina_actual - 1 ?>" class="<?= $pagina_actual <= 1 ? 'pointer-events-none opacity-30' : '' ?> relative inline-flex items-center rounded-full p-2 text-slate-400 hover:bg-slate-50">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?page=<?= $i ?>" class="relative inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold transition-all <?= $i === $pagina_actual ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?page=<?= $pagina_actual + 1 ?>" class="<?= $pagina_actual >= $total_paginas ? 'pointer-events-none opacity-30' : '' ?> relative inline-flex items-center rounded-full p-2 text-slate-400 hover:bg-slate-50">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </nav>
        </div>
    </div>
</div>
<?php endif; ?>