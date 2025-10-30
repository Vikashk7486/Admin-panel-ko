<?php
require_once '../common/config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

include 'common/header.php';

// Get all users
$users_query = "SELECT u.*, 
                COUNT(DISTINCT p.tournament_id) as total_tournaments,
                COUNT(DISTINCT CASE WHEN t.winner_id = u.id THEN t.id END) as total_wins
                FROM users u
                LEFT JOIN participants p ON u.id = p.user_id
                LEFT JOIN tournaments t ON t.winner_id = u.id
                GROUP BY u.id
                ORDER BY u.created_at DESC";
$users = mysqli_query($conn, $users_query);
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold mb-2">User Management</h2>
    <p class="text-gray-400 text-sm">View and manage all registered users</p>
</div>

<!-- Users List -->
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">All Users (<?php echo mysqli_num_rows($users); ?>)</h3>
    
    <?php if (mysqli_num_rows($users) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">ID</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Username</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Email</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Wallet Balance</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Tournaments</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Wins</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                            <td class="py-3 px-2 text-sm"><?php echo $user['id']; ?></td>
                            <td class="py-3 px-2 text-sm font-semibold"><?php echo $user['username']; ?></td>
                            <td class="py-3 px-2 text-sm text-gray-400"><?php echo $user['email']; ?></td>
                            <td class="py-3 px-2 text-sm">
                                <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded font-semibold">
                                    <?php echo formatCurrency($user['wallet_balance']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-2 text-sm text-center">
                                <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded">
                                    <?php echo $user['total_tournaments']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-2 text-sm text-center">
                                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded">
                                    <?php echo $user['total_wins']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-2 text-sm text-gray-400"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-400 text-center py-8">No users registered yet</p>
    <?php endif; ?>
</div>

<?php include 'common/bottom.php'; ?>
