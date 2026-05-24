# ⚡ Project Presentation Overview: Agricultural Electric Power Distribution Portal
### Ministry of Power Portal — Rajasthan

This document serves as your master guide for presenting the project to examiners, faculty, or technical panels. It summarizes the project's purpose, operational workflow, feature modules, database relations, security measures, and provides a step-by-step live demonstration script.

---

## 📋 1. Project Identity & Domain
*   **Project Name:** Distribution of Electric Power for Agriculture (Ministry of Power Portal — Rajasthan)
*   **Domain:** Public Utility, E-Governance, Resource Management & Grievance Redressal
*   **Target Audience:** Rajasthan Ministry of Power, Farmers (Consumers), Sub-Divisional Officers (SDOs), and Field Linemen.

---

## 🎯 2. Problem Statement & Objectives

### The Challenges in Traditional Systems:
1.  **Approval Delays:** New connection requests for tubewells or irrigation pumps take months due to paper-based filing, lack of transparency, and physical handoffs.
2.  **Billing Inaccuracies:** Manual meter readings recorded on paper are prone to recording errors, leading to consumer disputes and utility revenue leakages.
3.  **Subsidy Opaque/Leakage:** Complex government subsidy schemes (like PM-KUSUM) are calculated manually, making it difficult for farmers to track applied benefits.
4.  **Slow Outage Redressal:** Power cuts or transformer failures are reported individually, leading to duplicate reports, delayed lineman dispatch, and extended outages.
5.  **Lack of Accountability:** Administrative operations have no tracking trail, creating opportunities for unauthorized tariff changes or account status toggles.

### Project Objectives:
*   Establish a **multi-role portal** connecting Admin, SDOs, Linemen, and Farmers.
*   Automate the **connection approval pipeline** with transaction-safe sequential meter assignments.
*   Provide a **verification pipeline** for linemen submissions requiring GPS coordinates and physical meter photos.
*   Implement a **dynamic billing engine** that automatically calculates energy, fixed charges, taxes, and applies eligible subsidies.
*   Implement **crowd-sourced outage detection** that automatically flags high-priority grid issues and alerts zone SDOs.
*   Secure all transactions using **immutable audit logs**, session protections, and rate-limiting.

---

## ✨ 3. Core Modules & Feature Walkthrough

### 🟢 A. Farmer Portal
*   **Secure Registration:** Registers with Name, Phone, Aadhaar number, and Zone. It automatically generates a unique Farmer ID (e.g., `KV-2026-0001`).
*   **Connection Requests:** Farmers apply for connections (tubewell, drip irrigation, thresher) by declaring their field location and sanctioned load (kW).
*   **Smart Bill Prediction Widget:** Extrapolates current month's usage to estimate the final bill amount and alerts the farmer if they are trending 20%+ above their historical average.
*   **Digital Billing & Payments:** Downloads official PDF invoices and pays outstanding balances via simulated or live Razorpay gateways.
*   **Interactive Analytics:** Visualizes 12-month historical consumption trends using dynamic Chart.js graphs.
*   **Grievance Center & Outage Reporting:** Files complaints with priority tags and reports power outages with a single tap.

### 🟡 B. Lineman Operations
*   **Mobile-Optimized Dashboard:** Linemen view active connections and assigned grievances in their zone.
*   **Verification Submission:** Enters current readings, captures device GPS coordinates (ensuring physical presence), and uploads a photo of the meter dial.
*   **Grievance Resolution:** Updates assigned complaints (e.g., changing status to "In Progress" or "Resolved" with remarks).

### 🔵 C. Officer (SDO) Operations
*   **Zone Overview:** Tracks active connections, revenue totals, open complaints, and pending approvals.
*   **Connection Lifecycle:** Reviews connection requests, assigns tariff categories, and approves them to generate sequential meter IDs (`MT-XXXXX`).
*   **Reading Verification:** Reviews lineman readings, validating coordinates and photo uploads before marking them as verified.
*   **Automated Billing Run:** Triggers the billing cycle for the zone with a single click, auto-calculating all charges and applying subsidies.
*   **Subsidy Administration:** Approves or rejects farmer subsidy applications (e.g., verifying uploaded documents).
*   **Outage Grievances:** Monitors automated crowd-sourced outage alerts to dispatch linemen.

### 🔴 D. Admin Controls
*   **User Management:** Creates, activates, or deactivates SDO, Lineman, and Admin accounts.
*   **Tariff Category Manager:** Creates and edits rates per unit, fixed charges per kW, and effective dates.
*   **Subsidy Scheme Manager:** Defines schemes (e.g., PM-KUSUM), discounts, cap units, and active dates.
*   **Audit Trail Viewer:** Inspects system modification logs (IP, Operator, model affected, old and new values in JSON formats) to enforce security.

---

## 🛠️ 4. Technology Stack & Design Rationale

| Layer | Technology | Rationale |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 10 (PHP 8.1+) | MVC structure, built-in Eloquent ORM, database query safety, and robust authentication middleware. |
| **Database Engine** | MySQL 8.0 | Strong relational integrity, optimized indexing, and transaction-safety (ACID compliance). |
| **Frontend Templates** | Blade Engine & Tailwind CSS | Dynamic page rendering, responsive layouts, utility-first design for mobile compatibility. |
| **Document Generation** | Barryvdh DomPDF | Converts HTML layouts to official PDF bills and reports. |
| **Data Charts** | Chart.js | Renders interactive, client-side consumption graphs from JSON data. |
| **Payment Gateway** | Razorpay SDK | Secure online transactions with signature verification. |
| **Infrastructure** | Docker, Nginx, PHP-FPM | Containerized setup for deployment environments (like Render). |

