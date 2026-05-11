<?php
/**
 * view/layout/header.php
 */

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

$username = $_SESSION['username'] ?? 'Usuario';
$role = $_SESSION['role'] ?? 'invitado';

// Evitar variables indefinidas si el controlador no las inyecta
$totalReal = $totalPacientes ?? $totalReal ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Clínica Pura' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Sutil sombra Soft UI similar a la imagen */
        .soft-shadow {
            box-shadow: 0 10px 30px -5px rgba(148, 163, 184, 0.12), 0 4px 12px -4px rgba(148, 163, 184, 0.08);
        }
    </style>
</head>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Definimos la raíz del proyecto para los archivos JS
    const BASE_URL = '/clinica_pura/'; 
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<body class="bg-[#f6f8fb] flex flex-col md:flex-row min-h-screen font-sans antialiased text-slate-700">
