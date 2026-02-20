# Wolf Palomar Gym Management System
## QR Code-Based Membership and Attendance System

> **Academic Project** - BS Information Technology  
> **Institution**: Datamex College of Saint Adeline - Valenzuela Branch  
> **Version**: v2.5.6-STABLE  
> **Date**: January 2026

---

## 📋 Project Overview

The **Wolf Palomar Gym Management System** is a comprehensive web-based solution designed to modernize gym operations through automated QR code attendance tracking, membership management, and real-time revenue monitoring.

### Key Features

✅ **QR Code Attendance System** - Automatic check-in/check-out via QR scanning  
✅ **Member Management** - Complete member database with profiles and photos  
✅ **Membership Plans** - Monthly, quarterly, and yearly subscription management  
✅ **ID Card Maker** - Generate printable membership cards with QR codes  
✅ **Real-time Dashboard** - Monitor revenue, traffic, and attendance  
✅ **Digital Logbook** - Track all gym visits and calculate earnings  
✅ **Responsive Design** - Mobile-first interface with bottom navigation  
✅ **Role-based Access** - Admin and staff user management  
✅ **Activity Logging** - Complete audit trail of system actions

---

## 🚀 Installation Guide

### Prerequisites

- **XAMPP** (Apache + MySQL + PHP)
- **Web Browser** (Chrome, Firefox, Safari)
- **Text Editor** (VS Code, Sublime Text)

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install XAMPP to `C:\xampp` (Windows) or `/Applications/XAMPP` (Mac)
3. Start **Apache** and **MySQL** from XAMPP Control Panel

### Step 2: Setup Database

1. Open **phpMyAdmin** in your browser: `http://localhost/phpmyadmin`
2. Click **"Import"** in the top menu
3. Choose the file: `database.sql` from the project folder
4. Click **"Go"** to import

**OR** manually run the SQL:

1. Click **"New"** to create a database
2. Name it: `wolf_palomar_gym`
3. Click **"SQL"** tab
4. Copy and paste the entire content of `database.sql`
5. Click **"Go"** to execute

### Step 3: Setup Project Files

1. Copy the entire `wolf-palomar-gym` folder to:
   - **Windows**: `C:\xampp\htdocs\wolf-palomar-gym`
   - **Mac**: `/Applications/XAMPP/htdocs/wolf-palomar-gym`

2. Make sure the folder structure looks like this:
```
htdocs/
└── wolf-palomar-gym/
    ├── assets/
    │   ├── css/
    │   ├── js/
    │   ├── images/
    │   └── uploads/
    ├── config/
    │   └── database.php
    ├── pages/
    │   ├── qr-scan.php
    │   ├── members.php
    │   ├── logbook.php
    │   └── id-maker.php
    ├── index.php
    ├── login.php
    └── database.sql
```

### Step 4: Configure Database Connection

1. Open `config/database.php`
2. Verify these settings:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for default XAMPP
define('DB_NAME', 'wolf_palomar_gym');
```

### Step 5: Set Folder Permissions

Make sure the `assets/uploads/` folder is writable:

**Windows**: Right-click folder → Properties → Security → Edit → Allow "Full Control"  
**Mac/Linux**: Open terminal and run:
```bash
chmod 777 /Applications/XAMPP/htdocs/wolf-palomar-gym/assets/uploads/
```

---

## 🎯 How to Use the System

### First Login

1. Open your browser and go to: `http://localhost/wolf-palomar-gym`
2. You'll be redirected to the login page
3. Use the default credentials:
   - **Email**: `admin@palomargym.com`
   - **Password**: `admin123`

### Dashboard Overview

After login, you'll see:
- **Total Net Revenue** - Today's earnings from attendance and sales
- **Floor Traffic** - Current number of people in the gym
- **Latest Check-ins** - Recent attendance records

### Adding a New Member

1. Click **"MEMBERS"** in the bottom navigation
2. Click the **green (+)** floating button
3. Fill in member details:
   - Personal Information (Name, Email, Phone, etc.)
   - Emergency Contact
   - Health Information (optional)
   - Upload photo (optional)
4. The system will automatically generate a unique QR code
5. Click **"Save Member"**

### QR Code Scanning for Attendance

1. Click the **QR Code icon** in the center of bottom navigation
2. Click **"START SCANNING"** to activate camera
3. Point camera at member's QR code card
4. System will automatically:
   - Check-in member (if first scan of the day)
   - Check-out member (if already checked in)
   - Calculate and charge appropriate fee
   - Update logbook and revenue

