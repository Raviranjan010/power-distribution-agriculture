# ⚡ Distribution of Electric Power for Agriculture

### Ministry of Power Portal — india

A comprehensive web portal for managing agricultural electricity distribution across india. The system digitises connection management, metering, billing, complaint resolution, and subsidy administration — connecting farmers, field linemen, SDO officers, and administrators on a single platform.

---

## 🛠️ Tech Stack

| Layer        | Technology                                      |
|-------------|--------------------------------------------------|
| Backend      | Laravel 10 · PHP 8.1                            |
| Database     | MySQL 8                                          |
| Frontend     | Blade Templates · Tailwind CSS                  |
| Charts       | Chart.js                                         |
| Payments     | Razorpay SDK (production) · Simulated (dev)     |
| Icons        | Font Awesome 6                                   |
| Auth         | Laravel built-in (session-based, role middleware)|

---

## 👥 User Roles & Capabilities

### 🔴 Admin
- View system-wide dashboard with revenue charts, zone stats, complaint resolution rates
- Create and manage users (SDO, Lineman, Admin)
- Activate / deactivate any user account
- Configure tariff categories and rates
- Create and manage government subsidy schemes
- View full audit logs of all system actions

### 🔵 SDO (Sub-Divisional Officer)
- View zone-level dashboard with pending connections, open complaints, pending readings
- Approve or reject new farmer connection requests with tariff assignment
- Verify meter readings submitted by linemen
- Generate monthly bills for the entire zone
- Assign complaints to field linemen
- Approve or reject farmer subsidy applications

### 🟡 Lineman
- View assigned complaints and update their status (in progress / resolved)
- Submit meter readings for active connections in their zone
- Track monthly reading submissions

### 🟢 Farmer
- Register with Aadhaar verification and zone-linked district selection
- Apply for new electricity connections (tubewell, irrigation motor, thresher, drip)
- View connection status, tariff details, and meter reading history
- View and pay electricity bills (with payment confirmation flow)
- Track 12-month electricity usage with interactive charts
- File grievance complaints with priority levels
- Apply for government subsidy schemes
- Access help and support resources

---

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 8
- Node.js & npm (for asset compilation, if needed)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/Raviranjan010/power-distribution-agriculture.git
cd power-distribution-agriculture

# 2. Install PHP dependencies
composer install

# 3. Create environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env
# DB_DATABASE=power_distribution
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 6. Run migrations
php artisan migrate

# 7. Seed the database with demo data
php artisan db:seed

# 8. Link storage for avatar and file uploads
php artisan storage:link

# 9. Install and compile frontend assets (Required for CSS to load)
npm install
npm run build

# 10. Start the development server
php artisan serve
```

The application will be available at `http://localhost:8000`

### Razorpay (Optional — Production)

To enable real payment processing, add your Razorpay credentials to `.env`:

```env
RAZORPAY_KEY=rzp_live_xxxxxxxxxxxxxxx
RAZORPAY_SECRET=your_razorpay_secret
```

If these keys are not set, the system falls back to a simulated payment flow.

---

## 🔐 Default Login Credentials

All seeded accounts use the password: **`password`**

> ⚠️ **IMPORTANT:** Change the admin password immediately after seeding. Default passwords are for development only.

| Role     | Email                              | Description           |
|---------|-------------------------------------|-----------------------|
| Admin    | `admin@punjabpower.gov.in`         | Super Administrator   |
| SDO      | `sdo.1@punjabpower.gov.in`         | SDO — Zone 1          |
| SDO      | `sdo.2@punjabpower.gov.in`         | SDO — Zone 2          |
| Lineman  | `lineman.1@punjabpower.gov.in`     | Lineman — Zone 1      |
| Lineman  | `lineman.2@punjabpower.gov.in`     | Lineman — Zone 2      |
| Farmer   | `harjit.singh@gmail.com`           | Farmer — Nawanshahr   |
| Farmer   | `gurpreet.kaur@gmail.com`          | Farmer — Nawanshahr   |
| Farmer   | `manjit.singh@gmail.com`           | Farmer — Phagwara     |
| Farmer   | `balwinder.singh@gmail.com`        | Farmer — Hoshiarpur   |
| Farmer   | `surinder.singh@gmail.com`         | Farmer — Jalandhar    |

---

## ✨ Features

- **Multi-role Authentication** — Role-based access control with middleware protection
- **Connection Lifecycle** — Request → SDO Approval → Tariff Assignment → Active
- **Metering Pipeline** — Lineman Reading → SDO Verification → Bill Generation
- **Billing & Payments** — Auto-generated bills with payment confirmation flow and Razorpay integration
- **Complaint Management** — File → Assign to Lineman → In Progress → Resolved
- **Subsidy Administration** — Government schemes with farmer applications and SDO approval
- **Interactive Dashboards** — Real-time charts and KPIs for every role
- **Audit Trail** — Complete logging of administrative actions
- **Zone-based Architecture** — Data isolation and management by geographic zones
- **Responsive Design** — Dark-themed UI optimised for desktop and mobile
- **Farmer ID System** — Auto-generated unique IDs (KV-YYYY-XXXX format)
- **GRV Tracking** — Unique grievance numbers for complaint tracking

---

## 📸 Screenshots

<!-- Add screenshots here -->

| Dashboard | Description |
|-----------|-------------|
| ![Admin Dashboard](screenshots/admin-dashboard.png) | Admin overview with revenue charts and zone statistics |
| ![Farmer Dashboard](screenshots/farmer-dashboard.png) | Farmer portal with usage graphs and quick actions |
| ![Officer Dashboard](screenshots/officer-dashboard.png) | SDO panel with pending approvals and complaints |
| ![Bill Payment](screenshots/bill-payment.png) | Payment confirmation flow with bill details |

> 📌 *Create a `screenshots/` directory and add your screenshots to display them here.*

---

## 📁 Project Structure

```
├── app/Http/Controllers/
│   ├── AdminController.php       # Admin dashboard, users, tariffs, subsidies
│   ├── AuthController.php        # Login, register, logout
│   ├── FarmerController.php      # Farmer dashboard, bills, complaints, usage
│   ├── LinemanController.php     # Lineman dashboard, readings, complaints
│   └── OfficerController.php     # SDO dashboard, approvals, bill generation
├── app/Models/                   # Eloquent models (User, Connection, Bill, etc.)
├── database/
│   ├── migrations/               # Schema definitions
│   └── seeders/                  # Demo data seeders
├── resources/views/
│   ├── admin/                    # Admin panel views
│   ├── auth/                     # Login and registration
│   ├── farmer/                   # Farmer portal views
│   ├── lineman/                  # Lineman panel views
│   ├── officer/                  # SDO panel views
│   └── layouts/                  # Base layout template
└── routes/web.php                # All application routes
```

---

## 👤 Author

**Ravi Ranjan, Mansi  Singh and Ayush Jha**
<!-- Update with your college name below -->
*[Your College Name Here]*

GitHub: [@Raviranjan010](https://github.com/Raviranjan010)

---

## 📄 License

This project is built for academic purposes under the Ministry of Power domain.
