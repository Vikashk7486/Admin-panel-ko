<?php
require_once 'common/config.php';

$message = '';
$error = '';
$user_id = $_SESSION['user_id'];

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    
    if (empty($username) || empty($email)) {
        $error = "All fields are required!";
    } else {
        // Check if username/email already exists for other users
        $check_query = "SELECT * FROM users WHERE (username = '$username' OR email = '$email') AND id != '$user_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username or email already exists!";
        } else {
            $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = '$user_id'";
            if (mysqli_query($conn, $query)) {
                $_SESSION['username'] = $username;
                $message = "Profile updated successfully!";
            } else {
                $error = "Error updating profile!";
            }
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } else {
        // Verify current password
        $query = "SELECT password FROM users WHERE id = '$user_id'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
            if (mysqli_query($conn, $update_query)) {
                $message = "Password changed successfully!";
            } else {
                $error = "Error changing password!";
            }
        } else {
            $error = "Current password is incorrect!";
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

include 'common/header.php';

// Get user details
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get user stats
$tournaments_query = "SELECT COUNT(*) as total FROM participants WHERE user_id = '$user_id'";
$tournaments_result = mysqli_query($conn, $tournaments_query);
$total_tournaments = mysqli_fetch_assoc($tournaments_result)['total'];

$wins_query = "SELECT COUNT(*) as wins FROM tournaments WHERE winner_id = '$user_id'";
$wins_result = mysqli_query($conn, $wins_query);
$total_wins = mysqli_fetch_assoc($wins_result)['wins'];
?>

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

<div class="mb-6">
    <h2 class="text-2xl font-bold mb-2">My Profile</h2>
    <p class="text-gray-400 text-sm">Manage your account settings</p>
</div>

<!-- Profile Card -->
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <div class="flex items-center mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 w-20 h-20 rounded-full flex items-center justify-center text-3xl font-bold mr-4">
            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
        </div>
        <div>
            <h3 class="text-2xl font-bold"><?php echo $user['username']; ?></h3>
            <p class="text-gray-400"><?php echo $user['email']; ?></p>
        </div>
    </div>
    
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-slate-700 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-purple-400"><?php echo $total_tournaments; ?></p>
            <p class="text-xs text-gray-400">Tournaments</p>
        </div>
        <div class="bg-slate-700 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-yellow-400"><?php echo $total_wins; ?></p>
            <p class="text-xs text-gray-400">Wins</p>
        </div>
        <div class="bg-slate-700 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-green-400"><?php echo formatCurrency($user['wallet_balance']); ?></p>
            <p class="text-xs text-gray-400">Balance</p>
        </div>
    </div>
</div>

<!-- Edit Profile -->
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Edit Profile</h3>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Username</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        <button type="submit" name="update_profile" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition">
            <i class="fas fa-save mr-2"></i>Update Profile
        </button>
    </form>
</div>

<!-- Change Password -->
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Change Password</h3>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Current Password</label>
            <input type="password" name="current_password" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">New Password</label>
            <input type="password" name="new_password" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Confirm New Password</label>
            <input type="password" name="confirm_password" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        <button type="submit" name="change_password" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition">
            <i class="fas fa-key mr-2"></i>Change Password
        </button>
    </form>
</div>

<!-- Logout Button -->
<a href="?logout=true" class="block w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold text-center transition">
    <i class="fas fa-sign-out-alt mr-2"></i>Logout
</a>

<?php include 'common/bottom.php'; ?>
