# FleetManager Pro - Installation Guide

**Version**: 1.0.0
**Last Updated**: November 16, 2025
**Status**: Production Ready

---

## 📋 Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation Steps](#installation-steps)
3. [Configuration](#configuration)
4. [Database Setup](#database-setup)
5. [Demo Data](#demo-data)
6. [Web Server Configuration](#web-server-configuration)
7. [Scheduled Tasks](#scheduled-tasks)
8. [Testing](#testing)
9. [Troubleshooting](#troubleshooting)

---

## 🖥️ System Requirements

### Minimum Requirements
- **PHP**: 8.4 or higher
- **Composer**: 2.x
- **Node.js**: 18+ (for assets)
- **Database**: MySQL 8.0+ / PostgreSQL 13+ / SQLite 3
- **Web Server**: Nginx or Apache
- **Memory**: 512MB RAM minimum
- **Disk Space**: 500MB

### Recommended Requirements
- **PHP**: 8.4.14
- **Memory**: 1GB+ RAM
- **Disk Space**: 2GB+
- **SSL Certificate**: For production

### PHP Extensions Required
```
BCMath
Ctype
Fileinfo
JSON
Mbstring
OpenSSL
PDO
Tokenizer
XML
GD or Imagick (for image processing)
```

---

## 🚀 Installation Steps

### Step 1: Clone Repository

```bash
# Clone the repository
git clone https://github.com/haythemsaa/fleet-manager-pro.git
cd fleet-manager-pro

# Or download and extract ZIP
wget https://github.com/haythemsaa/fleet-manager-pro/archive/main.zip
unzip main.zip
cd fleet-manager-pro-main
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader

# Install Node dependencies
npm install

# Build frontend assets
npm run build
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Environment

Edit `.env` file with your settings:

```env
APP_NAME="FleetManager Pro"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fleet_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@fleetmanager.com
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

---

## 💾 Database Setup

### Step 1: Create Database

**MySQL:**
```sql
CREATE DATABASE fleet_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON fleet_manager.* TO 'your_username'@'localhost';
FLUSH PRIVILEGES;
```

**PostgreSQL:**
```sql
CREATE DATABASE fleet_manager;
GRANT ALL PRIVILEGES ON DATABASE fleet_manager TO your_username;
```

### Step 2: Run Migrations

```bash
# Run all migrations
php artisan migrate

# If you want to start fresh
php artisan migrate:fresh
```

### Step 3: Seed Initial Data

```bash
# Seed roles and permissions
php artisan db:seed --class=RolesAndPermissionsSeeder

# Seed basic data (brands, categories, fuel types, etc.)
php artisan db:seed --class=BasicDataSeeder

# Create admin user
php artisan db:seed --class=UserSeeder
```

**Default Admin Credentials:**
- **Email**: admin@fleetmanager.com
- **Password**: password
- ⚠️ **Change these immediately in production!**

---

## 📦 Demo Data

To populate the system with demo data for testing:

```bash
# Seed demo vehicles, employees, fuel records, etc.
php artisan db:seed --class=DemoDataSeeder
```

This will create:
- 17 employees (10 drivers, 5 mechanics, 2 managers)
- 25 vehicles with various statuses
- 125-250 fuel consumption records
- 30+ interventions (various statuses)
- 200+ GPS location points

---

## 🌐 Web Server Configuration

### Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    root /var/www/fleet-manager-pro/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/fleet-manager-pro/public

    <Directory /var/www/fleet-manager-pro/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/fleet-manager-error.log
    CustomLog ${APACHE_LOG_DIR}/fleet-manager-access.log combined
</VirtualHost>
```

### Storage Permissions

```bash
# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Create symbolic link for storage
php artisan storage:link
```

---

## ⏰ Scheduled Tasks

### Cron Job Setup

Add to crontab (`crontab -e`):

```cron
# Laravel Scheduler (runs every minute)
* * * * * cd /path/to/fleet-manager-pro && php artisan schedule:run >> /dev/null 2>&1

# Or run specific commands:

# Process scheduled reports (daily at 8 AM)
0 8 * * * cd /path/to/fleet-manager-pro && php artisan reports:process

# Send maintenance alerts (daily at 9 AM)
0 9 * * * cd /path/to/fleet-manager-pro && php artisan maintenance:alerts

# Clean old audit logs (weekly on Sunday at 2 AM)
0 2 * * 0 cd /path/to/fleet-manager-pro && php artisan audit:clean --days=90 --force
```

### Queue Worker (Optional but Recommended)

For background job processing:

```bash
# Install supervisor
sudo apt-get install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/fleet-manager-worker.conf
```

```ini
[program:fleet-manager-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/fleet-manager-pro/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/fleet-manager-pro/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start fleet-manager-worker:*
```

---

## 🧪 Testing

### Run Demo Seeder

```bash
php artisan db:seed --class=DemoDataSeeder
```

### Test Artisan Commands

```bash
# Test report processing
php artisan reports:process

# Test maintenance alerts
php artisan maintenance:alerts --days=30

# Test audit cleanup (dry run)
php artisan audit:clean --days=90
```

### Access the Application

1. Open browser: `http://your-domain.com`
2. Login with admin credentials
3. Explore the dashboard

---

## 🔧 Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Issue: Permission Denied

**Solution:**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Issue: Database Connection Failed

**Solution:**
1. Check `.env` database credentials
2. Verify database exists
3. Test connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### Issue: Assets Not Loading

**Solution:**
```bash
# Rebuild assets
npm run build

# Recreate storage link
php artisan storage:link
```

### Issue: Queue Jobs Not Processing

**Solution:**
```bash
# Start queue worker manually
php artisan queue:work

# Or restart supervisor
sudo supervisorctl restart fleet-manager-worker:*
```

---

## 🔐 Security Checklist

Before deploying to production:

- [ ] Change default admin password
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Generate new `APP_KEY`
- [ ] Configure SSL certificate
- [ ] Set up firewall rules
- [ ] Enable CSRF protection (enabled by default)
- [ ] Review file permissions
- [ ] Set up regular backups
- [ ] Configure rate limiting
- [ ] Enable two-factor authentication (if available)

---

## 📚 Additional Resources

- [Quick Start Guide](QUICK_START_GUIDE.md)
- [API Documentation](API_DOCUMENTATION.md)
- [Roadmap](ROADMAP_TO_MARKET_LEADER.md)
- [Project Status](PROJECT_FINAL_STATUS.md)

---

## 💡 Support

For issues and questions:
- **Email**: support@fleetmanager.com
- **GitHub Issues**: https://github.com/haythemsaa/fleet-manager-pro/issues
- **Documentation**: https://docs.fleetmanager.com

---

## ✅ Quick Installation (Development)

For quick local development setup:

```bash
# Clone and setup
git clone https://github.com/haythemsaa/fleet-manager-pro.git
cd fleet-manager-pro
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed
php artisan db:seed --class=DemoDataSeeder

# Start server
php artisan serve
```

Visit: `http://localhost:8000`

---

**Installation Complete! 🎉**

Your FleetManager Pro installation is ready. Login with the default credentials and start managing your fleet!
