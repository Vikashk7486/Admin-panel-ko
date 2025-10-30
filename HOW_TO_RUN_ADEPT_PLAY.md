# 🎮 HOW TO RUN ADEPT PLAY

## ⚠️ IMPORTANT: This is a PHP Application

The **Adept Play** tournament app is built with **PHP and MySQL**, which means it **cannot run on Vercel** (Vercel is for Node.js/Next.js apps).

## 🚀 SOLUTION: Run on a PHP Server

### Option 1: Use XAMPP (Easiest - Recommended)

1. **Download XAMPP**
   - Go to: https://www.apachefriends.org/
   - Download for your OS (Windows/Mac/Linux)
   - Install XAMPP

2. **Copy Project Files**
   ```bash
   # Copy the adept-play folder to XAMPP's htdocs directory
   # Windows: C:\xampp\htdocs\
   # Mac: /Applications/XAMPP/htdocs/
   # Linux: /opt/lampp/htdocs/
   
   cp -r /vercel/sandbox/adept-play /path/to/xampp/htdocs/
   ```

3. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start **Apache** (for PHP)
   - Start **MySQL** (for database)

4. **Run Installation**
   - Open browser
   - Go to: `http://localhost/adept-play/install.php`
   - Click "Start Installation"
   - Wait for completion

5. **Access the App**
   - **User Panel:** `http://localhost/adept-play/login.php`
   - **Admin Panel:** `http://localhost/adept-play/admin/login.php`
   - **Admin Credentials:** 
     - Username: `admin`
     - Password: `admin123`

---

### Option 2: Use PHP Built-in Server (Quick Test)

If you just want to test quickly without XAMPP:

```bash
# Navigate to the project directory
cd /vercel/sandbox/adept-play

# Start PHP built-in server
php -S localhost:8000

# Open browser to:
# http://localhost:8000/install.php
```

**Note:** You'll still need MySQL running separately for this option.

---

### Option 3: Deploy to PHP Hosting

To make it accessible online, deploy to a PHP hosting provider:

**Free PHP Hosting Options:**
- InfinityFree (https://infinityfree.net/)
- 000webhost (https://www.000webhost.com/)
- AwardSpace (https://www.awardspace.com/)

**Paid PHP Hosting Options:**
- Hostinger
- Bluehost
- SiteGround
- DigitalOcean (with LAMP stack)

**Deployment Steps:**
1. Upload all files via FTP/cPanel
2. Create MySQL database
3. Update `common/config.php` with database credentials
4. Run `install.php` from browser
5. Done!

---

## 🔧 Troubleshooting

### "Can't connect to database"
- Make sure MySQL is running
- Check credentials in `common/config.php`
- Default: host=127.0.0.1, user=root, pass=root

### "Installation fails"
- Ensure MySQL user has CREATE DATABASE permission
- Check PHP error logs

### "Page not found"
- Verify files are in correct directory
- Check Apache is running
- Ensure .htaccess file exists

---

## 📱 Testing on Mobile

1. Find your computer's local IP address:
   ```bash
   # Windows
   ipconfig
   
   # Mac/Linux
   ifconfig
   ```

2. Access from mobile browser:
   ```
   http://YOUR_IP_ADDRESS/adept-play/login.php
   ```

3. Make sure mobile is on same WiFi network

---

## ✅ Quick Start Checklist

- [ ] Install XAMPP
- [ ] Copy adept-play folder to htdocs
- [ ] Start Apache and MySQL
- [ ] Run install.php
- [ ] Login as admin
- [ ] Create first tournament
- [ ] Test user registration
- [ ] Test joining tournament

---

## 🎯 Why Not Vercel?

**Vercel is designed for:**
- Next.js applications
- Node.js backends
- Static sites
- Serverless functions

**Adept Play requires:**
- PHP runtime
- MySQL database
- Apache server
- Traditional server-side rendering

**That's why you're getting a 404 error on Vercel!**

---

## 💡 Alternative: Convert to Next.js

If you want to run on Vercel, you would need to:
1. Rewrite the entire app in Next.js (React)
2. Use Vercel Postgres or external MySQL
3. Convert PHP logic to API routes
4. This would be a complete rewrite (not recommended)

**Recommendation:** Stick with PHP and use proper PHP hosting!

---

## 📞 Need Help?

1. Check README.md for detailed documentation
2. Check QUICK_START.txt for step-by-step guide
3. Check PROJECT_SUMMARY.txt for overview
4. Review PHP error logs in XAMPP

---

**Happy Gaming! 🎮**
