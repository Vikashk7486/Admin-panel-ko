#!/bin/bash

# Adept Play - Local Setup Script
# This script helps you set up the project on your local machine

echo "🎮 ADEPT PLAY - Local Setup Script"
echo "===================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if PHP is installed
echo "Checking PHP installation..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    echo -e "${GREEN}✓ PHP is installed: $PHP_VERSION${NC}"
else
    echo -e "${RED}✗ PHP is not installed${NC}"
    echo "Please install PHP 7.4 or higher"
    echo "Download XAMPP from: https://www.apachefriends.org/"
    exit 1
fi

# Check if MySQL is accessible
echo ""
echo "Checking MySQL installation..."
if command -v mysql &> /dev/null; then
    echo -e "${GREEN}✓ MySQL is installed${NC}"
else
    echo -e "${YELLOW}⚠ MySQL command not found${NC}"
    echo "Make sure MySQL is installed and running"
    echo "If using XAMPP, start MySQL from XAMPP Control Panel"
fi

echo ""
echo "===================================="
echo "Setup Options:"
echo "===================================="
echo ""
echo "1. Quick Test (PHP Built-in Server)"
echo "   - Fast setup for testing"
echo "   - Requires MySQL running separately"
echo ""
echo "2. XAMPP Setup (Recommended)"
echo "   - Full featured setup"
echo "   - Includes Apache + MySQL"
echo ""
echo "3. Show Installation Instructions"
echo ""
read -p "Choose option (1-3): " option

case $option in
    1)
        echo ""
        echo "Starting PHP Built-in Server..."
        echo ""
        echo -e "${YELLOW}⚠ Make sure MySQL is running!${NC}"
        echo ""
        echo "Server will start at: http://localhost:8000"
        echo ""
        echo "Access these URLs:"
        echo "  - Installation: http://localhost:8000/install.php"
        echo "  - User Login:   http://localhost:8000/login.php"
        echo "  - Admin Login:  http://localhost:8000/admin/login.php"
        echo ""
        echo "Press Ctrl+C to stop the server"
        echo ""
        cd adept-play
        php -S localhost:8000
        ;;
    
    2)
        echo ""
        echo "XAMPP Setup Instructions:"
        echo "========================="
        echo ""
        echo "1. Download XAMPP from: https://www.apachefriends.org/"
        echo ""
        echo "2. Install XAMPP on your system"
        echo ""
        echo "3. Copy the 'adept-play' folder to:"
        echo "   - Windows: C:\\xampp\\htdocs\\"
        echo "   - Mac:     /Applications/XAMPP/htdocs/"
        echo "   - Linux:   /opt/lampp/htdocs/"
        echo ""
        echo "4. Start XAMPP Control Panel"
        echo "   - Start Apache"
        echo "   - Start MySQL"
        echo ""
        echo "5. Open browser and go to:"
        echo "   http://localhost/adept-play/install.php"
        echo ""
        echo "6. Click 'Start Installation'"
        echo ""
        echo "7. Login with:"
        echo "   Admin: admin / admin123"
        echo ""
        ;;
    
    3)
        echo ""
        cat << 'EOF'
╔════════════════════════════════════════════════════════════╗
║           ADEPT PLAY - INSTALLATION GUIDE                  ║
╚════════════════════════════════════════════════════════════╝

📋 REQUIREMENTS:
  • PHP 7.4 or higher
  • MySQL 5.7 or higher
  • Apache web server (or PHP built-in server)
  • Modern web browser

🚀 QUICK START (3 STEPS):

STEP 1: Install XAMPP
  → Download: https://www.apachefriends.org/
  → Install and start Apache + MySQL

STEP 2: Copy Files
  → Copy 'adept-play' folder to htdocs directory
  → Windows: C:\xampp\htdocs\
  → Mac: /Applications/XAMPP/htdocs/
  → Linux: /opt/lampp/htdocs/

STEP 3: Run Installation
  → Open: http://localhost/adept-play/install.php
  → Click "Start Installation"
  → Done!

🔐 DEFAULT CREDENTIALS:
  Admin Username: admin
  Admin Password: admin123

📱 ACCESS URLS:
  User Panel:  http://localhost/adept-play/login.php
  Admin Panel: http://localhost/adept-play/admin/login.php

🔧 DATABASE CONFIG (if needed):
  File: common/config.php
  Host: 127.0.0.1
  User: root
  Pass: root
  DB:   adept_play

⚠️  IMPORTANT:
  This is a PHP application and CANNOT run on Vercel!
  Vercel is for Node.js/Next.js apps only.
  Use PHP hosting or local XAMPP instead.

📚 MORE HELP:
  • README.md - Complete documentation
  • QUICK_START.txt - Step-by-step guide
  • HOW_TO_RUN_ADEPT_PLAY.md - Detailed setup

🎮 ENJOY YOUR TOURNAMENT PLATFORM!

EOF
        ;;
    
    *)
        echo "Invalid option"
        exit 1
        ;;
esac
