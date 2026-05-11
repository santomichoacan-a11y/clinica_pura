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
            <p class="text-slate-500 text-sm">Gestión de accesos, perfiles y permisos de usuarios del sistema.</p>
        </div>
        <button onclick="openUserModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/10 transition-colors self-start sm:self-auto">
            <i class="fa-solid fa-user-plus text-xs"></i>
            Registrar Nuevo Usuario
        </button>
    </div>

    <?php if (!empty($error) || isset($_SESSION['flash_error'])): ?>
        <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-sm rounded-2xl font-medium mb-4">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>
            <?= $error ?: $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div id="flash-success-trigger" data-message="<?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>" class="hidden"></div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl border border-slate-100 soft-shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/50">
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 text-[11px] uppercase tracking-wider font-bold">
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
                                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center uppercase border border-slate-200/50">
                                            <?= substr($u['username'], 0, 2) ?>
                                        </div>
                                        <span class="text-slate-800 font-bold"><?= htmlspecialchars($u['username']) ?></span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full <?= $u['role'] === 'admin' ? 'bg-purple-50 text-purple-600 border border-purple-100' : 'bg-slate-100 text-slate-600 border border-slate-200/50' ?>">
                                        <?= $u['role'] === 'admin' ? 'Administrador' : 'Personal / Asistente' ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <a href="<?= BASE_URL ?>../app/controllers/users_controller.php?action=edit&id=<?= $u['id'] ?>" 
                                        class="p-2 text-sky-500 hover:bg-sky-50 rounded-xl transition-all" title="Editar usuario">
                                            <i class="fa-solid fa-user-pen text-sm"></i>
                                        </a>

                                        <button onclick="confirmarEliminarUsuario('<?= $u['id'] ?>', '<?= $u['username'] ?>')" 
                                        class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Eliminar cuenta">
                                            <i class="fa-solid fa-circle-xmark text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-6 py-20 text-center text-slate-400">
                                <i class="fa-solid fa-user-slash text-4xl mb-4 block text-slate-200"></i>
                                <h3 class="text-sm font-bold text-slate-700 mb-0.5">Base de datos vacía</h3>
                                <p class="text-xs text-slate-400">No hay usuarios registrados en el sistema.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] border border-slate-100 shadow-2xl transform scale-95 transition-transform duration-300 p-8 relative">
        
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i class="fa-solid <?= $userToEdit ? 'fa-user-pen' : 'fa-user-plus' ?> text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">
                        <?= $userToEdit ? 'Editar Usuario' : 'Nuevo Usuario' ?>
                    </h3>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                        <?= $userToEdit ? 'Actualizar credenciales' : 'Acceso al sistema' ?>
                    </p>
                </div>
            </div>
            <button onclick="closeUserModal()" class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:rotate-90 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>../app/controllers/users_controller.php?action=<?= $userToEdit ? 'update' : 'create' ?>" method="POST" class="space-y-4">
            <?php if ($userToEdit): ?>
                <input type="hidden" name="id" value="<?= $userToEdit['id'] ?>">
            <?php endif; ?>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Nombre de Usuario</label>
                <input type="text" name="username" required value="<?= $userToEdit['username'] ?? '' ?>" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none" placeholder="Ej: maria_perez">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">
                    Contraseña <?= $userToEdit ? '<span class="text-[9px] font-normal text-slate-400 normal-case">(Opcional para mantener actual)</span>' : '' ?>
                </label>
                <input type="password" name="password" <?= $userToEdit ? '' : 'required' ?> class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none" placeholder="••••••••">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Rol de Acceso</label>
                <select name="role" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none bg-white">
                    <option value="user" <?= (isset($userToEdit['role']) && $userToEdit['role'] === 'user') ? 'selected' : '' ?>>Usuario / Personal</option>
                    <option value="admin" <?= (isset($userToEdit['role']) && $userToEdit['role'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <div class="pt-6 flex gap-3">
                <button type="button" onclick="closeUserModal()" class="flex-1 py-3.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-2xl uppercase tracking-widest hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-blue-500/20 uppercase tracking-widest transition-all">
                    <?= $userToEdit ? 'Guardar Cambios' : 'Registrar Usuario' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const UserAlert = {
        // Alerta de confirmación para Actualizar
        confirmUpdate: function(callback) {
            Swal.fire({
                title: '¿Guardar cambios?',
                text: "Se actualizarán los datos del usuario.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Revisar',
                reverseButtons: true,
                customClass: { popup: 'rounded-[2rem]' }
            }).then((result) => { if (result.isConfirmed) callback(); });
        },
        // Alerta de confirmación para Eliminar
        confirmDelete: function(nombre, callback) {
            Swal.fire({
                title: '¿Eliminar cuenta?',
                html: `Estás por eliminar a <b>${nombre}</b>.<br><small>Esto revocará su acceso al sistema.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                customClass: { popup: 'rounded-[2rem]' }
            }).then((result) => { if (result.isConfirmed) callback(); });
        },
        // Éxito
        success: function(msg) {
            Swal.fire({
                icon: 'success',
                title: '¡Hecho!',
                text: msg,
                timer: 2500,
                showConfirmButton: false,
                customClass: { popup: 'rounded-[2rem] border-none shadow-xl' }
            });
        }
    };

    function getModalElements() {
        const modal = document.getElementById('userModal');
        return { modal, container: modal ? modal.querySelector('div') : null };
    }

    function openUserModal() {
        const { modal, container } = getModalElements();
        if (modal && container) {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        }
    }

    function closeUserModal() {
        const { modal, container } = getModalElements();
        if (modal && container) {
            modal.classList.add('opacity-0', 'pointer-events-none');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');
            <?php if (isset($userToEdit) && $userToEdit): ?>
                setTimeout(() => { window.location.href = "<?= BASE_URL ?>usuarios"; }, 300);
            <?php endif; ?>
        }
    }

    function confirmarEliminarUsuario(id, nombre) {
        UserAlert.confirmDelete(nombre, () => {
            window.location.href = "<?= BASE_URL ?>../app/controllers/users_controller.php?action=delete&id=" + id;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Abrir modal si es modo edición
        <?php if (isset($userToEdit) && $userToEdit): ?>
            openUserModal();
        <?php endif; ?>

        // 2. Detectar Flash Message de PHP
        const flashTrigger = document.getElementById('flash-success-trigger');
        if (flashTrigger) {
            UserAlert.success(flashTrigger.getAttribute('data-message'));
        }

        // 3. Manejo del Formulario
        const userForm = document.querySelector('#userModal form');
        if (userForm) {
            userForm.addEventListener('submit', function(e) {
                const idInput = userForm.querySelector('input[name="id"]');
                if (idInput && idInput.value.trim() !== "") {
                    e.preventDefault();
                    UserAlert.confirmUpdate(() => { userForm.submit(); });
                }
            });
        }
    });

    window.onclick = function(event) {
        const { modal } = getModalElements();
        if (event.target == modal) closeUserModal();
    };
</script>

<?php include_once VIEW_PATH . 'layout/footer.php'; ?>