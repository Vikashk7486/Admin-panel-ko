<?php
require_once 'common/config.php';

$message = '';
$error = '';
$activeTab = 'login';

// Handle Login
if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                redirect('index.php');
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found!";
        }
    }
}

// Handle Signup
if (isset($_POST['signup'])) {
    $activeTab = 'signup';
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        // Check if username exists
        $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Username or email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password, wallet_balance) VALUES ('$username', '$email', '$hashed_password', 0.00)";
            
            if (mysqli_query($conn, $query)) {
                $message = "Account created successfully! Please login.";
                $activeTab = 'login';
            } else {
                $error = "Error creating account: " . mysqli_error($conn);
            }
        }
    }
}

// Redirect if already logged in
if (isUserLoggedIn()) {
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Adept Play</title>
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
        
        function showTab(tab) {
            document.getElementById('login-tab').classList.add('hidden');
            document.getElementById('signup-tab').classList.add('hidden');
            document.getElementById(tab + '-tab').classList.remove('hidden');
            
            document.getElementById('login-btn').classList.remove('bg-purple-600', 'text-white');
            document.getElementById('login-btn').classList.add('bg-slate-700', 'text-gray-400');
            document.getElementById('signup-btn').classList.remove('bg-purple-600', 'text-white');
            document.getElementById('signup-btn').classList.add('bg-slate-700', 'text-gray-400');
            
            document.getElementById(tab + '-btn').classList.remove('bg-slate-700', 'text-gray-400');
            document.getElementById(tab + '-btn').classList.add('bg-purple-600', 'text-white');
        }
    </script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-gamepad text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold mb-2">Adept Play</h1>
            <p class="text-gray-400">Join tournaments and win prizes</p>
        </div>
        
        <?php if ($message): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-4">
                <i class="fas fa-check-circle mr-2"></i><?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-slate-800 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Tab Buttons -->
            <div class="flex">
                <button id="login-btn" onclick="showTab('login')" class="flex-1 py-4 font-semibold <?php echo $activeTab == 'login' ? 'bg-purple-600 text-white' : 'bg-slate-700 text-gray-400'; ?> transition">
                    Login
                </button>
                <button id="signup-btn" onclick="showTab('signup')" class="flex-1 py-4 font-semibold <?php echo $activeTab == 'signup' ? 'bg-purple-600 text-white' : 'bg-slate-700 text-gray-400'; ?> transition">
                    Sign Up
                </button>
            </div>
            
            <!-- Login Form -->
            <div id="login-tab" class="p-6 <?php echo $activeTab != 'login' ? 'hidden' : ''; ?>">
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Username</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="username" required class="w-full bg-slate-700 text-white pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="password" name="password" required class="w-full bg-slate-700 text-white pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>
                    <button type="submit" name="login" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>
            </div>
            
            <!-- Signup Form -->
            <div id="signup-tab" class="p-6 <?php echo $activeTab != 'signup' ? 'hidden' : ''; ?>">
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Username</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="username" required class="w-full bg-slate-700 text-white pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="email" name="email" required class="w-full bg-slate-700 text-white pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="password" name="password" required class="w-full bg-slate-700 text-white pl-10 pr-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>
                    <button type="submit" name="signup" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-6">
            <a href="admin/login.php" class="text-gray-400 hover:text-purple-400 transition text-sm">
                <i class="fas fa-shield-alt mr-1"></i>Admin Login
            </a>
        </div>
    </div>
</body>
</html>
