# 🔧 ADEPT PLAY - TROUBLESHOOTING GUIDE

## 🚨 Common Issues & Solutions

---

## Issue #1: 404 Error on Vercel

### Problem:
```
Problem 404: NOT_FOUND
Code: 'NOT_FOUND
ID: `bom1::ndln8-1761846198089-900b2fb302f0`
```

### ✅ Solution:
**This is expected!** Adept Play is a PHP application and **cannot run on Vercel**.

**Why?**
- Vercel is designed for Node.js/Next.js applications
- Adept Play requires PHP runtime and MySQL database
- Vercel doesn't support traditional PHP applications

**What to do:**
1. **Use XAMPP locally** (recommended)
2. **Deploy to PHP hosting** (for production)
3. See `HOW_TO_RUN_ADEPT_PLAY.md` for detailed instructions

---

## Issue #2: Database Connection Failed

### Error Message:
```
Connection failed: Can't connect to MySQL server
```

### ✅ Solutions:

**Solution A: MySQL Not Running**
```bash
# Check if MySQL is running
# For XAMPP: Open XAMPP Control Panel and start MySQL
# For standalone MySQL:
sudo service mysql start  # Linux
mysql.server start        # Mac
```

**Solution B: Wrong Credentials**
1. Open `adept-play/common/config.php`
2. Check these lines:
```php
define('DB_HOST', '127.0.0.1');  // Usually correct
define('DB_USER', 'root');        // Change if different
define('DB_PASS', 'root');        // Change if different
define('DB_NAME', 'adept_play');  // Database name
```
3. Update with your MySQL credentials

**Solution C: MySQL Port Issue**
```php
// If MySQL runs on different port (e.g., 3307)
define('DB_HOST', '127.0.0.1:3307');
```

---

## Issue #3: Installation Fails

### Error Message:
```
Error creating database: Access denied
```

### ✅ Solutions:

**Solution A: Grant Permissions**
```sql
-- Login to MySQL as root
mysql -u root -p

-- Grant all privileges
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

**Solution B: Create Database Manually**
```sql
-- Login to MySQL
mysql -u root -p

-- Create database
CREATE DATABASE adept_play;

-- Then run install.php again
```

**Solution C: Check PHP MySQL Extension**
```bash
# Check if mysqli extension is enabled
php -m | grep mysqli

# If not found, enable it in php.ini:
# Remove semicolon from: ;extension=mysqli
```

---

## Issue #4: Blank White Page

### Problem:
Page loads but shows nothing (blank white screen)

### ✅ Solutions:

**Solution A: Enable Error Display**
1. Open `adept-play/common/config.php`
2. Add at the top:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Solution B: Check PHP Error Log**
```bash
# XAMPP error log location:
# Windows: C:\xampp\apache\logs\error.log
# Mac: /Applications/XAMPP/logs/error_log
# Linux: /opt/lampp/logs/error_log

# View last 50 lines:
tail -n 50 /path/to/error.log
```

**Solution C: Check File Permissions**
```bash
# Make sure files are readable
chmod -R 755 adept-play/
```

---

## Issue #5: Can't Join Tournament

### Error Message:
```
Insufficient balance! Please add money to your wallet.
```

### ✅ Solution:

**Add Balance Manually (for testing):**

```sql
-- Login to MySQL
mysql -u root -p

-- Use the database
USE adept_play;

-- Check user ID
SELECT id, username, wallet_balance FROM users;

-- Add balance (e.g., ₹1000 to user ID 1)
UPDATE users SET wallet_balance = 1000.00 WHERE id = 1;

-- Verify
SELECT id, username, wallet_balance FROM users;
```

**Or via phpMyAdmin:**
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `adept_play` database
3. Click `users` table
4. Click "Edit" on your user
5. Change `wallet_balance` to `1000.00`
6. Click "Go"

---

## Issue #6: Room ID Not Showing

### Problem:
Joined tournament but can't see Room ID and Password

### ✅ Solution:

**Admin must set room details:**
1. Login to admin panel
2. Go to "Tournaments"
3. Click "Manage" on the tournament
4. Enter Room ID and Password
5. Change status to "Live"
6. Click "Update Room Details"

**Now users will see:**
- Room ID in "My Tournaments" > "Upcoming/Live" tab

---

## Issue #7: Winner Not Getting Prize

### Problem:
Declared winner but prize not added to wallet

### ✅ Solution:

**Check these steps:**

1. **Verify winner was selected:**
```sql
SELECT id, title, winner_id, status FROM tournaments WHERE id = YOUR_TOURNAMENT_ID;
```

2. **Check if prize was added:**
```sql
SELECT * FROM transactions WHERE user_id = WINNER_USER_ID ORDER BY created_at DESC LIMIT 5;
```

3. **Manually add prize (if needed):**
```sql
-- Get tournament prize
SELECT prize_pool FROM tournaments WHERE id = YOUR_TOURNAMENT_ID;

-- Add to winner's wallet
UPDATE users SET wallet_balance = wallet_balance + PRIZE_AMOUNT WHERE id = WINNER_USER_ID;

-- Add transaction record
INSERT INTO transactions (user_id, amount, type, description) 
VALUES (WINNER_USER_ID, PRIZE_AMOUNT, 'credit', 'Won tournament: TOURNAMENT_NAME');
```

---

## Issue #8: .htaccess Not Working

### Error Message:
```
Internal Server Error (500)
```

### ✅ Solutions:

**Solution A: Enable mod_rewrite**
```bash
# For XAMPP, it's usually enabled by default
# For standalone Apache:
sudo a2enmod rewrite
sudo service apache2 restart
```

**Solution B: Allow .htaccess Override**
1. Open Apache config: `httpd.conf` or `apache2.conf`
2. Find `<Directory>` section for your htdocs
3. Change:
```apache
AllowOverride None
```
to:
```apache
AllowOverride All
```
4. Restart Apache

**Solution C: Remove .htaccess (temporary)**
```bash
# Rename .htaccess to disable it
mv adept-play/.htaccess adept-play/.htaccess.bak

