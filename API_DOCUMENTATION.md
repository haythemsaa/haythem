# 📡 FleetManager Pro - API Documentation

## 🔐 Authentication

All API endpoints require authentication using Laravel Sanctum tokens.

### Get API Token

```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@fleet.com",
  "password": "password123"
}

Response:
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxx",
  "user": {...}
}
```

### Using Token

Include the token in all requests:

```bash
Authorization: Bearer {your-token-here}
```

---

## 📊 Dashboard API

### Get Main Dashboard Statistics

```http
GET /api/dashboard
```

**Response:**
```json
{
  "success": true,
  "data": {
    "fleet": {
      "total_vehicles": 150,
      "available": 45,
      "in_service": 80,
      "in_maintenance": 20,
      "out_of_service": 5,
      "utilization_rate": 70.0
    },
    "maintenance": {
      "total_interventions": 234,
      "pending": 12,
      "in_progress": 8,
      "completed_this_month": 15,
      "urgent": 3
    },
    "fuel": {
      "total_cost_this_month": 45230.50,
      "total_liters_this_month": 3250.75,
      "avg_price_per_liter": 13.92
    },
    "financial": {
      "rental_revenue": 125000.00,
      "maintenance_costs": 32500.00,
      "fuel_costs": 45230.50,
      "net_income": 47269.50
    }
  }
}
```

### Get Fleet Alerts

```http
GET /api/dashboard/alerts
```

**Response:**
```json
{
  "success": true,
  "data": {
    "critical": [
      {
        "type": "insurance_expired",
        "level": "critical",
        "title": "Assurance Expirée",
        "message": "L'assurance du véhicule ABC-123 a expiré",
        "action_url": "/insurances/123/edit"
      }
    ],
    "warnings": [...],
    "info": [...],
    "stats": {
      "critical_count": 5,
      "warning_count": 12,
      "info_count": 3,
      "total_count": 20
    }
  }
}
```

### Get Performance Metrics

```http
GET /api/dashboard/performance
```

**Response:**
```json
{
  "success": true,
  "data": {
    "utilization_rate": 70.0,
    "maintenance_efficiency": 85.5,
    "fuel_efficiency": 8.5,
    "safety_score": 92.3
  }
}
```

### Get Trends Data

```http
GET /api/dashboard/trends?type=fuel&months=12
```

**Parameters:**
- `type`: fuel, maintenance, revenue
- `months`: 6 or 12 (default: 12)

---

## 🚗 Vehicles API

### List All Vehicles

```http
GET /api/vehicles?page=1&per_page=20
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "registration_number": "ABC-123-45",
        "internal_code": "VH-001",
        "brand": {...},
        "model": {...},
        "status": "disponible",
        "current_mileage": 45000
      }
    ],
    "total": 150
  }
}
```

### Get Vehicle Details

```http
GET /api/vehicles/1
```

### Create Vehicle

```http
POST /api/vehicles
Content-Type: application/json

{
  "registration_number": "XYZ-789-12",
  "internal_code": "VH-152",
  "brand_id": 1,
  "vehicle_model_id": 5,
  "vehicle_category_id": 2,
  "year": 2023,
  "color": "Blanc",
  "current_mileage": 0,
  "status": "disponible"
}
```

### Update Vehicle

```http
PUT /api/vehicles/1
Content-Type: application/json

{
  "current_mileage": 45500,
  "status": "en_mission"
}
```

### Delete Vehicle

```http
DELETE /api/vehicles/1
```

### Get Available Vehicles

```http
GET /api/vehicles-available
```

### Get Vehicles in Maintenance

```http
GET /api/vehicles-maintenance
```

### Get Vehicle Statistics

```http
GET /api/vehicles-stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 150,
    "available": 45,
    "in_service": 80,
    "maintenance": 20,
    "out_of_service": 5
  }
}
```

---

## 📅 Rentals API

### List Rentals

```http
GET /api/rentals?status=ongoing
```

### Create Rental

```http
POST /api/rentals
Content-Type: application/json

{
  "vehicle_id": 1,
  "customer_id": 5,
  "start_date": "2025-11-20",
  "end_date": "2025-11-25",
  "daily_rate": 350.00,
  "deposit_amount": 2000.00
}
```

### Complete Rental

```http
POST /api/rentals/1/complete
Content-Type: application/json

{
  "actual_end_date": "2025-11-25",
  "final_mileage": 45750,
  "damage_notes": null
}
```

### Get Ongoing Rentals

```http
GET /api/rentals-ongoing
```

### Get Rental Statistics

```http
GET /api/rentals-stats
```

---

## 🛡️ Insurance API

### List Insurances

```http
GET /api/insurances
```

### Create Insurance

