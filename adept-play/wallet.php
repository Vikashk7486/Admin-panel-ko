<?php
require_once 'common/config.php';
include 'common/header.php';

$user_id = $_SESSION['user_id'];

// Get user wallet balance
$query = "SELECT wallet_balance FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get transaction history
$trans_query = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 50";
$transactions = mysqli_query($conn, $trans_query);
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold mb-2">My Wallet</h2>
    <p class="text-gray-400 text-sm">Manage your balance and transactions</p>
</div>

<!-- Wallet Balance Card -->
<div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl p-6 mb-6 shadow-2xl">
    <p class="text-purple-100 text-sm mb-2">Available Balance</p>
    <h3 class="text-4xl font-bold mb-6"><?php echo formatCurrency($user['wallet_balance']); ?></h3>
    
    <div class="grid grid-cols-2 gap-3">
        <button class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white py-3 rounded-lg font-semibold transition">
            <i class="fas fa-plus-circle mr-2"></i>Add Money
        </button>
        <button class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white py-3 rounded-lg font-semibold transition">
            <i class="fas fa-arrow-circle-up mr-2"></i>Withdraw
        </button>
    </div>
</div>

<!-- Transaction History -->
<div class="mb-6">
    <h3 class="text-xl font-bold mb-4">Transaction History</h3>
    
    <?php if (mysqli_num_rows($transactions) > 0): ?>
        <div class="space-y-3">
            <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                <div class="bg-slate-800 rounded-lg p-4 border border-slate-700">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <p class="font-semibold mb-1"><?php echo $transaction['description']; ?></p>
                            <p class="text-xs text-gray-400">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo date('d M Y, h:i A', strtotime($transaction['created_at'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <?php if ($transaction['type'] == 'credit'): ?>
                                <p class="text-lg font-bold text-green-400">
                                    +<?php echo formatCurrency($transaction['amount']); ?>
                                </p>
                                <span class="inline-block bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">
                                    Credit
                                </span>
                            <?php else: ?>
                                <p class="text-lg font-bold text-red-400">
                                    -<?php echo formatCurrency($transaction['amount']); ?>
                                </p>
                                <span class="inline-block bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs">
                                    Debit
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="bg-slate-800 rounded-xl p-8 text-center">
            <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
            <p class="text-gray-400">No transactions yet</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'common/bottom.php'; ?>
