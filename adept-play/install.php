<?php
// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'adept_play');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connect without database first
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    
    if (!$conn) {
        $error = "Connection failed: " . mysqli_connect_error();
    } else {
        // Create database
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
        if (mysqli_query($conn, $sql)) {
            // Select the database
            mysqli_select_db($conn, DB_NAME);
            
            // Create users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                wallet_balance DECIMAL(10,2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            mysqli_query($conn, $sql);
            
            // Create admin table
            $sql = "CREATE TABLE IF NOT EXISTS admin (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL
            )";
            mysqli_query($conn, $sql);
            
            // Insert default admin (username: admin, password: admin123)
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "INSERT INTO admin (username, password) VALUES ('admin', '$admin_password')";
            mysqli_query($conn, $sql);
            
            // Create tournaments table
            $sql = "CREATE TABLE IF NOT EXISTS tournaments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                game_name VARCHAR(100) NOT NULL,
                entry_fee DECIMAL(10,2) NOT NULL,
                prize_pool DECIMAL(10,2) NOT NULL,
                commission_percentage DECIMAL(5,2) DEFAULT 0.00,
                match_time DATETIME NOT NULL,
                room_id VARCHAR(100) DEFAULT NULL,
                room_password VARCHAR(100) DEFAULT NULL,
                status ENUM('Upcoming', 'Live', 'Completed') DEFAULT 'Upcoming',
                winner_id INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            mysqli_query($conn, $sql);
            
            // Create participants table
            $sql = "CREATE TABLE IF NOT EXISTS participants (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                tournament_id INT NOT NULL,
                joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
                UNIQUE KEY unique_participation (user_id, tournament_id)
            )";
            mysqli_query($conn, $sql);
            
            // Create transactions table
            $sql = "CREATE TABLE IF NOT EXISTS transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                type ENUM('credit', 'debit') NOT NULL,
                description VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            mysqli_query($conn, $sql);
            
            $message = "Installation completed successfully! Redirecting to login page...";
            header("refresh:2;url=login.php");
        } else {
            $error = "Error creating database: " . mysqli_error($conn);
        }
        
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Install - Adept Play</title>
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
    </style>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-slate-800 rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-download text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Adept Play</h1>
                <p class="text-gray-400">Installation Setup</p>
            </div>
            
            <?php if ($message): ?>
                <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$message): ?>
                <div class="bg-slate-700 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold mb-3 text-lg">Installation Details:</h3>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><i class="fas fa-check text-green-400 mr-2"></i>Database: adept_play</li>
                        <li><i class="fas fa-check text-green-400 mr-2"></i>Admin Username: admin</li>
                        <li><i class="fas fa-check text-green-400 mr-2"></i>Admin Password: admin123</li>
                        <li><i class="fas fa-check text-green-400 mr-2"></i>All tables will be created</li>
                    </ul>
                </div>
                
                <form method="POST" action="">
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-4 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition shadow-lg">
                        <i class="fas fa-play mr-2"></i>Start Installation
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
