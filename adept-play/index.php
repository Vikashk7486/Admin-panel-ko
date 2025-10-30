<?php
require_once 'common/config.php';

$message = '';
$error = '';

// Handle tournament join
if (isset($_POST['join_tournament'])) {
    $tournament_id = sanitize($_POST['tournament_id']);
    $user_id = $_SESSION['user_id'];
    
    // Get tournament details
    $query = "SELECT * FROM tournaments WHERE id = '$tournament_id' AND status = 'Upcoming'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $tournament = mysqli_fetch_assoc($result);
        
        // Check if user already joined
        $check_query = "SELECT * FROM participants WHERE user_id = '$user_id' AND tournament_id = '$tournament_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "You have already joined this tournament!";
        } else {
            // Check user balance
            $user_query = "SELECT wallet_balance FROM users WHERE id = '$user_id'";
            $user_result = mysqli_query($conn, $user_query);
            $user = mysqli_fetch_assoc($user_result);
            
            if ($user['wallet_balance'] < $tournament['entry_fee']) {
                $error = "Insufficient balance! Please add money to your wallet.";
            } else {
                // Deduct entry fee
                $new_balance = $user['wallet_balance'] - $tournament['entry_fee'];
                $update_query = "UPDATE users SET wallet_balance = '$new_balance' WHERE id = '$user_id'";
                mysqli_query($conn, $update_query);
                
                // Add to participants
                $insert_query = "INSERT INTO participants (user_id, tournament_id) VALUES ('$user_id', '$tournament_id')";
                mysqli_query($conn, $insert_query);
                
                // Add transaction
                $trans_query = "INSERT INTO transactions (user_id, amount, type, description) VALUES ('$user_id', '{$tournament['entry_fee']}', 'debit', 'Joined tournament: {$tournament['title']}')";
                mysqli_query($conn, $trans_query);
                
                $message = "Successfully joined the tournament!";
            }
        }
    } else {
        $error = "Tournament not found or already started!";
    }
}

include 'common/header.php';

// Get all upcoming tournaments
$query = "SELECT * FROM tournaments WHERE status = 'Upcoming' ORDER BY match_time ASC";
$tournaments = mysqli_query($conn, $query);
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
    <h2 class="text-2xl font-bold mb-2">Upcoming Tournaments</h2>
    <p class="text-gray-400 text-sm">Join now and win exciting prizes!</p>
</div>

<?php if (mysqli_num_rows($tournaments) > 0): ?>
    <div class="space-y-4">
        <?php while ($tournament = mysqli_fetch_assoc($tournaments)): ?>
            <?php
            // Check if user already joined
            $check_query = "SELECT * FROM participants WHERE user_id = '{$_SESSION['user_id']}' AND tournament_id = '{$tournament['id']}'";
            $check_result = mysqli_query($conn, $check_query);
            $already_joined = mysqli_num_rows($check_result) > 0;
            
            // Count participants
            $count_query = "SELECT COUNT(*) as count FROM participants WHERE tournament_id = '{$tournament['id']}'";
            $count_result = mysqli_query($conn, $count_query);
            $count = mysqli_fetch_assoc($count_result)['count'];
            ?>
            
            <div class="bg-slate-800 rounded-xl overflow-hidden shadow-lg border border-slate-700">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-4">
                    <h3 class="text-xl font-bold mb-1"><?php echo $tournament['title']; ?></h3>
                    <p class="text-purple-100 text-sm"><i class="fas fa-gamepad mr-1"></i><?php echo $tournament['game_name']; ?></p>
                </div>
                
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-slate-700 rounded-lg p-3">
                            <p class="text-xs text-gray-400 mb-1">Entry Fee</p>
                            <p class="text-lg font-bold text-yellow-400"><?php echo formatCurrency($tournament['entry_fee']); ?></p>
                        </div>
                        <div class="bg-slate-700 rounded-lg p-3">
                            <p class="text-xs text-gray-400 mb-1">Prize Pool</p>
                            <p class="text-lg font-bold text-green-400"><?php echo formatCurrency($tournament['prize_pool']); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-4 text-sm">
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-clock mr-2"></i>
                            <?php echo date('d M Y, h:i A', strtotime($tournament['match_time'])); ?>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-users mr-2"></i>
                            <?php echo $count; ?> joined
                        </div>
                    </div>
                    
                    <?php if ($already_joined): ?>
                        <button disabled class="w-full bg-gray-600 text-white py-3 rounded-lg font-semibold cursor-not-allowed">
                            <i class="fas fa-check mr-2"></i>Already Joined
                        </button>
                    <?php else: ?>
                        <form method="POST" action="">
                            <input type="hidden" name="tournament_id" value="<?php echo $tournament['id']; ?>">
                            <button type="submit" name="join_tournament" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition shadow-lg">
                                <i class="fas fa-sign-in-alt mr-2"></i>Join Now
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="bg-slate-800 rounded-xl p-8 text-center">
        <i class="fas fa-trophy text-6xl text-gray-600 mb-4"></i>
        <p class="text-gray-400">No upcoming tournaments available</p>
    </div>
<?php endif; ?>

<?php include 'common/bottom.php'; ?>
