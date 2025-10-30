<?php
require_once '../common/config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

$message = '';
$error = '';

// Handle tournament creation
if (isset($_POST['create_tournament'])) {
    $title = sanitize($_POST['title']);
    $game_name = sanitize($_POST['game_name']);
    $entry_fee = sanitize($_POST['entry_fee']);
    $prize_pool = sanitize($_POST['prize_pool']);
    $commission_percentage = sanitize($_POST['commission_percentage']);
    $match_time = sanitize($_POST['match_time']);
    
    if (empty($title) || empty($game_name) || empty($entry_fee) || empty($prize_pool) || empty($match_time)) {
        $error = "All fields are required!";
    } else {
        $query = "INSERT INTO tournaments (title, game_name, entry_fee, prize_pool, commission_percentage, match_time, status) 
                  VALUES ('$title', '$game_name', '$entry_fee', '$prize_pool', '$commission_percentage', '$match_time', 'Upcoming')";
        
        if (mysqli_query($conn, $query)) {
            $message = "Tournament created successfully!";
        } else {
            $error = "Error creating tournament: " . mysqli_error($conn);
        }
    }
}

// Handle tournament deletion
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $query = "DELETE FROM tournaments WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        $message = "Tournament deleted successfully!";
    } else {
        $error = "Error deleting tournament!";
    }
}

include 'common/header.php';

// Get all tournaments
$tournaments_query = "SELECT * FROM tournaments ORDER BY created_at DESC";
$tournaments = mysqli_query($conn, $tournaments_query);
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
    <h2 class="text-2xl font-bold mb-2">Tournament Management</h2>
    <p class="text-gray-400 text-sm">Create and manage tournaments</p>
</div>

<!-- Create Tournament Form -->
<div class="bg-slate-800 rounded-xl p-6 mb-8 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Create New Tournament</h3>
    <form method="POST" action="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Tournament Title</label>
                <input type="text" name="title" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Game Name</label>
                <input type="text" name="game_name" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Entry Fee (₹)</label>
                <input type="number" step="0.01" name="entry_fee" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Prize Pool (₹)</label>
                <input type="number" step="0.01" name="prize_pool" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Commission Percentage (%)</label>
                <input type="number" step="0.01" name="commission_percentage" value="20" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Match Time</label>
                <input type="datetime-local" name="match_time" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
        </div>
        <button type="submit" name="create_tournament" class="mt-4 w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>Create Tournament
        </button>
    </form>
</div>

<!-- All Tournaments List -->
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">All Tournaments</h3>
    
    <?php if (mysqli_num_rows($tournaments) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">ID</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Title</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Game</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Entry Fee</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Prize Pool</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Match Time</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($tournament = mysqli_fetch_assoc($tournaments)): ?>
                        <?php
                        // Count participants
                        $count_query = "SELECT COUNT(*) as count FROM participants WHERE tournament_id = '{$tournament['id']}'";
                        $count_result = mysqli_query($conn, $count_query);
                        $participant_count = mysqli_fetch_assoc($count_result)['count'];
                        ?>
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                            <td class="py-3 px-2 text-sm"><?php echo $tournament['id']; ?></td>
                            <td class="py-3 px-2 text-sm font-semibold"><?php echo $tournament['title']; ?></td>
                            <td class="py-3 px-2 text-sm text-gray-400"><?php echo $tournament['game_name']; ?></td>
                            <td class="py-3 px-2 text-sm text-yellow-400"><?php echo formatCurrency($tournament['entry_fee']); ?></td>
                            <td class="py-3 px-2 text-sm text-green-400"><?php echo formatCurrency($tournament['prize_pool']); ?></td>
                            <td class="py-3 px-2 text-sm text-gray-400"><?php echo date('d M Y, h:i A', strtotime($tournament['match_time'])); ?></td>
                            <td class="py-3 px-2 text-sm">
                                <?php if ($tournament['status'] == 'Upcoming'): ?>
                                    <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">Upcoming</span>
                                <?php elseif ($tournament['status'] == 'Live'): ?>
                                    <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">Live</span>
                                <?php else: ?>
                                    <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs">Completed</span>
                                <?php endif; ?>
                                <div class="text-xs text-gray-500 mt-1"><?php echo $participant_count; ?> participants</div>
                            </td>
                            <td class="py-3 px-2 text-sm">
                                <div class="flex gap-2">
                                    <a href="manage_tournament.php?id=<?php echo $tournament['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition">
                                        <i class="fas fa-cog"></i> Manage
                                    </a>
                                    <a href="?delete=<?php echo $tournament['id']; ?>" onclick="return confirm('Are you sure you want to delete this tournament?')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
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
