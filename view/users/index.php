<?php
/**
 * view/users/index.php
 */
$pageTitle = "Gestión de Personal | Clínica Pura";
$activePage = "usuarios"; 

include_once VIEW_PATH . 'layout/header.php';
include_once VIEW_PATH . 'layout/nav.php';
?>

<div class="p-4 max-w-7xl mx-auto space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Gestión de Personal</h1>
            <p class="text-xs text-slate-400 mt-1">Crea, edita y administra los accesos de los usuarios del sistema</p>
        </div>
        <button onclick="openUserModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/10 transition-colors self-start sm:self-auto">
            <i class="fa-solid fa-user-plus text-xs"></i>
            Registrar Nuevo Usuario
        </button>
    </div>

    <?php if (!empty($error) || isset($_SESSION['flash_error'])): ?>
        <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-sm rounded-2xl font-medium">
            <?= $error ?: $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 text-sm rounded-2xl font-medium">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl border border-slate-100 soft-shadow overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h3 class="text-base font-bold text-slate-800">Cuentas Activas</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <th class="p-4 pl-6">Nombre de Usuario</th>
                        <th class="p-4">Rol de Acceso</th>
                        <th class="p-4 text-right pr-6">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 pl-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center uppercase">
                                            <?= substr($u['username'], 0, 2) ?>
                                        </div>
                                        <span class="text-slate-800 font-bold"><?= htmlspecialchars($u['username']) ?></span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full <?= $u['role'] === 'admin' ? 'bg-purple-50 text-purple-600' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= $u['role'] === 'admin' ? 'Administrador' : 'Personal / Asistente' ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6 space-x-1">
                                    <a href="<?= BASE_URL ?>../app/controllers/users_controller.php?action=edit&id=<?= $u['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-100 transition-all" title="Editar usuario">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>../app/controllers/users_controller.php?action=delete&id=<?= $u['id'] ?>" onclick="return confirm('¿Estás completamente seguro de que deseas eliminar esta cuenta?');" class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 hover:border-red-100 transition-all" title="Eliminar cuenta">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-10 text-slate-400 text-xs">No hay usuarios registrados en el sistema.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    
    <div class="bg-white w-full max-w-md rounded-3xl border border-slate-100 shadow-2xl transform scale-95 transition-transform duration-300 p-6 relative">
        
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid <?= $userToEdit ? 'fa-user-pen text-amber-500' : 'fa-user-plus text-blue-500' ?>"></i>
                <span><?= $userToEdit ? 'Editar Miembro del Personal' : 'Registrar Nuevo Staff' ?></span>
            </h3>
            <button onclick="closeUserModal()" class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>../app/controllers/users_controller.php?action=<?= $userToEdit ? 'update' : 'create' ?>" method="POST" class="space-y-4">
            <?php if ($userToEdit): ?>
                <input type="hidden" name="id" value="<?= $userToEdit['id'] ?>">
            <?php endif; ?>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre de Usuario</label>
                <input type="text" name="username" required value="<?= $userToEdit['username'] ?? '' ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 transition-colors" placeholder="Ej: maria_perez">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">
                    Contraseña <?= $userToEdit ? '<span class="text-[10px] font-normal text-slate-400 normal-case">(Déjalo en blanco para mantener la actual)</span>' : '' ?>
                </label>
                <input type="password" name="password" <?= $userToEdit ? '' : 'required' ?> class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 transition-colors" placeholder="••••••••">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Rol de Acceso</label>
                <select name="role" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 bg-white transition-colors">
                    <option value="user" <?= (isset($userToEdit['role']) && $userToEdit['role'] === 'user') ? 'selected' : '' ?>>Usuario</option>
                    <option value="admin" <?= (isset($userToEdit['role']) && $userToEdit['role'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <div class="pt-4 flex gap-2 border-t border-slate-50">
                <button type="button" onclick="closeUserModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-colors text-center">
                    Cancelar
                </button>
                <button type="submit" class="w-2/3 py-2.5 <?= $userToEdit ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/10' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/10' ?> text-white font-semibold text-sm rounded-xl shadow-md transition-colors">
                    <?= $userToEdit ? 'Guardar Cambios' : 'Registrar Usuario' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('userModal');
    const modalContainer = modal.querySelector('div');

    function openUserModal() {
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modalContainer.classList.remove('scale-95');
        modalContainer.classList.add('scale-100');
    }

    function closeUserModal() {
        modal.classList.add('opacity-0', 'pointer-events-none');
        modalContainer.classList.remove('scale-100');
        modalContainer.classList.add('scale-95');
        
        // Si el usuario estaba editando y decide cancelar, limpiamos la URL regresando a la lista limpia
        <?php if ($userToEdit): ?>
            window.location.href = "<?= BASE_URL ?>usuarios";
        <?php endif; ?>
    }

    // Si venimos del flujo de edición (GET ?action=edit), abrimos el modal automáticamente al cargar
    <?php if ($userToEdit): ?>
        document.addEventListener('DOMContentLoaded', function() {
            openUserModal();
        });
    <?php endif; ?>
</script>

<?php 
include_once VIEW_PATH . 'layout/footer.php'; 
?>