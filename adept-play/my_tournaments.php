<?php
require_once 'common/config.php';
include 'common/header.php';

$user_id = $_SESSION['user_id'];
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming';

// Get upcoming/live tournaments
$upcoming_query = "SELECT t.*, p.joined_at 
                   FROM tournaments t 
                   INNER JOIN participants p ON t.id = p.tournament_id 
                   WHERE p.user_id = '$user_id' AND t.status IN ('Upcoming', 'Live') 
                   ORDER BY t.match_time ASC";
$upcoming_tournaments = mysqli_query($conn, $upcoming_query);

// Get completed tournaments
$completed_query = "SELECT t.*, p.joined_at, 
                    CASE WHEN t.winner_id = '$user_id' THEN 'Winner' ELSE 'Participated' END as result
                    FROM tournaments t 
                    INNER JOIN participants p ON t.id = p.tournament_id 
                    WHERE p.user_id = '$user_id' AND t.status = 'Completed' 
                    ORDER BY t.match_time DESC";
$completed_tournaments = mysqli_query($conn, $completed_query);
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold mb-2">My Tournaments</h2>
    <p class="text-gray-400 text-sm">Track your tournament participation</p>
</div>

<!-- Tab Buttons -->
<div class="flex gap-2 mb-6">
    <a href="?tab=upcoming" class="flex-1 py-3 text-center rounded-lg font-semibold transition <?php echo $activeTab == 'upcoming' ? 'bg-purple-600 text-white' : 'bg-slate-800 text-gray-400'; ?>">
        Upcoming/Live
    </a>
    <a href="?tab=completed" class="flex-1 py-3 text-center rounded-lg font-semibold transition <?php echo $activeTab == 'completed' ? 'bg-purple-600 text-white' : 'bg-slate-800 text-gray-400'; ?>">
        Completed
    </a>
</div>

<!-- Upcoming/Live Tab -->
<?php if ($activeTab == 'upcoming'): ?>
    <?php if (mysqli_num_rows($upcoming_tournaments) > 0): ?>
        <div class="space-y-4">
            <?php while ($tournament = mysqli_fetch_assoc($upcoming_tournaments)): ?>
                <div class="bg-slate-800 rounded-xl overflow-hidden shadow-lg border border-slate-700">
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold mb-1"><?php echo $tournament['title']; ?></h3>
                                <p class="text-purple-100 text-sm"><i class="fas fa-gamepad mr-1"></i><?php echo $tournament['game_name']; ?></p>
                            </div>
                            <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold">
                                <?php echo $tournament['status']; ?>
                            </span>
                        </div>
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
                        
                        <div class="bg-slate-700 rounded-lg p-3 mb-4">
                            <p class="text-xs text-gray-400 mb-1"><i class="fas fa-clock mr-1"></i>Match Time</p>
                            <p class="font-semibold"><?php echo date('d M Y, h:i A', strtotime($tournament['match_time'])); ?></p>
                        </div>
                        
                        <?php if ($tournament['status'] == 'Live' && $tournament['room_id']): ?>
                            <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 mb-4">
                                <p class="text-green-400 font-semibold mb-3 flex items-center">
                                    <i class="fas fa-circle animate-pulse mr-2"></i>Tournament is Live!
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Room ID</p>
                                        <p class="font-bold text-white"><?php echo $tournament['room_id']; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Password</p>
                                        <p class="font-bold text-white"><?php echo $tournament['room_password']; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-xs text-gray-400">
                            <i class="fas fa-calendar mr-1"></i>Joined on <?php echo date('d M Y', strtotime($tournament['joined_at'])); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="bg-slate-800 rounded-xl p-8 text-center">
            <i class="fas fa-trophy text-6xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 mb-4">You haven't joined any tournaments yet</p>
            <a href="index.php" class="inline-block bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition">
                Browse Tournaments
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Completed Tab -->
<?php if ($activeTab == 'completed'): ?>
    <?php if (mysqli_num_rows($completed_tournaments) > 0): ?>
        <div class="space-y-4">
            <?php while ($tournament = mysqli_fetch_assoc($completed_tournaments)): ?>
                <div class="bg-slate-800 rounded-xl overflow-hidden shadow-lg border border-slate-700">
                    <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold mb-1"><?php echo $tournament['title']; ?></h3>
                                <p class="text-gray-300 text-sm"><i class="fas fa-gamepad mr-1"></i><?php echo $tournament['game_name']; ?></p>
                            </div>
                            <?php if ($tournament['result'] == 'Winner'): ?>
                                <span class="bg-yellow-500 text-black px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-crown mr-1"></i>Winner
                                </span>
                            <?php else: ?>
                                <span class="bg-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                                    Participated
                                </span>
                            <?php endif; ?>
                        </div>
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
                        
                        <div class="text-xs text-gray-400">
                            <i class="fas fa-calendar mr-1"></i>Played on <?php echo date('d M Y', strtotime($tournament['match_time'])); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="bg-slate-800 rounded-xl p-8 text-center">
            <i class="fas fa-history text-6xl text-gray-600 mb-4"></i>
            <p class="text-gray-400">No completed tournaments yet</p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include 'common/bottom.php'; ?>
