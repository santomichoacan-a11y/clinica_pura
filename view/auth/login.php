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
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 sm:p-6 md:p-10">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl flex flex-col md:flex-row overflow-hidden min-h-[600px]">
        
        <div class="w-full md:w-1/2 p-8 sm:p-12 md:p-16 flex flex-col justify-center">
            
            <div class="text-center mb-8">
                <div class="flex items-center justify-center gap-2 text-cyan-600 font-bold text-3xl tracking-wide uppercase">
                    <i class="fa-solid fa-heart-pulse text-blue-600 text-4xl"></i>
                    <span>Clínica <span class="text-blue-600">RV</span></span>
                </div>
                <p class="text-slate-400 text-sm mt-3 px-4">
                    Inicia sesión para acceder al historial médico y la gestión de citas.
                </p>
            </div>

            <form action="?action=login" method="POST" class="space-y-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <input type="text" name="username" placeholder="Usuario" required 
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-slate-700 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm">
                </div>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" placeholder="Contraseña" required 
                        class="w-full pl-11 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-full text-slate-700 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-300 hover:text-slate-500 cursor-pointer">
                        <i class="fa-regular fa-circle-question"></i>
                    </span>
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-r hover:bg-gradient-to-l from-blue-600 to-cyan-500 text-white font-semibold py-3 rounded-full shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 transform active:scale-[0.98] transition-all text-base tracking-wide uppercase mt-2">
                    Iniciar Sesión
                </button>
            </form>

        </div>

        <div class="hidden md:flex w-1/2 relative bg-cover bg-center items-center justify-center p-12 text-center" 
             style="background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=1000');">
            
            <div class="absolute inset-0 bg-gradient-to-br from-blue-700/90 via-blue-800/85 to-cyan-600/90 mix-blend-multiply"></div>
            
            <div class="relative z-10 text-white max-w-sm">
                <div class="mb-4">
                    <i class="fa-solid fa-hospital-user text-5xl opacity-90"></i>
                </div>
                <h3 class="text-3xl font-bold mb-4 tracking-tight">Portal de Salud</h3>
                <p class="text-blue-100 text-sm leading-relaxed font-light">
                    Bienvenido a su espacio digital de confianza. Acceda a sus resultados clínicos, recetas electrónicas e información médica con total seguridad.
                </p>
            </div>
        </div>

    </div>
        <script>
    // Forzar la recarga de la página si se detecta que el usuario llegó aquí usando el botón "Atrás"
    (function () {
        window.onpageshow = function (event) {
            // event.persisted es verdadero si la página se cargó desde la memoria caché del navegador (BFCache)
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                // Recargar la página de forma limpia desde el servidor
                window.location.reload();
            }
        };
    })();
</script>
</body>
</html>