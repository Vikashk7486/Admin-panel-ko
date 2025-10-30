<?php
require_once '../common/config.php';

$error = '';

// Handle Login
if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $query = "SELECT * FROM admin WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                redirect('index.php');
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Admin not found!";
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Login - Adept Play</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        body {
            overflow-x: hidden;
        }
        input {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
    </style>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-red-600 to-orange-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold mb-2">Admin Panel</h1>
            <p class="text-gray-400">Adept Play Management</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-slate-800 rounded-2xl shadow-2xl p-8">
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="username" required class="w-full bg-slate-700 text-white pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" required class="w-full bg-slate-700 text-white pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
                    </div>
                </div>
                <button type="submit" name="login" class="w-full bg-gradient-to-r from-red-600 to-orange-600 text-white py-3 rounded-lg font-semibold hover:from-red-700 hover:to-orange-700 transition shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login as Admin
                </button>
            </form>
        </div>
        
        <div class="text-center mt-6">
            <a href="../login.php" class="text-gray-400 hover:text-purple-400 transition text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back to User Login
            </a>
        </div>
    </div>
</body>
</html>
