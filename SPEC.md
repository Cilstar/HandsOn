# HandsOn - Technical Specification Document

## Project Overview
- **Project Name**: HandsOn
- **Type**: Location-based skilled worker marketplace web application
- **Core Functionality**: Connects customers with verified skilled workers (plumbers, electricians, carpenters, cleaners) in Kenya
- **Target Users**: Customers seeking services, Skilled Workers, System Administrators
- **Pilot Location**: Roysambu, Nairobi, Kenya (plumbing services)

---

## Technology Stack

### Frontend
- HTML5, CSS3, JavaScript (Vanilla)
- Mobile-responsive design
- OpenStreetMap with Leaflet.js for mapping

### Backend
- PHP 8.x with PDO
- RESTful API architecture

### Database
- MySQL 8.x

### External Integrations
- Mock M-Pesa payment system (structure ready for Daraja API)
- OpenStreetMap/Leaflet.js for location features

---

## UI/UX Specification

### Color Palette
- **Primary**: #1E88E5 (Blue - trust, reliability)
- **Primary Dark**: #1565C0
- **Primary Light**: #64B5F6
- **Secondary**: #FF6F00 (Amber/Orange - action, urgency)
- **Secondary Light**: #FFA726
- **Success**: #43A047 (Green)
- **Danger**: #E53935 (Red)
- **Warning**: #FB8C00 (Orange)
- **Background**: #F5F7FA
- **Card Background**: #FFFFFF
- **Text Primary**: #212121
- **Text Secondary**: #757575
- **Border**: #E0E0E0

### Typography
- **Primary Font**: 'Poppins', sans-serif (headings)
- **Secondary Font**: 'Open Sans', sans-serif (body)
- **Font Sizes**:
  - H1: 2.5rem (40px)
  - H2: 2rem (32px)
  - H3: 1.5rem (24px)
  - Body: 1rem (16px)
  - Small: 0.875rem (14px)

### Layout Structure
- **Header**: Fixed navigation with logo, menu, user controls
- **Hero Section**: Landing page with search and call-to-action
- **Content Areas**: Card-based layouts for workers, jobs
- **Footer**: Links, contact info, social media

### Responsive Breakpoints
- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

---

## Core Features Specification

### 1. Authentication Module

#### User Registration
- Fields: Full name, email, phone, password, role (customer/worker)
- Email/phone format validation
- Password: minimum 8 characters
- Password hashing using bcrypt

#### User Login
- Email/phone + password
- Session management
- Role-based access control

#### User Roles
- Customer
- Worker
- Admin

### 2. Worker Management Module

#### Worker Profile
- Profile photo (upload)
- Skills category (Plumber, Electrician, Carpenter, Cleaner, Painter, Technician)
- Experience level (1-2 years, 3-5 years, 5+ years)
- Location (coordinates for mapping)
- Service area radius
- Pricing range (KES)
- Availability status (Available, Busy, Offline)
- Bio/description

#### Worker Categories
1. Plumber
2. Electrician
3. Carpenter
4. Cleaner
5. Painter
6. Technician

### 3. Service Request Module

#### Request Process
1. Customer selects service category
2. System shows nearby workers on map
3. Customer selects worker
4. Customer describes job (title, description, address)
5. Worker receives notification
6. Worker accepts or declines
7. Job status updates: Pending → Accepted → In Progress → Completed

#### Job Statuses
- Pending
- Accepted
- In Progress
- Completed
- Cancelled
- Rejected

### 4. Payment Module (Mock M-Pesa)

#### Features
- STK Push simulation
- Payment request creation
- Transaction recording
- Payment status tracking
- Digital receipt generation

#### Payment Statuses
- Pending
- Initiated
- Completed
- Failed
- Refunded

### 5. Rating & Review Module

#### Rating System
- 1-5 star rating
- Written review (optional)
- Rating calculation (average)
- Admin review for poor ratings (< 3 stars)

### 6. Admin Dashboard

#### Features
- Worker verification management
- User account management
- Category management
- Transaction reports
- Analytics dashboard
- Dispute management

---

## Database Schema

### Tables

#### users
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| name | VARCHAR(100) | Full name |
| email | VARCHAR(100) | Unique email |
| phone | VARCHAR(20) | Phone number |
| password | VARCHAR(255) | Hashed password |
| role | ENUM | customer, worker, admin |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

#### worker_profiles
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| user_id | INT (FK) | Link to users |
| category | VARCHAR(50) | Skill category |
| experience | VARCHAR(50) | Experience level |
| bio | TEXT | Description |
| photo | VARCHAR(255) | Profile photo path |
| latitude | DECIMAL(10,8) | Location lat |
| longitude | DECIMAL(11,8) | Location lng |
| service_radius | INT | Area coverage (km) |
| hourly_rate | DECIMAL(10,2) | Price per hour |
| availability | ENUM | Available, Busy, Offline |
| is_verified | BOOLEAN | Admin verified |
| rating_avg | DECIMAL(3,2) | Average rating |
| review_count | INT | Total reviews |
| created_at | TIMESTAMP | Creation date |

