<?php
require_once '../common/config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

$message = '';
$error = '';

// Get tournament ID
if (!isset($_GET['id'])) {
    redirect('tournament.php');
}

$tournament_id = sanitize($_GET['id']);

// Get tournament details
$tournament_query = "SELECT * FROM tournaments WHERE id = '$tournament_id'";
$tournament_result = mysqli_query($conn, $tournament_query);

if (mysqli_num_rows($tournament_result) == 0) {
    redirect('tournament.php');
}

$tournament = mysqli_fetch_assoc($tournament_result);

// Handle room details update
if (isset($_POST['update_room'])) {
    $room_id = sanitize($_POST['room_id']);
    $room_password = sanitize($_POST['room_password']);
    $status = sanitize($_POST['status']);
    
    $query = "UPDATE tournaments SET room_id = '$room_id', room_password = '$room_password', status = '$status' WHERE id = '$tournament_id'";
    
    if (mysqli_query($conn, $query)) {
        $message = "Room details updated successfully!";
        $tournament['room_id'] = $room_id;
        $tournament['room_password'] = $room_password;
        $tournament['status'] = $status;
    } else {
        $error = "Error updating room details!";
    }
}

// Handle winner declaration
if (isset($_POST['declare_winner'])) {
    $winner_id = sanitize($_POST['winner_id']);
    
    if (empty($winner_id)) {
        $error = "Please select a winner!";
    } else {
        // Update tournament status and winner
        $update_query = "UPDATE tournaments SET status = 'Completed', winner_id = '$winner_id' WHERE id = '$tournament_id'";
        mysqli_query($conn, $update_query);
        
        // Add prize to winner's wallet
        $prize_pool = $tournament['prize_pool'];
        $update_wallet_query = "UPDATE users SET wallet_balance = wallet_balance + $prize_pool WHERE id = '$winner_id'";
        mysqli_query($conn, $update_wallet_query);
        
        // Add transaction
        $trans_query = "INSERT INTO transactions (user_id, amount, type, description) 
                        VALUES ('$winner_id', '$prize_pool', 'credit', 'Won tournament: {$tournament['title']}')";
        mysqli_query($conn, $trans_query);
        
        $message = "Winner declared and prize distributed successfully!";
        $tournament['status'] = 'Completed';
        $tournament['winner_id'] = $winner_id;
    }
}

include 'common/header.php';

// Get participants
$participants_query = "SELECT u.id, u.username, u.email, p.joined_at 
                       FROM participants p 
                       INNER JOIN users u ON p.user_id = u.id 
                       WHERE p.tournament_id = '$tournament_id' 
                       ORDER BY p.joined_at ASC";
$participants = mysqli_query($conn, $participants_query);
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
    <a href="tournament.php" class="text-orange-400 hover:text-orange-300 text-sm mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i>Back to Tournaments
    </a>
    <h2 class="text-2xl font-bold mb-2">Manage Tournament</h2>
    <p class="text-gray-400 text-sm">Manage participants and declare winner</p>
</div>

