<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-96">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-slate-800">Clínica Médica</h1>
            <p class="text-slate-500">Inicia sesión para continuar</p>
        </div>
        <form action="/login" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Usuario</label>
                <input type="text" name="username" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Entrar
            </button>
        </form>
    </div>
</body>
</html>