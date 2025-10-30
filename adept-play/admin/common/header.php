<?php
if (!isAdminLoggedIn()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Panel - Adept Play</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }
        body {
            overflow-x: hidden;
            background: #0f172a;
        }
        input, textarea, select {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
    </style>
    <script>
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        // Disable zoom
        document.addEventListener('gesturestart', function(e) {
            e.preventDefault();
        });
        
        document.addEventListener('touchmove', function(event) {
            if (event.scale !== 1) {
                event.preventDefault();
            }
        }, { passive: false });
        
        // Disable keyboard shortcuts for zoom
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === '+' || e.key === '-' || e.key === '=')) {
                e.preventDefault();
            }
        });
    </script>
</head>
<body class="bg-slate-900 text-white min-h-screen">
    <!-- Top Header -->
    <header class="bg-gradient-to-r from-red-600 to-orange-600 p-4 sticky top-0 z-50 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Admin Panel</h1>
                <p class="text-xs text-red-100">Adept Play Management</p>
            </div>
            <a href="?logout=true" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-semibold transition">
                <i class="fas fa-sign-out-alt mr-1"></i>Logout
            </a>
        </div>
    </header>
    
    <!-- Navigation Menu -->
    <nav class="bg-slate-800 border-b border-slate-700 sticky top-16 z-40">
        <div class="max-w-6xl mx-auto flex overflow-x-auto">
            <a href="index.php" class="px-4 py-3 text-sm font-medium whitespace-nowrap <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-orange-400 border-b-2 border-orange-400' : 'text-gray-400 hover:text-white'; ?>">
                <i class="fas fa-home mr-1"></i>Dashboard
            </a>
            <a href="tournament.php" class="px-4 py-3 text-sm font-medium whitespace-nowrap <?php echo basename($_SERVER['PHP_SELF']) == 'tournament.php' ? 'text-orange-400 border-b-2 border-orange-400' : 'text-gray-400 hover:text-white'; ?>">
                <i class="fas fa-trophy mr-1"></i>Tournaments
            </a>
            <a href="user.php" class="px-4 py-3 text-sm font-medium whitespace-nowrap <?php echo basename($_SERVER['PHP_SELF']) == 'user.php' ? 'text-orange-400 border-b-2 border-orange-400' : 'text-gray-400 hover:text-white'; ?>">
                <i class="fas fa-users mr-1"></i>Users
            </a>
            <a href="setting.php" class="px-4 py-3 text-sm font-medium whitespace-nowrap <?php echo basename($_SERVER['PHP_SELF']) == 'setting.php' ? 'text-orange-400 border-b-2 border-orange-400' : 'text-gray-400 hover:text-white'; ?>">
                <i class="fas fa-cog mr-1"></i>Settings
            </a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="max-w-6xl mx-auto p-4">
