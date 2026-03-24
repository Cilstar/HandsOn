# HandsOn - Location-Based Skilled Worker Platform

A digital platform connecting customers with verified skilled workers (plumbers, electricians, carpenters, cleaners, painters, and technicians) in Kenya.

## Features

- **User Registration & Authentication** - Secure login with email/phone
- **Worker Profiles** - Verified professionals with ratings, pricing, and experience
- **Location-Based Matching** - Find workers near you using OpenStreetMap
- **Job Request System** - Create and manage service requests
- **Mock M-Pesa Integration** - Simulated mobile payment system
- **Rating & Review System** - Customer feedback for workers
- **Admin Dashboard** - Manage verifications and view analytics

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP with PDO
- **Database**: MySQL
- **Maps**: OpenStreetMap with Leaflet.js
- **Payments**: Mock M-Pesa (ready for Daraja API integration)

## Project Structure

```
hands-on/
├── api/
│   ├── config/
│   │   ├── database.php
│   │   └── constants.php
│   └── modules/
│       ├── auth/
│       ├── workers/
│       ├── jobs/
│       ├── payments/
│       ├── reviews/
│       └── admin/
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── pages/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── workers.php
│   ├── worker-profile.php
│   ├── jobs.php
│   └── admin/
├── database/
│   └── schema.sql
└── README.md
```

## Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Steps

1. **Create Database**
   ```sql
   CREATE DATABASE handson_db;
   ```
   
   Import the schema:
   ```bash
   mysql -u root -p handson_db < database/schema.sql
   ```

2. **Configure Database**
   
   Edit `api/config/database.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'handson_db');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Start Server**
   
   Using PHP built-in server:
   ```bash
   cd hands-on
   php -S localhost:8000
   ```

4. **Access Application**
   
   Open browser: `http://localhost:8000`

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Customer | john@example.com | admin123 |
| Worker | david@handson.co.ke | admin123 |
| Admin | admin@handson.co.ke | admin123 |

## Usage

### For Customers

1. Register as a customer
2. Browse workers by category
3. Select a worker and request service
4. Track job status
5. Make payment (mock)
6. Leave a review

### For Workers

1. Register as a worker
2. Complete your profile
3. Accept/decline job requests
4. Update job status as you work
5. Receive ratings and reviews

### For Admin

1. Login with admin credentials
2. Verify worker profiles
3. Monitor transactions
4. View analytics

## M-Pesa Integration

The payment system is currently in mock mode. To integrate real M-Pesa:

1. Get M-Pesa API credentials from Safaricom Developer Portal
2. Update `api/modules/payments/create.php` with real Daraja API calls
3. Configure callback URL for payment notifications

## Google Maps Alternative

Using OpenStreetMap with Leaflet.js - no API key required. To switch to Google Maps:

1. Replace Leaflet CSS/JS with Google Maps SDK
2. Update map initialization in JavaScript
3. Update marker creation logic

## Security Features

- Password hashing with bcrypt
- SQL injection prevention via prepared statements
- XSS prevention via output escaping
- Session-based authentication
- Role-based access control

## Future Enhancements

- Real M-Pesa integration
- Android/iOS mobile apps
- In-app chat
- Voice booking
- AI-based recommendations
- Insurance integration

## License

This project is for educational purposes.

## Author

HandsOn Development Team
