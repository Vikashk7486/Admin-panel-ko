    </main>
    
    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-slate-800 border-t border-slate-700 shadow-lg">
        <div class="max-w-md mx-auto flex justify-around items-center py-3">
            <a href="index.php" class="flex flex-col items-center text-center px-3 py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-purple-400' : 'text-gray-400'; ?> hover:text-purple-400 transition">
                <i class="fas fa-home text-xl mb-1"></i>
                <span class="text-xs">Home</span>
            </a>
            <a href="my_tournaments.php" class="flex flex-col items-center text-center px-3 py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'my_tournaments.php' ? 'text-purple-400' : 'text-gray-400'; ?> hover:text-purple-400 transition">
                <i class="fas fa-trophy text-xl mb-1"></i>
                <span class="text-xs">My Tournaments</span>
            </a>
            <a href="wallet.php" class="flex flex-col items-center text-center px-3 py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'wallet.php' ? 'text-purple-400' : 'text-gray-400'; ?> hover:text-purple-400 transition">
                <i class="fas fa-wallet text-xl mb-1"></i>
                <span class="text-xs">Wallet</span>
            </a>
            <a href="profile.php" class="flex flex-col items-center text-center px-3 py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'text-purple-400' : 'text-gray-400'; ?> hover:text-purple-400 transition">
                <i class="fas fa-user text-xl mb-1"></i>
                <span class="text-xs">Profile</span>
            </a>
        </div>
    </nav>
</body>
</html>
