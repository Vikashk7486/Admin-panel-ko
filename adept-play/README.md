# Adept Play - Tournament Web App

A fully functional mobile-first tournament web application built with PHP, MySQL, HTML, Tailwind CSS, and JavaScript.

## 🚀 Features

### User Panel
- User registration and login
- Browse and join tournaments
- View upcoming/live tournaments with room details
- Tournament history with win/loss records
- Wallet management with transaction history
- Profile management with password change
- Mobile-first responsive design

### Admin Panel
- Secure admin login
- Dashboard with statistics
- Create and manage tournaments
- Set room ID and password for tournaments
- Declare winners and distribute prizes automatically
- User management
- Admin settings

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

## 🛠️ Installation

1. **Extract the files** to your web server directory (e.g., `htdocs`, `www`, or `public_html`)

2. **Configure Database** (if needed):
   - Open `common/config.php`
   - Update database credentials if different from defaults:
     ```php
     define('DB_HOST', '127.0.0.1');
     define('DB_USER', 'root');
     define('DB_PASS', 'root');
     define('DB_NAME', 'adept_play');
     ```

3. **Run Installation**:
   - Open your browser and navigate to: `http://localhost/adept-play/install.php`
   - Click "Start Installation"
   - Wait for the installation to complete
   - You will be redirected to the login page

4. **Default Admin Credentials**:
   - Username: `admin`
   - Password: `admin123`

## 📁 Project Structure

```
adept-play/
├── common/
│   ├── config.php          # Database configuration
│   ├── header.php          # User panel header
│   └── bottom.php          # User panel bottom navigation
├── admin/
│   ├── common/
│   │   ├── header.php      # Admin panel header
│   │   └── bottom.php      # Admin panel footer
│   ├── login.php           # Admin login
│   ├── index.php           # Admin dashboard
│   ├── tournament.php      # Tournament management
│   ├── manage_tournament.php # Individual tournament management
│   ├── user.php            # User management
│   └── setting.php         # Admin settings
├── login.php               # User login/signup
├── index.php               # User homepage
├── my_tournaments.php      # User's tournaments
├── wallet.php              # Wallet management
├── profile.php             # User profile
├── install.php             # Installation script
├── .htaccess               # Apache configuration
└── README.md               # This file
```

## 🎮 Usage

### For Users

1. **Register/Login**:
   - Go to `http://localhost/adept-play/login.php`
   - Create a new account or login with existing credentials

2. **Browse Tournaments**:
   - View all upcoming tournaments on the homepage
   - See entry fees, prize pools, and match times

3. **Join Tournament**:
   - Click "Join Now" on any tournament
   - Entry fee will be deducted from your wallet
   - View joined tournaments in "My Tournaments"

4. **View Live Tournaments**:
   - When a tournament goes live, room ID and password will be displayed
   - Access this information from "My Tournaments" > "Upcoming/Live" tab

5. **Check Results**:
   - View completed tournaments in "My Tournaments" > "Completed" tab
   - See if you won or participated

### For Admins

1. **Login**:
   - Go to `http://localhost/adept-play/admin/login.php`
   - Use admin credentials

2. **Create Tournament**:
   - Navigate to "Tournaments" section
   - Fill in tournament details (title, game, fees, prize, time)
   - Click "Create Tournament"

3. **Manage Tournament**:
   - Click "Manage" on any tournament
   - Set room ID and password
   - Change status to "Live" when tournament starts
   - View all participants

4. **Declare Winner**:
   - Select winner from participants dropdown
   - Click "Declare Winner & Distribute Prize"
   - Prize will be automatically added to winner's wallet
   - Tournament status will change to "Completed"

## 🔒 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- Session management for authentication
- Right-click disabled
- Text selection disabled (except input fields)
- Zoom disabled for native app feel
- Protected configuration files via .htaccess

## 💰 Currency Format

All monetary values are displayed in Indian Rupees (₹) format.

## 🎨 Design

- Mobile-first responsive design
- Dark theme with gradient accents
- Tailwind CSS for styling
- Font Awesome icons
- Clean and modern UI

## 📊 Database Schema

### Tables:
- **users**: User accounts and wallet balances
- **admin**: Admin accounts
- **tournaments**: Tournament details
- **participants**: User-tournament relationships
- **transactions**: Wallet transaction history

## 🐛 Troubleshooting

1. **Database Connection Error**:
   - Verify MySQL is running
   - Check database credentials in `common/config.php`
   - Ensure database user has proper permissions

2. **Installation Issues**:
   - Make sure MySQL user has CREATE DATABASE privileges
   - Check PHP error logs for detailed error messages

3. **Page Not Found**:
   - Verify .htaccess is enabled
   - Check Apache mod_rewrite is enabled

## 📝 Notes

- This is a demonstration project for educational purposes
- Add Money and Withdraw buttons are dummy buttons (not functional)
- Customize the design and features as needed
- Always use HTTPS in production environments
- Change default admin password after installation

## 🔧 Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Icons**: Font Awesome 6.4.0
- **Server**: Apache with .htaccess

## 📄 License

This project is open-source and available for educational purposes.

## 👨‍💻 Support

For issues or questions, please refer to the code comments or contact the development team.

---

**Adept Play** - Your Ultimate Tournament Gaming Platform
