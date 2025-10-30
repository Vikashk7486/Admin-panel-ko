<?php
require_once '../common/config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

include 'common/header.php';

// Get statistics
$total_users_query = "SELECT COUNT(*) as count FROM users";
$total_users = mysqli_fetch_assoc(mysqli_query($conn, $total_users_query))['count'];

$total_tournaments_query = "SELECT COUNT(*) as count FROM tournaments";
$total_tournaments = mysqli_fetch_assoc(mysqli_query($conn, $total_tournaments_query))['count'];

$total_prize_query = "SELECT SUM(prize_pool) as total FROM tournaments WHERE status = 'Completed'";
$total_prize_result = mysqli_query($conn, $total_prize_query);
$total_prize = mysqli_fetch_assoc($total_prize_result)['total'] ?? 0;

$total_revenue_query = "SELECT SUM(t.prize_pool * t.commission_percentage / 100) as revenue 
                        FROM tournaments t WHERE status = 'Completed'";
$total_revenue_result = mysqli_query($conn, $total_revenue_query);
$total_revenue = mysqli_fetch_assoc($total_revenue_result)['revenue'] ?? 0;

// Get recent tournaments
$recent_tournaments_query = "SELECT * FROM tournaments ORDER BY created_at DESC LIMIT 5";
$recent_tournaments = mysqli_query($conn, $recent_tournaments_query);
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold mb-2">Dashboard</h2>
    <p class="text-gray-400 text-sm">Overview of your tournament platform</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-users text-3xl text-blue-200"></i>
            <span class="bg-white/20 px-2 py-1 rounded text-xs">Total</span>
        </div>
        <p class="text-3xl font-bold mb-1"><?php echo $total_users; ?></p>
        <p class="text-blue-100 text-sm">Total Users</p>
    </div>
    
    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-trophy text-3xl text-purple-200"></i>
            <span class="bg-white/20 px-2 py-1 rounded text-xs">Total</span>
        </div>
        <p class="text-3xl font-bold mb-1"><?php echo $total_tournaments; ?></p>
        <p class="text-purple-100 text-sm">Total Tournaments</p>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-gift text-3xl text-green-200"></i>
            <span class="bg-white/20 px-2 py-1 rounded text-xs">Distributed</span>
        </div>
        <p class="text-3xl font-bold mb-1"><?php echo formatCurrency($total_prize); ?></p>
        <p class="text-green-100 text-sm">Total Prize Distributed</p>
    </div>
    
    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-coins text-3xl text-orange-200"></i>
            <span class="bg-white/20 px-2 py-1 rounded text-xs">Earned</span>
        </div>
        <p class="text-3xl font-bold mb-1"><?php echo formatCurrency($total_revenue); ?></p>
        <p class="text-orange-100 text-sm">Total Revenue</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-slate-800 rounded-xl p-6 mb-8 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="tournament.php" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white p-4 rounded-lg text-center font-semibold transition shadow-lg">
            <i class="fas fa-plus-circle text-2xl mb-2 block"></i>
            Create New Tournament
        </a>
        <a href="user.php" class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white p-4 rounded-lg text-center font-semibold transition shadow-lg">
            <i class="fas fa-users text-2xl mb-2 block"></i>
            Manage Users
        </a>
        <a href="tournament.php" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white p-4 rounded-lg text-center font-semibold transition shadow-lg">
            <i class="fas fa-trophy text-2xl mb-2 block"></i>
            View All Tournaments
        </a>
    </div>
</div>

<!-- Recent Tournaments -->
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Recent Tournaments</h3>
    
    <?php if (mysqli_num_rows($recent_tournaments) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Title</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Game</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Entry Fee</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Prize</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($tournament = mysqli_fetch_assoc($recent_tournaments)): ?>
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                            <td class="py-3 px-2 text-sm"><?php echo $tournament['title']; ?></td>
                            <td class="py-3 px-2 text-sm text-gray-400"><?php echo $tournament['game_name']; ?></td>
                            <td class="py-3 px-2 text-sm text-yellow-400"><?php echo formatCurrency($tournament['entry_fee']); ?></td>
                            <td class="py-3 px-2 text-sm text-green-400"><?php echo formatCurrency($tournament['prize_pool']); ?></td>
                            <td class="py-3 px-2 text-sm">
                                <?php if ($tournament['status'] == 'Upcoming'): ?>
                                    <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">Upcoming</span>
                                <?php elseif ($tournament['status'] == 'Live'): ?>
                                    <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">Live</span>
                                <?php else: ?>
                                    <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-2 text-sm">
                                <a href="manage_tournament.php?id=<?php echo $tournament['id']; ?>" class="text-orange-400 hover:text-orange-300">
                                    <i class="fas fa-cog"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-400 text-center py-8">No tournaments created yet</p>
    <?php endif; ?>
</div>

<?php include 'common/bottom.php'; ?>