---

## 🔐 5. Database Schema & Entity Relationships

The relational design ensures data integrity across all features:
*   `users` table: Holds details of all users; uses a `role` field (`admin`, `sdo`, `lineman`, `farmer`) to control permissions.
*   `zones` table: Groups geographical areas; links one SDO to many Farmers and Linemen.
*   `connections` table: Tracks connection numbers, status (`pending`, `active`, `rejected`), meter numbers, load capacity, and belongs to a farmer user and a tariff category.
*   `meter_readings` table: Stores readings, photos, and GPS coordinates submitted by linemen; belongs to a connection and lineman.
*   `bills` table: Logs generated invoices, monthly units, rates, taxes, applied subsidies, net payable, and payment status.
*   `payments` table: Records transaction IDs, payment methods, timestamps, and gateway responses; belongs to a bill and user.
*   `complaints` table: Tracks complaints, grievance numbers (`GRV-YYYY-XXXX`), priority levels, assigned lineman, and resolution remarks.
*   `subsidy_schemes` & `consumer_subsidies` tables: Manages schemes and logs farmer applications, document uploads, and SDO approvals.
*   `audit_logs` table: Logs system actions, Operator ID, IP address, and JSON strings of changed values.

---

## 🚀 6. Step-by-Step Live Presentation Script

During your viva or project presentation, guide the examiners through this sequential workflow:

### Step 1: User Login & Role Routing
1.  Navigate to `/login`. Log in as Admin (`admin@punjabpower.gov.in`, password: `password`).
2.  **Speak:** *"I am logging in as the Admin. The portal uses session-based authentication. Laravel's RoleMiddleware evaluates the user's role and redirects them accordingly. As an admin, I am routed to `/admin/dashboard`."*
3.  Show the system metrics, revenue totals, and the **Audit Log** page showcasing captured actions.

### Step 2: Tariff & Subsidy Scheme Configuration
1.  Navigate to **Tariffs** under the Admin menu. Click **Add New Tariff** (e.g., Agricultural Tariff: Rate ₹2.50/unit, Fixed charge ₹50.00/kW).
2.  Navigate to **Subsidies**. Create a scheme (e.g., PM-KUSUM: 80% discount, cap 300 units).
3.  **Speak:** *"As the Admin, I configure tariffs and subsidies. This action is logged in our audit trails. These settings drive the automated billing calculations."*

### Step 3: Farmer Registration & Connection Request
1.  Log out. Click **Register** to create a new Farmer profile, selecting a zone, and entering an Aadhaar number.
2.  **Speak:** *"When a new farmer registers, the system validates the uniqueness of their phone and Aadhaar numbers. It generates a unique Farmer ID (e.g., KV-2026-0001)."*
3.  Log in as the farmer. Go to **Connections** and click **Apply for Connection** (e.g., Tubewell, 5 kW load).
4.  **Speak:** *"I am submitting a connection request. The system wraps this in a database transaction, uses `lockForUpdate()` to avoid duplicate connection numbers, and saves the request as 'pending' before notifying SDOs."*

### Step 4: SDO Approval & Activation
1.  Log out. Log in as SDO (`sdo.1@punjabpower.gov.in`, password: `password`).
2.  Find the pending request, select the agricultural tariff category, and click **Approve**.
3.  **Speak:** *"As the SDO, I approve this connection. The backend starts a transaction, increments the last meter number to generate a sequential meter ID (e.g., MT-10001), marks the connection as active, and dispatches email and real-time alerts to the farmer."*

### Step 5: Lineman Reading Submission
1.  Log out. Log in as Lineman (`lineman.1@punjabpower.gov.in`, password: `password`).
2.  Find the active connection under "Submit Readings". Enter a reading of `200` units, enter latitude/longitude coordinates, and upload a meter photo. Submit.
3.  **Speak:** *"As the lineman in the field, I submit the meter reading. The system requires GPS coordinates and a photo upload to verify the lineman was physically present, preventing reading fraud."*

### Step 6: SDO Verification & Billing Cycle
1.  Log out. Log in as the SDO. Find the reading under "Pending Readings" and click **Verify**.
2.  Click **Generate Bills**.
3.  **Speak:** *"The SDO verifies the reading. Once verified, the billing engine is run. It calculates energy charges, fixed load charges, and a 5% tax. Since this is an agricultural connection and the farmer has no pre-approved subsidy, the billing engine auto-applies our solar/KUSUM scheme, updates net payable using the formula, and generates a PDF bill."*

### Step 7: Farmer Bill Payment & Outage Redressal
1.  Log out. Log in as the Farmer. Navigate to **Bills** to find the invoice. Click **Pay Now** and complete the payment simulation.
2.  Go to the dashboard and click **Report Outage**.
3.  **Speak:** *"The farmer receives their bill, reviews the calculation, and pays. The payment updates the status to 'paid' and logs the transaction. If a power cut occurs, the farmer can report it. If 3 or more unique reports are filed in the same zone within 30 minutes, the system automatically creates a high-priority grievance ticket and alerts the SDO."*
