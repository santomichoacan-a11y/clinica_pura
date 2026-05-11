<?php
/**
 * view/auth/login.php
 * Formulario de autenticación con protección de caché y redirección activa de sesión
 */

// 1. Evitar que el navegador guarde el Login en caché si se regresa con las flechas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Si el usuario ya está autenticado, no tiene sentido mostrarle el login; lo mandamos al Dashboard
if (isset($_SESSION['user_id'])) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $redirectUrl = $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/dashboard";
    header("Location: " . $redirectUrl);
    exit();
}

// 3. Respaldo de la constante por si se accede directo sin pasar por el index del enrutador
if (!defined('BASE_URL')) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    define('BASE_URL', $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Clínica RV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 sm:p-6 md:p-8">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl flex flex-col md:flex-row overflow-hidden min-h-[500px] md:min-h-[600px]">
        
        <div class="w-full md:w-1/2 p-6 sm:p-10 md:p-12 lg:p-16 flex flex-col justify-center">
            
            <div class="text-center mb-6 md:mb-8">
                <div class="flex items-center justify-center gap-2 text-cyan-600 font-bold text-2xl sm:text-3xl tracking-wide uppercase">
                    <i class="fa-solid fa-heart-pulse text-blue-600 text-3xl sm:text-4xl"></i>
                    <span>Clínica <span class="text-blue-600">RV</span></span>
                </div>
                <p class="text-slate-400 text-xs sm:text-sm mt-2 px-2 sm:px-4">
                    Inicia sesión para acceder al historial médico y la gestión de citas.
                </p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-full text-xs sm:text-sm text-center mb-4 border border-red-100 flex items-center justify-center gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="?action=login" method="POST" class="space-y-4 sm:space-y-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <i class="fa-regular fa-user text-sm sm:text-base"></i>
                    </span>
                    <input type="text" name="username" placeholder="Usuario" required 
                        class="w-full pl-11 pr-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-full text-slate-700 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-xs sm:text-sm">
                </div>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <i class="fa-solid fa-lock text-sm sm:text-base"></i>
                    </span>
                    <input type="password" id="passwordInput" name="password" placeholder="Contraseña" required 
                        class="w-full pl-11 pr-12 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-full text-slate-700 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-xs sm:text-sm">
                    
                    <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-blue-600 cursor-pointer transition-colors">
                        <i class="fa-regular fa-eye text-sm sm:text-base" id="eyeIcon"></i>
                    </span>
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-r hover:bg-gradient-to-l from-blue-600 to-cyan-500 text-white font-semibold py-2.5 sm:py-3 rounded-full shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 transform active:scale-[0.98] transition-all text-sm sm:text-base tracking-wide uppercase mt-2">
                    Iniciar Sesión
                </button>
            </form>

        </div>

        <div class="hidden md:flex w-1/2 relative bg-cover bg-center items-center justify-center p-8 lg:p-12 text-center" 
             style="background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=1000');">
            
            <div class="absolute inset-0 bg-gradient-to-br from-blue-700/90 via-blue-800/85 to-cyan-600/90 mix-blend-multiply"></div>
            
            <div class="relative z-10 text-white max-w-sm">
                <div class="mb-4">
                    <i class="fa-solid fa-hospital-user text-4xl lg:text-5xl opacity-90"></i>
                </div>
                <h3 class="text-2xl lg:text-3xl font-bold mb-3 lg:mb-4 tracking-tight">Portal de Salud</h3>
                <p class="text-blue-100 text-xs lg:text-sm leading-relaxed font-light">
                   Bienvenido a su plataforma digital de confianza. Acceda a los historiales clínicos de sus pacientes y gestione su información médica con total seguridad.
                </p>
            </div>
        </div>

    </div>

    <script>
    // 1. Forzar la recarga si el usuario vuelve atrás con la caché del navegador
    (function () {
        window.onpageshow = function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        };
    })();

    // 2. Funcionalidad interactiva para ver/ocultar la contraseña
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#passwordInput');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Alternar icono de ojo abierto / cerrado
        if (type === 'text') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
    </script>
</body>
</html>