**Fee Structure**:
- Active Member: ₱0 (free access)
- Student: ₱30 per visit
- Expired Member: ₱40 per visit
- Non-Member: ₱50 per visit

### Creating Membership Cards (ID Maker)

1. Open the sidebar by clicking **"MORE"** in bottom navigation
2. Click **"SYSTEM MANAGEMENT"** → **"ID MAKER"**
3. Search for member name in the search box
4. Preview the card (Front and Back)
5. Click **"PRINT IDENTITY CARD"** to print
6. Print on cardstock paper and laminate

### Viewing Logbook

1. Click **"LOGBOOK"** in bottom navigation
2. View all check-ins for the selected day
3. See total revenue for the day
4. Click on different days (SUN-SAT) to view history

### Managing the System

Access more features from the sidebar (**MORE** button):

**System Management**:
- Members - Add, edit, delete members
- Equipments - Track gym equipment (coming soon)
- ID Maker - Create membership cards
- Goal Center - Set and track daily goals

**Reports & Feedback** - Generate attendance and revenue reports  
**System Audit Log** - View all system activities  
**General Settings** - Configure gym rates and settings

---

## 📱 Mobile/Tablet Usage

The system is fully responsive and optimized for:
- **Tablets** - Perfect for QR scanning at gym entrance
- **Mobile Phones** - Access dashboard and manage members on the go
- **Desktop** - Full administrative access

### Recommended Tablet Setup

1. Use a tablet (iPad or Android) at the gym entrance
2. Keep the QR Scanner page open
3. Members scan their cards on arrival and departure
4. Tablet automatically records attendance and calculates fees

---

## 🔐 Security Features

- **Password Hashing** - All passwords encrypted with bcrypt
- **SQL Injection Prevention** - Prepared statements for all queries
- **Session Management** - Secure session handling
- **Input Sanitization** - All user inputs cleaned
- **Activity Logging** - All actions tracked with timestamps
- **Role-based Access** - Admin, Coach, Trainer permissions

---

## 📊 Database Schema

### Main Tables

**member** - Member information and QR codes  
**membership** - Active subscriptions and expiration dates  
**membership_plans** - Monthly, quarterly, yearly plans  
**attendance** - Check-in/out records and fees  
**admin** - System administrators  
**staff** - Coaches and trainers  
**activity_logs** - System audit trail  
**gym_settings** - System configuration

---

## 🎨 Branding

**Colors**:
- Primary: Neon Green (#7FFF00)
- Secondary: Black (#000000)
- Background: White and Light Gray

**Logo**: Wolf Palomar Gym  
**Tagline**: "QR Code Gym Membership and Attendance System"

---

## 🐛 Troubleshooting

### Problem: "Connection failed" error
**Solution**: Make sure MySQL is running in XAMPP Control Panel

### Problem: "Table doesn't exist" error
**Solution**: Import the `database.sql` file again

### Problem: Can't upload images
**Solution**: Check that `assets/uploads/` folder has write permissions

### Problem: QR Scanner not working
**Solution**: Use HTTPS or allow camera access in browser settings

### Problem: Blank page after login
**Solution**: Check PHP error logs in `C:\xampp\htdocs\php_error_log`

---

## 📞 Support & Credits

**Development Team**:
- Angeles, Adrian
- Alba, Janus
- Amponin, John Alexis
- Ferrer, Daryl Jake
- Galsim, Carmelo
- Geronimo, Tristhan
- Lagulao, Melz
- Mamay, Jhon Carlo
- Reposar, Jhaym Dueñas
- Suycano, Mark Rufort
- Tamporong, Meldrin

**Subject**: Cloud Computing  
**Instructor**: Mr. Emman Gumayagay

---

## 📝 License

This project is developed for academic purposes only and is not intended for commercial use without further development and approval.

© 2026 Wolf Palomar Systems - All Rights Reserved

---

## 🚀 Future Enhancements

- [ ] Online database integration (Supabase)
- [ ] Equipment inventory tracking
- [ ] Trainer scheduling system
- [ ] Member workout plans
- [ ] Progress tracking and analytics
- [ ] Mobile app (iOS/Android)
- [ ] Email/SMS notifications
- [ ] Payment gateway integration
- [ ] Biometric authentication
- [ ] Advanced reporting and analytics

---

**Good luck with your defense! 🎓**
