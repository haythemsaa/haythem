# 🚀 FleetManager Pro - Quick Start Guide

## Welcome to FleetManager Pro!

This guide will help you get started with the most powerful fleet management platform in Africa and Europe.

---

## 📋 Table of Contents
1. [Installation](#installation)
2. [First Login](#first-login)
3. [Essential Features](#essential-features)
4. [Mobile App Setup](#mobile-app-setup)
5. [API Integration](#api-integration)
6. [Support](#support)

---

## 🔧 Installation

### Prerequisites
- PHP 8.4+
- Composer 2.x
- MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.x
- Node.js 18+ (for frontend assets)

### Step 1: Clone and Install
```bash
# Clone the repository
git clone https://github.com/your-org/fleet-manager-pro.git
cd fleet-manager-pro

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Build frontend assets
npm run build
```

### Step 2: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fleet_manager
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Database Setup
```bash
# Run migrations
php artisan migrate

# Seed initial data (roles, permissions, settings)
php artisan db:seed

# OR run both in one command
php artisan migrate --seed
```

### Step 4: Storage Setup
```bash
# Create storage link
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
```

### Step 5: Start the Server
```bash
# Development server
php artisan serve

# Access at: http://localhost:8000
```

---

## 🔐 First Login

### Default Admin Account
```
Email: admin@fleetmanager.com
Password: admin123
```

**IMPORTANT**: Change this password immediately after first login!

### Change Admin Password
1. Go to **Profile** > **Settings**
2. Click **Change Password**
3. Enter new secure password
4. Save changes

---

## ⭐ Essential Features

### 1. Dashboard Overview
**Location**: `/dashboard`

The dashboard provides:
- 📊 Real-time statistics (vehicles, employees, interventions)
- 📈 Interactive charts (Chart.js)
  - Vehicle status distribution (pie chart)
  - Fuel consumption trend (line chart)
  - Maintenance costs (bar chart)
  - Revenue trends (area chart)
- 🔔 Critical alerts
- 📋 Recent activity

### 2. Vehicle Management
**Location**: `/vehicles`

**Add a Vehicle:**
1. Click **"Nouveau Véhicule"**
2. Fill in required fields:
   - Registration number
   - Brand and model
   - VIN (optional but recommended)
   - Purchase date and price
   - Current mileage
3. Click **"Enregistrer"**

**Batch Operations:**
- Go to `/vehicles/batch`
- Select multiple vehicles
- Perform bulk actions:
  - Update status
  - Assign to site
  - Export selection
  - Update fields
  - Delete multiple

### 3. Fuel Management
**Location**: `/fuel-consumptions`

**Log Fuel Entry:**
1. Click **"Ravitaillement"**
2. Select vehicle
3. Enter:
   - Quantity (liters)
   - Unit price
   - Total cost
   - Current mileage
4. Save

**View Analytics:**
- Consumption trends
- Cost analysis
- Efficiency reports
- Anomaly detection

### 4. Maintenance (GMAO)
**Location**: `/interventions`

**Create Intervention:**
1. Click **"Nouvelle Intervention"**
2. Select vehicle
3. Choose type:
   - Préventive (scheduled)
   - Curative (breakdown)
4. Set urgency level
5. Add description
6. Assign to technician (if available)

**Track Progress:**
- View status: En Attente → Diagnostique → En Réparation → Clôturé
- Add work orders
- Track parts used
- Record labor costs

### 5. TCO Calculator
**Location**: `/tco`

**Calculate Total Cost of Ownership:**
1. Select vehicle
2. Choose date range
3. Click **"Calculer TCO"**
4. View 8-category breakdown:
   - Acquisition costs
   - Depreciation
   - Fuel costs
   - Maintenance costs
   - Insurance costs
   - Tax costs
   - Administrative costs
   - Other costs

**Export TCO Report:**
- PDF format
- Excel format
- Share with stakeholders

### 6. Reports & Analytics
**Location**: `/reports`

**Predefined Reports:**
- Fleet summary
- Maintenance history
- Fuel analysis
- Insurance status
- Accident reports
- Financial overview

**Custom Reports:**
1. Go to `/reports/custom`
2. Select report type
3. Choose date range
4. Apply filters
5. Select format (PDF/Excel)
6. Generate

**Scheduled Reports:**
1. Go to `/reports/scheduled`
2. Click **"Schedule New Report"**
3. Configure:
   - Frequency (daily/weekly/monthly)
   - Recipients (emails)
   - Format
   - Filters
4. Save

### 7. GPS Tracking
**Location**: `/gps`

**View Live Tracking:**
- See all active vehicles on map
- Real-time position updates
- Vehicle status indicators
- Click vehicle for details

**View History:**
- Select vehicle
- Choose date range
- View route on map
- See statistics:
  - Total distance
  - Average speed
  - Max speed
  - Stops detected

### 8. Notifications Center
**Location**: `/notifications`

**Manage Notifications:**
- View by level (Critical/Warning/Info)
- Mark as read
- Filter by type
- Take action directly

### 9. Settings
**Location**: `/settings`

**Configure System:**
- **Application**: Language, currency, timezone
- **Fleet**: Alert thresholds, auto-updates
- **Notifications**: Email/SMS settings
- **Payments**: Payment gateways, tax rates
- **Fuel**: Fuel prices, anomaly detection
- **Maintenance**: Service intervals
- **Security**: 2FA, audit logging, session timeout

### 10. Audit Trail
**Location**: `/audit`

**View System Activity:**
- All user actions logged
- Filter by:
  - Event type (created/updated/deleted)
  - Entity type (Vehicle/Intervention/etc.)
  - Date range
  - User
- View detailed changes (old vs new values)

---

## 📱 Mobile App Setup

### For Drivers & Field Staff

**Base API URL**: `https://your-domain.com/api`

### Authentication
```http
POST /api/login
Content-Type: application/json

{
  "email": "driver@example.com",
  "password": "password"
}

Response:
{
  "token": "your-sanctum-token",
  "user": { ... }
}
```

### Mobile Dashboard
```http
GET /api/mobile/dashboard
Authorization: Bearer {token}

Response:
{
  "user": { "name": "...", "email": "..." },
  "stats": {
    "total_vehicles": 150,
    "available_vehicles": 100,
    "urgent_interventions": 5,
    "unread_notifications": 3
  }
}
```

### Quick Fuel Entry
```http
POST /api/mobile/fuel/quick-entry
Authorization: Bearer {token}
Content-Type: application/json

{
  "vehicle_id": 1,
  "quantity_liters": 50,
  "unit_price": 13.50,
  "mileage": 125000
}
```

### Update GPS Location
```http
POST /api/mobile/gps/location
Authorization: Bearer {token}
Content-Type: application/json

{
  "vehicle_id": 1,
  "latitude": 33.5731,
  "longitude": -7.5898,
  "speed": 60,
  "heading": 180
}
```

### Quick Intervention Report
```http
POST /api/mobile/interventions/quick-report
Authorization: Bearer {token}
Content-Type: application/json

{
  "vehicle_id": 1,
  "title": "Pneu crevé",
  "description": "Pneu avant gauche crevé sur l'autoroute",
  "urgency": "urgent"
}
```

**See `API_DOCUMENTATION.md` for complete API reference.**

---

## 🔌 API Integration

### API Endpoints Overview

**Total Endpoints**: 60+

**Categories:**
- Dashboard: 4 endpoints
- Vehicles: 7 endpoints
- Fuel: 5 endpoints
- Interventions: 6 endpoints
- Mobile: 10 endpoints
- Reports: 5 endpoints
- GPS: 4 endpoints

### Example: Get Dashboard Stats
```javascript
// JavaScript/Node.js
const axios = require('axios');

const token = 'your-sanctum-token';

axios.get('https://your-domain.com/api/dashboard', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => {
  console.log(response.data);
})
.catch(error => {
  console.error(error);
});
```

### Example: Add Fuel Entry
```python
# Python
import requests

token = 'your-sanctum-token'
url = 'https://your-domain.com/api/fuel-consumptions'

headers = {
    'Authorization': f'Bearer {token}',
    'Content-Type': 'application/json',
    'Accept': 'application/json'
}

data = {
    'vehicle_id': 1,
    'date': '2025-11-16',
    'quantity_liters': 50,
    'unit_price': 13.50,
    'total_cost': 675.00,
    'mileage': 125000,
    'fuel_type': 'diesel'
}

response = requests.post(url, json=data, headers=headers)
print(response.json())
```

---

## 📚 Common Tasks

### Export Fleet Summary
1. Go to **Reports** > **Predefined Reports**
2. Click **"Rapport Flotte Mensuel"**
3. Select format (PDF/Excel)
4. Download

### Set Up Maintenance Alerts
1. Go to **Settings** > **Fleet**
2. Set **"Alerte Maintenance"** to desired days (e.g., 30)
3. Set **"Alerte Assurance"** to desired days (e.g., 30)
4. Save

### Create User Account
1. Go to **Users** (Admin only)
2. Click **"Nouvel Utilisateur"**
3. Fill in details
4. Assign role:
   - Super Admin (full access)
   - Fleet Manager (fleet management)
   - Mechanic (maintenance only)
   - Driver (limited access)
5. Save

### Generate Monthly Report
1. Go to **Reports** > **Custom Report Builder**
2. Select **"Financial Report"**
3. Set date range (first to last day of month)
4. Choose format (PDF recommended)
5. Click **"Generate"**
6. Download and share

---

## 🆘 Support

### Documentation
- **User Manual**: `USER_MANUAL.md`
- **API Documentation**: `API_DOCUMENTATION.md`
- **Implementation Guide**: `IMPLEMENTATION_COMPLETE.md`
- **Roadmap**: `ROADMAP_TO_MARKET_LEADER.md`

### Contact Support
- **Email**: support@fleetmanager.com
- **Phone**: +212 xxx xxx xxx
- **WhatsApp**: +212 xxx xxx xxx (Africa support)
- **Website**: https://fleetmanager.com/support

### Community
- **GitHub Issues**: Report bugs and feature requests
- **Forums**: Community discussions
- **YouTube**: Video tutorials (coming soon)

### Training
- **Webinars**: Monthly online training sessions
- **On-site**: Available for enterprise clients
- **Documentation**: Comprehensive guides and tutorials

---

## 🎓 Next Steps

### For Administrators
1. ✅ Complete system configuration
2. ✅ Set up user accounts
3. ✅ Import existing vehicle data
4. ✅ Configure alert thresholds
5. ✅ Set up scheduled reports

### For Fleet Managers
1. ✅ Add all vehicles
2. ✅ Set up maintenance schedules
3. ✅ Configure fuel tracking
4. ✅ Train staff on mobile app
5. ✅ Monitor dashboard daily

### For Developers
1. ✅ Review API documentation
2. ✅ Test API endpoints
3. ✅ Build mobile/web integrations
4. ✅ Implement webhooks
5. ✅ Customize features

---

## 🎉 You're All Set!

FleetManager Pro is now ready to help you:
- ✅ Reduce fleet costs by 20-30%
- ✅ Improve vehicle uptime
- ✅ Automate maintenance scheduling
- ✅ Track fuel consumption in real-time
- ✅ Generate comprehensive reports
- ✅ Ensure compliance with regulations

**Welcome to the future of fleet management!** 🚀

---

**Version**: 1.0.0
**Last Updated**: November 16, 2025
**Platform**: FleetManager Pro
**License**: Proprietary
