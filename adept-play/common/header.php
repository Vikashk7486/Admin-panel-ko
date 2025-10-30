<?php
if (!isUserLoggedIn()) {
    redirect('login.php');
}

// Get user wallet balance
$user_id = $_SESSION['user_id'];
$query = "SELECT username, wallet_balance FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Adept Play - Tournament App</title>
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
        input, textarea {
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
<body class="bg-slate-900 text-white min-h-screen pb-20">
    <!-- Top Header -->
    <header class="bg-gradient-to-r from-purple-600 to-blue-600 p-4 sticky top-0 z-50 shadow-lg">
        <div class="flex justify-between items-center max-w-md mx-auto">
            <div>
                <h1 class="text-2xl font-bold">Adept Play</h1>
                <p class="text-xs text-purple-100">Tournament Gaming</p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                <p class="text-xs text-purple-100">Wallet</p>
                <p class="text-lg font-bold"><?php echo formatCurrency($user['wallet_balance']); ?></p>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="max-w-md mx-auto p-4">
