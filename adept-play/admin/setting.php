<?php
require_once '../common/config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

$message = '';
$error = '';
$admin_id = $_SESSION['admin_id'];

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = sanitize($_POST['username']);
    
    if (empty($username)) {
        $error = "Username is required!";
    } else {
        // Check if username already exists for other admins
        $check_query = "SELECT * FROM admin WHERE username = '$username' AND id != '$admin_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username already exists!";
        } else {
            $query = "UPDATE admin SET username = '$username' WHERE id = '$admin_id'";
            if (mysqli_query($conn, $query)) {
                $_SESSION['admin_username'] = $username;
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
        $query = "SELECT password FROM admin WHERE id = '$admin_id'";
        $result = mysqli_query($conn, $query);
        $admin = mysqli_fetch_assoc($result);
        
        if (password_verify($current_password, $admin['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE admin SET password = '$hashed_password' WHERE id = '$admin_id'";
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

include 'common/header.php';

// Get admin details
$query = "SELECT * FROM admin WHERE id = '$admin_id'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);
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
    <h2 class="text-2xl font-bold mb-2">Admin Settings</h2>
    <p class="text-gray-400 text-sm">Manage your admin account</p>
</div>

<!-- Admin Profile Card -->
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <div class="flex items-center mb-4">
        <div class="bg-gradient-to-r from-red-600 to-orange-600 w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mr-4">
            <i class="fas fa-user-shield"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold"><?php echo $admin['username']; ?></h3>
            <p class="text-gray-400">Administrator</p>
        </div>
    </div>
</div>

<!-- Update Profile -->
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Update Profile</h3>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Username</label>
            <input type="text" name="username" value="<?php echo $admin['username']; ?>" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
        </div>
        <button type="submit" name="update_profile" class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-700 transition shadow-lg">
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
            <input type="password" name="current_password" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">New Password</label>
            <input type="password" name="new_password" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Confirm New Password</label>
            <input type="password" name="confirm_password" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
        </div>
        <button type="submit" name="change_password" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition shadow-lg">
            <i class="fas fa-key mr-2"></i>Change Password
        </button>
    </form>
</div>

<!-- System Information -->
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">System Information</h3>
    <div class="space-y-3">
        <div class="flex justify-between items-center py-2 border-b border-slate-700">
            <span class="text-gray-400">Application Name</span>
            <span class="font-semibold">Adept Play</span>
        </div>
        <div class="flex justify-between items-center py-2 border-b border-slate-700">
            <span class="text-gray-400">Version</span>
            <span class="font-semibold">1.0.0</span>
        </div>
        <div class="flex justify-between items-center py-2 border-b border-slate-700">
            <span class="text-gray-400">Database</span>
            <span class="font-semibold">MySQL</span>
        </div>
        <div class="flex justify-between items-center py-2">
            <span class="text-gray-400">PHP Version</span>
            <span class="font-semibold"><?php echo phpversion(); ?></span>
        </div>
    </div>
</div>

<?php include 'common/bottom.php'; ?>