<!-- Tournament Details -->
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-2xl font-bold mb-2"><?php echo $tournament['title']; ?></h3>
            <p class="text-gray-400"><i class="fas fa-gamepad mr-1"></i><?php echo $tournament['game_name']; ?></p>
        </div>
        <span class="px-4 py-2 rounded-lg font-semibold <?php 
            echo $tournament['status'] == 'Upcoming' ? 'bg-blue-500/20 text-blue-400' : 
                ($tournament['status'] == 'Live' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'); 
        ?>">
            <?php echo $tournament['status']; ?>
        </span>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-700 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1">Entry Fee</p>
            <p class="text-lg font-bold text-yellow-400"><?php echo formatCurrency($tournament['entry_fee']); ?></p>
        </div>
        <div class="bg-slate-700 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1">Prize Pool</p>
            <p class="text-lg font-bold text-green-400"><?php echo formatCurrency($tournament['prize_pool']); ?></p>
        </div>
        <div class="bg-slate-700 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1">Commission</p>
            <p class="text-lg font-bold text-orange-400"><?php echo $tournament['commission_percentage']; ?>%</p>
        </div>
        <div class="bg-slate-700 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1">Match Time</p>
            <p class="text-sm font-bold"><?php echo date('d M Y, h:i A', strtotime($tournament['match_time'])); ?></p>
        </div>
    </div>
</div>

<!-- Room Details Form -->
<?php if ($tournament['status'] != 'Completed'): ?>
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Room Details</h3>
    <form method="POST" action="">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Room ID</label>
                <input type="text" name="room_id" value="<?php echo $tournament['room_id'] ?? ''; ?>" class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Room Password</label>
                <input type="text" name="room_password" value="<?php echo $tournament['room_password'] ?? ''; ?>" class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
                    <option value="Upcoming" <?php echo $tournament['status'] == 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="Live" <?php echo $tournament['status'] == 'Live' ? 'selected' : ''; ?>>Live</option>
                </select>
            </div>
        </div>
        <button type="submit" name="update_room" class="mt-4 w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-700 transition shadow-lg">
            <i class="fas fa-save mr-2"></i>Update Room Details
        </button>
    </form>
</div>
<?php endif; ?>

<!-- Participants List -->
<div class="bg-slate-800 rounded-xl p-6 mb-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Participants (<?php echo mysqli_num_rows($participants); ?>)</h3>
    
    <?php if (mysqli_num_rows($participants) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">ID</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Username</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Email</th>
                        <th class="text-left py-3 px-2 text-sm font-semibold text-gray-400">Joined At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($participants, 0);
                    while ($participant = mysqli_fetch_assoc($participants)): 
                    ?>
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                            <td class="py-3 px-2 text-sm"><?php echo $participant['id']; ?></td>
                            <td class="py-3 px-2 text-sm font-semibold"><?php echo $participant['username']; ?></td>
                            <td class="py-3 px-2 text-sm text-gray-400"><?php echo $participant['email']; ?></td>
                            <td class="py-3 px-2 text-sm text-gray-400"><?php echo date('d M Y, h:i A', strtotime($participant['joined_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-400 text-center py-8">No participants yet</p>
    <?php endif; ?>
</div>

<!-- Declare Winner -->
<?php if ($tournament['status'] != 'Completed' && mysqli_num_rows($participants) > 0): ?>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
    <h3 class="text-xl font-bold mb-4">Declare Winner</h3>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Select Winner</label>
            <select name="winner_id" required class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600">
                <option value="">-- Select Winner --</option>
                <?php 
                mysqli_data_seek($participants, 0);
                while ($participant = mysqli_fetch_assoc($participants)): 
                ?>
                    <option value="<?php echo $participant['id']; ?>"><?php echo $participant['username']; ?> (<?php echo $participant['email']; ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="declare_winner" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition shadow-lg">
            <i class="fas fa-crown mr-2"></i>Declare Winner & Distribute Prize
        </button>
    </form>
</div>
<?php elseif ($tournament['status'] == 'Completed'): ?>
<div class="bg-green-500/20 border border-green-500 rounded-xl p-6">
    <h3 class="text-xl font-bold mb-2 text-green-400"><i class="fas fa-check-circle mr-2"></i>Tournament Completed</h3>
    <?php
    if ($tournament['winner_id']) {
        $winner_query = "SELECT username FROM users WHERE id = '{$tournament['winner_id']}'";
        $winner_result = mysqli_query($conn, $winner_query);
        $winner = mysqli_fetch_assoc($winner_result);
        echo "<p class='text-green-300'>Winner: <strong>{$winner['username']}</strong></p>";
        echo "<p class='text-green-300'>Prize Distributed: <strong>" . formatCurrency($tournament['prize_pool']) . "</strong></p>";
    }
    ?>
</div>
<?php endif; ?>

<?php include 'common/bottom.php'; ?>
