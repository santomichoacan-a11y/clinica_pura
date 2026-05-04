<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Clínica Pura</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-200">
        <h2 class="text-3xl font-bold text-slate-800 mb-6 text-center">Bienvenido</h2>
        <p class="text-slate-500 text-center mb-8">Ingresa tus credenciales para acceder</p>
        
        <form action="?action=login" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700">Usuario</label>
                <input type="text" name="username" required 
                    class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                <input type="password" name="password" required 
                    class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transform active:scale-95 transition-all">
                Entrar al Sistema
            </button>
        </form>
    </div>
</body>
</html>