#### job_requests
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| customer_id | INT (FK) | Customer user ID |
| worker_id | INT (FK) | Worker user ID |
| category | VARCHAR(50) | Service category |
| title | VARCHAR(200) | Job title |
| description | TEXT | Job details |
| address | VARCHAR(255) | Service address |
| latitude | DECIMAL(10,8) | Location lat |
| longitude | DECIMAL(11,8) | Location lng |
| status | ENUM | Job status |
| scheduled_date | DATE | Expected date |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

#### payments
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| job_id | INT (FK) | Link to job |
| customer_id | INT (FK) | Payer ID |
| worker_id | INT (FK) | Payee ID |
| amount | DECIMAL(10,2) | Payment amount |
| status | ENUM | Payment status |
| transaction_id | VARCHAR(100) | M-Pesa transaction ID |
| payment_method | VARCHAR(50) | M-Pesa/Cash |
| created_at | TIMESTAMP | Payment date |

#### reviews
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| job_id | INT (FK) | Link to job |
| customer_id | INT (FK) | Reviewer ID |
| worker_id | INT (FK) | Reviewed worker |
| rating | INT(1) | 1-5 stars |
| review_text | TEXT | Written review |
| admin_reviewed | BOOLEAN | Flag for poor ratings |
| created_at | TIMESTAMP | Review date |

---

## API Endpoints

### Authentication
- POST /api/auth/register.php
- POST /api/auth/login.php
- POST /api/auth/logout.php
- GET /api/auth/user.php

### Workers
- GET /api/workers/list.php?category=&lat=&lng=&radius=
- GET /api/workers/profile.php?id=
- POST /api/workers/profile.php (update)
- GET /api/workers/search.php?q=

### Jobs
- POST /api/jobs/create.php
- GET /api/jobs/list.php?user_id=
- GET /api/jobs/detail.php?id=
- PUT /api/jobs/update-status.php

### Payments
- POST /api/payments/create.php
- POST /api/payments/callback.php (M-Pesa webhook)
- GET /api/payments/list.php?job_id=

### Reviews
- POST /api/reviews/create.php
- GET /api/reviews/worker.php?worker_id=

### Admin
- GET /api/admin/workers/pending
- PUT /api/admin/workers/verify
- GET /api/admin/stats

---

## Security Measures

1. **Password Hashing**: bcrypt with cost factor 10
2. **Input Validation**: Server-side validation for all inputs
3. **SQL Injection Prevention**: Prepared statements with PDO
4. **XSS Prevention**: Output escaping
5. **CSRF Protection**: Token-based validation
6. **Session Management**: Secure session handling
7. **Role-Based Access Control**: Middleware for route protection

---

## File Structure

```
hands-on/
├── api/
│   ├── config/
│   │   ├── database.php
│   │   └── constants.php
│   ├── modules/
│   │   ├── auth/
│   │   ├── workers/
│   │   ├── jobs/
│   │   ├── payments/
│   │   ├── reviews/
│   │   └── admin/
│   └── index.php
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── responsive.css
│   ├── js/
│   │   ├── main.js
│   │   ├── auth.js
│   │   ├── workers.js
│   │   ├── jobs.js
│   │   └── map.js
│   └── images/
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── pages/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── workers.php
│   ├── worker-profile.php
│   ├── jobs.php
│   ├── job-detail.php
│   ├── create-job.php
│   ├── payments.php
│   ├── admin/
│   │   ├── index.php
│   │   ├── workers.php
│   │   ├── jobs.php
│   │   └── payments.php
├── uploads/
│   └── profiles/
├── database/
│   └── schema.sql
└── README.md
```

---

## Acceptance Criteria

### Authentication
- [ ] Users can register as customer or worker
- [ ] Users can login with email/phone and password
- [ ] Sessions persist across page loads
- [ ] Role-based access works correctly

### Worker Management
- [ ] Workers can create and edit profiles
- [ ] Profile displays all required information
- [ ] Workers appear in search results by category
- [ ] Location-based filtering works

### Job Requests
- [ ] Customers can create job requests
- [ ] Workers receive and can accept/decline jobs
- [ ] Job status updates correctly
- [ ] Job history is viewable

### Payments
- [ ] Mock payment flow completes
- [ ] Transaction is recorded in database
- [ ] Payment status updates correctly

### Reviews
- [ ] Customers can rate workers after job completion
- [ ] Ratings display on worker profiles
- [ ] Average rating calculates correctly

### Admin
- [ ] Admin can verify workers
- [ ] Admin can view all transactions
- [ ] Admin dashboard shows statistics