```http
POST /api/insurances
Content-Type: application/json

{
  "vehicle_id": 1,
  "supplier_id": 10,
  "policy_number": "POL-2025-1234",
  "start_date": "2025-01-01",
  "end_date": "2025-12-31",
  "annual_premium": 4500.00,
  "coverage_type": "Tous Risques"
}
```

### Get Expiring Insurances

```http
GET /api/insurances-expiring?days=30
```

**Parameters:**
- `days`: Number of days ahead to check (default: 30)

### Get Expired Insurances

```http
GET /api/insurances-expired
```

### Get Insurance Statistics

```http
GET /api/insurances-stats
```

---

## ⛽ Fuel Consumption API

### List Fuel Consumptions

```http
GET /api/fuel-consumptions?vehicle_id=1&start_date=2025-01-01&end_date=2025-11-30
```

### Record Fuel Consumption

```http
POST /api/fuel-consumptions
Content-Type: application/json

{
  "vehicle_id": 1,
  "fuel_type_id": 1,
  "refuel_date": "2025-11-16",
  "quantity": 50.5,
  "unit_price": 13.95,
  "total_cost": 704.48,
  "mileage": 45500,
  "station": "Total Casablanca"
}
```

### Get Fuel Statistics

```http
GET /api/fuel-consumptions-stats?vehicle_id=1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_consumptions": 45,
    "total_quantity": 2250.50,
    "total_cost": 31405.98,
    "average_unit_price": 13.96,
    "average_quantity": 50.01
  }
}
```

### Get Fuel Trend

```http
GET /api/fuel-consumptions-trend?vehicle_id=1&months=12
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "month": "2025-01",
      "total_quantity": 185.50,
      "total_cost": 2589.48,
      "avg_price": 13.96,
      "count": 4
    }
  ]
}
```

---

## 🔧 Interventions API

### List Interventions

```http
GET /api/interventions?status=en_attente&urgency=tres_urgent
```

### Create Intervention

```http
POST /api/interventions
Content-Type: application/json

{
  "vehicle_id": 1,
  "employee_id": 5,
  "intervention_category_id": 2,
  "title": "Révision 50 000 km",
  "description": "Vidange, filtres, freins",
  "type": "preventive",
  "urgency": "normal",
  "severity": "mineure",
  "request_date": "2025-11-16"
}
```

### Close Intervention

```http
POST /api/interventions/1/close
```

### Get Pending Interventions

```http
GET /api/interventions-pending
```

### Get Urgent Interventions

```http
GET /api/interventions-urgent
```

### Get Intervention Statistics

```http
GET /api/interventions-stats
```

---

## 📊 Common Response Formats

### Success Response

```json
{
  "success": true,
  "data": {...}
}
```

### Success with Message

```json
{
  "success": true,
  "message": "Vehicle created successfully",
  "data": {...}
}
```

### Error Response

```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Paginated Response

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [...],
    "first_page_url": "http://api/endpoint?page=1",
    "from": 1,
    "last_page": 10,
    "per_page": 20,
    "to": 20,
    "total": 200
  }
}
```

---

## 🚀 Rate Limiting

- **Authenticated requests:** 60 requests per minute
- **Public endpoints:** 20 requests per minute

Rate limit headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
```

---

## 🔒 Security Best Practices

1. **Always use HTTPS** in production
2. **Never share** your API token
3. **Rotate tokens** regularly
4. **Use specific scopes** when possible
5. **Implement CORS** properly
6. **Validate all input** on client side
7. **Handle errors** gracefully

---

## 📱 Mobile App Integration

### Example: Swift (iOS)

```swift
let headers: HTTPHeaders = [
    "Authorization": "Bearer \(token)",
    "Accept": "application/json"
]

AF.request("https://api.example.com/api/dashboard", headers: headers)
    .responseDecodable(of: DashboardResponse.self) { response in
        // Handle response
    }
```

### Example: Kotlin (Android)

```kotlin
val client = OkHttpClient()
val request = Request.Builder()
    .url("https://api.example.com/api/dashboard")
    .addHeader("Authorization", "Bearer $token")
    .build()

client.newCall(request).enqueue(...)
```

### Example: React Native

```javascript
fetch('https://api.example.com/api/dashboard', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

---

## 🧪 Testing

### Health Check

```http
GET /api/health
```

**Response:**
```json
{
  "status": "ok",
  "app": "FleetManager Pro",
  "version": "1.0.0"
}
```

---

## 📞 Support

- **Email:** support@fleetmanager.pro
- **Documentation:** https://docs.fleetmanager.pro
- **Status:** https://status.fleetmanager.pro

---

**Version:** 1.0.0
**Last Updated:** 2025-11-16