# App will still work, just without .htaccess features
```

---

## Issue #9: Session Not Working

### Problem:
Login successful but redirects back to login page

### ✅ Solutions:

**Solution A: Check Session Path**
```php
// Add to common/config.php (before session_start)
ini_set('session.save_path', sys_get_temp_dir());
```

**Solution B: Check Cookies**
1. Clear browser cookies
2. Try in incognito/private mode
3. Check browser console for errors

**Solution C: Verify Session Start**
```php
// In common/config.php, verify this line exists:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

---

## Issue #10: Mobile View Not Working

### Problem:
Design looks broken on mobile

### ✅ Solutions:

**Solution A: Clear Cache**
1. Clear browser cache
2. Hard refresh: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)

**Solution B: Check Viewport Meta Tag**
- Should be in header.php:
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
```

**Solution C: Test in Browser DevTools**
1. Open DevTools (F12)
2. Click device toolbar icon
3. Select mobile device
4. Refresh page

---

## Issue #11: Tailwind CSS Not Loading

### Problem:
Page has no styling, looks plain HTML

### ✅ Solutions:

**Solution A: Check Internet Connection**
- Tailwind is loaded from CDN
- Requires internet connection
- Check this line in header.php:
```html
<script src="https://cdn.tailwindcss.com"></script>
```

**Solution B: Use Local Tailwind (offline)**
1. Download Tailwind CSS
2. Save to `adept-play/assets/css/tailwind.css`
3. Update header.php:
```html
<link rel="stylesheet" href="assets/css/tailwind.css">
```

---

## Issue #12: Font Awesome Icons Not Showing

### Problem:
Icons appear as squares or not at all

### ✅ Solutions:

**Solution A: Check CDN Link**
```html
<!-- Should be in header.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

**Solution B: Clear Cache**
1. Clear browser cache
2. Hard refresh page

**Solution C: Check Internet Connection**
- Font Awesome loads from CDN
- Requires internet connection

---

## Issue #13: Can't Access Admin Panel

### Problem:
Admin login page not found

### ✅ Solutions:

**Solution A: Check URL**
```
Correct URL: http://localhost/adept-play/admin/login.php
NOT: http://localhost/admin/login.php
```

**Solution B: Verify Files Exist**
```bash
ls -la adept-play/admin/
# Should show: login.php, index.php, etc.
```

**Solution C: Check File Permissions**
```bash
chmod 755 adept-play/admin/
chmod 644 adept-play/admin/*.php
```

---

## Issue #14: Password Not Working

### Problem:
"Invalid password" error with correct password

### ✅ Solutions:

**Solution A: Reset Admin Password**
```sql
-- Login to MySQL
mysql -u root -p

USE adept_play;

-- Generate new password hash for "admin123"
-- Use this hash:
UPDATE admin SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';
```

**Solution B: Reset User Password**
```sql
-- For user password reset
UPDATE users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'YOUR_USERNAME';
-- This sets password to: admin123
```

---

## Issue #15: Transactions Not Showing

### Problem:
Wallet page shows no transaction history

### ✅ Solution:

**Check if transactions exist:**
```sql
SELECT * FROM transactions WHERE user_id = YOUR_USER_ID ORDER BY created_at DESC;
```

**If empty, transactions will appear when:**
- User joins a tournament (debit)
- User wins a tournament (credit)

**Add test transaction:**
```sql
INSERT INTO transactions (user_id, amount, type, description) 
VALUES (1, 100.00, 'credit', 'Test transaction');
```

---

## 🆘 Still Having Issues?

### Debug Mode:

1. **Enable PHP Errors:**
```php
// Add to top of any PHP file
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

2. **Check PHP Info:**
```php
// Create test.php in adept-play folder
<?php phpinfo(); ?>
// Access: http://localhost/adept-play/test.php
```

3. **Test Database Connection:**
```php
// Create db-test.php
<?php
$conn = mysqli_connect('127.0.0.1', 'root', 'root', 'adept_play');
if ($conn) {
    echo "✓ Database connected successfully!";
} else {
    echo "✗ Connection failed: " . mysqli_connect_error();
}
?>
```

### Get Help:

1. **Check Documentation:**
   - README.md
   - QUICK_START.txt
   - HOW_TO_RUN_ADEPT_PLAY.md

2. **Check Logs:**
   - PHP error log
   - Apache error log
   - MySQL error log

3. **Verify Requirements:**
   - PHP 7.4+ installed
   - MySQL 5.7+ installed
   - Apache running
   - All files uploaded correctly

---

## 📋 Quick Diagnostic Checklist

Run through this checklist:

- [ ] PHP installed and version 7.4+
- [ ] MySQL installed and running
- [ ] Apache/web server running
- [ ] Files in correct directory (htdocs)
- [ ] Database credentials correct in config.php
- [ ] install.php completed successfully
- [ ] .htaccess file exists
- [ ] Internet connection (for CDN resources)
- [ ] Browser cache cleared
- [ ] Cookies enabled
- [ ] JavaScript enabled

---

## 🎯 Prevention Tips

1. **Always backup database before major changes**
2. **Test on localhost before deploying**
3. **Keep PHP and MySQL updated**
4. **Use strong passwords in production**
5. **Enable HTTPS in production**
6. **Regular database backups**
7. **Monitor error logs**

---

**Need more help? Check the other documentation files!**

🎮 Happy Gaming!
