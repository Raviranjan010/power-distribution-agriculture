<?php

/**
 * PDF Generator Script for Project Viva and MCQ bank.
 * Bootstraps Laravel and compiles a beautiful PDF using Barryvdh\DomPDF\Facade\Pdf.
 */

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

echo "Bootstrapping Laravel environment...\n";

// HTML content for the PDF
$html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Project Documentation, Viva Preparation & MCQ Bank</title>
<style>
    @page { 
        margin: 50px 45px 50px 45px; 
    }
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #2b2b2b;
        line-height: 1.5;
        font-size: 10pt;
    }
    .title-page {
        text-align: center;
        padding-top: 100px;
        height: 100%;
    }
    .logo-container {
        font-size: 50pt;
        color: #234817;
        margin-bottom: 20px;
    }
    h1.main-title {
        font-size: 24pt;
        color: #1e3a1e;
        margin-bottom: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .subtitle {
        font-size: 13pt;
        color: #555555;
        margin-bottom: 40px;
        font-style: italic;
    }
    .metadata-box {
        border-top: 2px solid #2e5a27;
        border-bottom: 2px solid #2e5a27;
        padding: 20px 0;
        margin: 50px auto;
        width: 80%;
    }
    .metadata-box table {
        width: 100%;
        border: none;
    }
    .metadata-box td {
        border: none;
        padding: 5px;
        font-size: 11pt;
        text-align: left;
    }
    .author-info {
        margin-top: 100px;
        font-size: 11pt;
    }
    .page-break {
        page-break-after: always;
    }
    h2.section-header {
        font-size: 14pt;
        color: #2e5a27;
        border-bottom: 1.5px solid #d8bd78;
        padding-bottom: 4px;
        margin-top: 25px;
        margin-bottom: 12px;
        page-break-after: avoid;
        text-transform: uppercase;
    }
    h3.subsection-header {
        font-size: 11pt;
        color: #4a6f3a;
        margin-top: 15px;
        margin-bottom: 6px;
        page-break-after: avoid;
    }
    p {
        margin: 0 0 10px 0;
        text-align: justify;
    }
    ul, ol {
        margin: 0 0 10px 0;
        padding-left: 20px;
    }
    li {
        margin-bottom: 4px;
        text-align: justify;
    }
    .code-box {
        font-family: 'Courier New', Courier, monospace;
        font-size: 8.5pt;
        background-color: #f7f7f7;
        border: 1px solid #e1e1e1;
        padding: 8px;
        margin: 8px 0;
        border-radius: 4px;
        word-wrap: break-word;
    }
    .file-tag {
        font-family: 'Courier New', Courier, monospace;
        font-weight: bold;
        font-size: 8.5pt;
        background-color: #f0f4f1;
        color: #2e5a27;
        border: 1px solid #d0dfd3;
        padding: 1px 4px;
        border-radius: 3px;
    }
    .concept-box {
        background-color: #f5f8f5;
        border-left: 4px solid #2e5a27;
        padding: 10px;
        margin-bottom: 15px;
    }
    .q-item {
        margin-bottom: 18px;
        page-break-inside: avoid;
    }
    .q-title {
        font-weight: bold;
        color: #234817;
        font-size: 10.5pt;
        margin-bottom: 4px;
    }
    .q-answer {
        margin-left: 10px;
        padding-left: 10px;
        border-left: 2.5px solid #d8bd78;
        color: #2b2b2b;
    }
    .q-exp {
        font-size: 9pt;
        color: #555;
        background-color: #faf8f5;
        border: 1px solid #f0eae1;
        padding: 6px;
        border-radius: 3px;
        margin-top: 4px;
    }
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 8.5pt;
    }
    table.data-table, table.data-table th, table.data-table td {
        border: 1px solid #dddddd;
    }
    table.data-table th {
        background-color: #e5daae;
        color: #333333;
        font-weight: bold;
        padding: 8px;
        text-align: left;
    }
    table.data-table td {
        padding: 8px;
        vertical-align: top;
    }
    .mcq-q {
        font-weight: bold;
        color: #234817;
        margin-bottom: 4px;
    }
    .mcq-option {
        margin-left: 15px;
        margin-bottom: 3px;
        font-size: 9.5pt;
    }
    .mcq-correct {
        color: #2e5a27;
        font-weight: bold;
    }
    .footer {
        position: fixed;
        bottom: -30px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8pt;
        color: #777777;
        border-top: 1px solid #eeeeee;
        padding-top: 5px;
    }
</style>
</head>
<body>

    <div class="footer">
        Digital Portal for Agricultural Power Distribution · Rajasthan Ministry of Power · Page PageNo
    </div>

    <!-- PAGE 1: TITLE PAGE -->
    <div class="title-page">
        <div class="logo-container">⚡</div>
        <h1 class="main-title">Distribution of Electric Power for Agriculture</h1>
        <div class="subtitle">Ministry of Power Portal — Rajasthan</div>
        
        <div class="metadata-box">
            <table>
                <tr>
                    <td style="font-weight: bold; width: 40%;">Project Domain:</td>
                    <td>Public Utility & E-Governance</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Backend Framework:</td>
                    <td>Laravel 10.10 (PHP 8.1+)</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Frontend Stack:</td>
                    <td>Blade Templates & Tailwind CSS</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Database Engine:</td>
                    <td>MySQL 8.0</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Document Type:</td>
                    <td>Comprehensive Viva Preparation, MCQ Bank & Architecture Guide</td>
                </tr>
            </table>
        </div>
        
        <div class="author-info">
            <p><strong>Developed By:</strong> Ravi Ranjan</p>
            <p><strong>GitHub:</strong> github.com/Raviranjan010</p>
            <p><strong>Purpose:</strong> Academic Thesis & Project Defense</p>
        </div>
    </div>
    
    <div class="page-break"></div>

    <!-- PAGE 2: PROJECT OVERVIEW & ARCHITECTURE -->
    <h2 class="section-header">1. Project Overview & Architecture</h2>
    <p>
        The <strong>Agricultural Electric Power Distribution Portal</strong> is a specialized e-governance platform designed for the Rajasthan Ministry of Power. Its primary goal is to digitize and manage the electrical supply infrastructure dedicated to the agricultural sector. The system coordinates interaction between four core user groups: <strong>Administrators</strong>, <strong>Sub-Divisional Officers (SDOs)</strong>, <strong>Field Linemen</strong>, and <strong>Farmers</strong>.
    </p>
    
    <h3 class="subsection-header">Key Modules:</h3>
    <ul>
        <li><strong>Connection Lifecycle Management:</strong> Farmers request connections (e.g., tubewell, drip irrigation); SDOs review, assign tariffs, and activate meters.</li>
        <li><strong>Metering Pipeline:</strong> Linemen submit physical readings with mandatory GPS coordinates and photo uploads to ensure authenticity. SDOs verify these readings.</li>
        <li><strong>Automated Billing Engine:</strong> Auto-calculates Energy Charges, Fixed Charges, and Taxes, while subtracting applicable government subsidies.</li>
        <li><strong>Crowd-Sourced Outage Tracking:</strong> Integrates community outage reporting that automatically flags high-priority grid issues when 3+ reports occur in the same zone within 30 minutes.</li>
        <li><strong>Subsidy Administration:</strong> Processes farmer claims for schemes like PM-KUSUM, validating uploaded documents.</li>
    </ul>

    <h3 class="subsection-header">MVC Design Pattern Implementation:</h3>
    <p>
        This project uses the Model-View-Controller pattern natively supported by Laravel:
    </p>
    <ul>
        <li><strong>Models (<span class="file-tag">app/Models/</span>):</strong> Define the schema structure, casts, and database relationships (e.g., <span class="file-tag">User.php</span>, <span class="file-tag">Connection.php</span>, <span class="file-tag">Bill.php</span>).</li>
        <li><strong>Views (<span class="file-tag">resources/views/</span>):</strong> Blade templates rendering responsive layouts. Folders are structured by role: `admin/`, `officer/` (SDO), `lineman/`, `farmer/`, and `auth/`.</li>
        <li><strong>Controllers (<span class="file-tag">app/Http/Controllers/</span>):</strong> Handle processing. Key controllers include <span class="file-tag">AuthController.php</span>, <span class="file-tag">FarmerController.php</span>, <span class="file-tag">OfficerController.php</span>, <span class="file-tag">LinemanController.php</span>, and <span class="file-tag">AdminController.php</span>.</li>
    </ul>

    <div class="page-break"></div>

    <!-- PAGE 3: CORE WORKFLOWS - LOGIN & NOTIFICATIONS -->
    <h2 class="section-header">2. Core Workflows (Deep-Dive)</h2>
    
    <h3 class="subsection-header">A. How the Login Process Works</h3>
    <div class="concept-box">
        The login flow secures role-based entry points.
    </div>
    <ol>
        <li><strong>Request Entry:</strong> The user accesses `/login` (route named `login` in <span class="file-tag">routes/web.php</span>), calling `showLogin()` in <span class="file-tag">AuthController.php</span>, which returns the view `auth.login`.</li>
        <li><strong>Form Submission:</strong> The login POST request is routed to `login()` in <span class="file-tag">AuthController.php</span>, wrapped with the `throttle:5,1` middleware to block brute force logins (limits users to 5 attempts per minute).</li>
        <li><strong>Validation:</strong> The inputs are validated to ensure a valid email format and presence of a password.</li>
        <li><strong>Database Authentication:</strong> Laravel calls `Auth::attempt(['email', 'password'])`. The query driver checks the `users` table for the email, gets the bcrypt hashed password, and performs a secure hash match.</li>
        <li><strong>Active Check:</strong> If the hash matches, the system retrieves the User object and checks if `$user->is_active` is true. If false, the session is cleared (`Auth::logout()`) and the user is redirected back with an error.</li>
        <li><strong>Session Protection & Redirect:</strong> If active, Laravel calls `$request->session()->regenerate()` to generate a new session ID (combating session fixation). A `match` statement evaluates `$user->role` and redirects to the corresponding dashboard:
            <ul>
                <li>`admin` &rarr; `admin.dashboard` (/admin/dashboard)</li>
                <li>`sdo` &rarr; `officer.dashboard` (/officer/dashboard)</li>
                <li>`lineman` &rarr; `lineman.dashboard` (/lineman/dashboard)</li>
                <li>`farmer` &rarr; `farmer.dashboard` (/farmer/dashboard)</li>
            </ul>
        </li>
    </ol>

    <h3 class="subsection-header">B. How the Notification System Works</h3>
    <div class="concept-box">
        The notification engine keeps farmers and SDOs informed using database persistence and AJAX polling.
    </div>
    <ol>
        <li><strong>Creation:</strong> When a transaction completes (e.g., SDO approves a connection), the code invokes the notify method: 
            <div class="code-box">$user->notify(new \App\Notifications\RealTimeNotification($title, $message, $url, $icon));</div>
        </li>
        <li><strong>Storage:</strong> The `via()` method of <span class="file-tag">RealTimeNotification.php</span> returns `['database']`. Laravel serializes the notification's array values (title, message, redirect URL, icon) into JSON and stores them in the `notifications` table:
            <div class="code-box">id (UUID) | type | notifiable_type | notifiable_id | data (JSON) | read_at | timestamps</div>
        </li>
        <li><strong>Frontend AJAX Engine:</strong> In the layout file <span class="file-tag">app.blade.php</span>, a JavaScript block runs a `fetchNotifications()` method on DOM load. It sets up polling:
            <div class="code-box">setInterval(fetchNotifications, 8000); // Polls every 8 seconds</div>
        </li>
        <li><strong>Backend Retrieval:</strong> The JavaScript polls `/notifications/poll`, which maps to `poll()` in <span class="file-tag">NotificationController.php</span>. The controller fetches the user's unread notifications:
            <div class="code-box">$unreadCount = $user->unreadNotifications()->count();<br>$notifications = $user->unreadNotifications()->limit(5)->get();</div>
            It returns these as a JSON response containing the message details and time format (e.g., "5 minutes ago").</li>
        <li><strong>Toast Rendering:</strong> The JavaScript reads the JSON. It matches notification IDs against a set stored in browser `localStorage`. If a new ID is found, it adds it to the known set and triggers an animated, slide-in toast card at the bottom-right corner of the screen. Clicking the card marks it as read and redirects the user.</li>
    </ol>

    <div class="page-break"></div>

    <!-- PAGE 4: LANGUAGE CHANGING WORKFLOW -->
    <h2 class="section-header">2. Core Workflows (Continued)</h2>
    
    <h3 class="subsection-header">C. How Language Changing Works (Localization)</h3>
    <div class="concept-box">
        The current portal codebase contains UI comments/hooks for a Language Selector in the layout, but remains statically in English. To implement a dynamic multi-language (English / Hindi) toggle, we use Laravel's localization architecture.
    </div>
    
    <p>
        Here is the standard, best-practice way to implement dynamic language changing in Laravel:
    </p>

    <h4>Step 1: Create Translation Files</h4>
    <p>
        In Laravel 10, translations are stored in the root <span class="file-tag">lang/</span> directory. We create JSON files for translation matching:
    </p>
    <ul>
        <li><strong><span class="file-tag">lang/en.json</span> (English):</strong>
            <div class="code-box">{ "dashboard": "Dashboard", "welcome": "Welcome, :name", "bills": "Bills & Payments" }</div>
        </li>
        <li><strong><span class="file-tag">lang/hi.json</span> (Hindi):</strong>
            <div class="code-box">{ "dashboard": "डैशबोर्ड", "welcome": "स्वागत है, :name", "bills": "बिल और भुगतान" }</div>
        </li>
    </ul>

    <h4>Step 2: Create a Language Controller and Switcher Route</h4>
    <p>
        We register a route in <span class="file-tag">routes/web.php</span> to capture the user's language selection:
    </p>
    <div class="code-box">
        Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');
    </div>
    <p>
        In the controller, we save the chosen locale (e.g., 'en' or 'hi') in the user's session:
    </p>
    <div class="code-box">
        public function switchLang($locale) {<br>
        &nbsp;&nbsp;&nbsp;&nbsp;if (in_array($locale, ['en', 'hi'])) {<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;session(['app_locale' => $locale]);<br>
        &nbsp;&nbsp;&nbsp;&nbsp;}<br>
        &nbsp;&nbsp;&nbsp;&nbsp;return back();<br>
        }
    </div>

    <h4>Step 3: Register SetLocale Middleware</h4>
    <p>
        To apply the language setting for every request, we write a custom middleware <span class="file-tag">SetLocaleMiddleware.php</span>:
    </p>
    <div class="code-box">
        public function handle(Request $request, Closure $next) {<br>
        &nbsp;&nbsp;&nbsp;&nbsp;$locale = session('app_locale', config('app.locale'));<br>
        &nbsp;&nbsp;&nbsp;&nbsp;App::setLocale($locale);<br>
        &nbsp;&nbsp;&nbsp;&nbsp;return $next($request);<br>
        }
    </div>
    <p>
        This middleware is registered in <span class="file-tag">app/Http/Kernel.php</span> under the `web` middleware group.
    </p>

    <h4>Step 4: Update Blade Views</h4>
    <p>
        We replace static strings in Blade views with the translation helper `__('Key')` or blade directives:
    </p>
    <div class="code-box">
        &lt;a href="..."&gt;{{ __('dashboard') }}&lt;/a&gt;<br>
        &lt;h2&gt;@lang('welcome', ['name' => Auth::user()->name])&lt;/h2&gt;
    </div>

    <div class="page-break"></div>

    <!-- PAGE 5: CONCEPT COMPARISONS (WHY THIS INSTEAD OF THAT) -->
    <h2 class="section-header">3. Concept Comparisons (Design Rationale)</h2>
    
    <div class="q-item">
        <div class="q-title">Q1. Why did we use Eloquent ORM instead of Raw SQL Queries or Query Builder?</div>
        <div class="q-answer">
            <strong>Eloquent ORM</strong> is Laravel's ActiveRecord implementation. We selected Eloquent over raw SQL because:
            <ol>
                <li><strong>Readability and Syntactic Simplicity:</strong> Writing `$user->connections` is cleaner than writing `SELECT * FROM connections WHERE consumer_id = ?`.</li>
                <li><strong>Relationship Management:</strong> Eloquent makes relationship definitions (`hasMany`, `belongsTo`) easy, allowing us to eager-load records and avoid N+1 query problems via `$connection->load('consumer')`.</li>
                <li><strong>SQL Injection Protection:</strong> Eloquent automatically utilizes PDO parameter bindings for all queries, protecting the database against injection attacks.</li>
                <li><strong>Events and Mutators:</strong> Eloquent model lifecycle hooks (like `creating`, `updating`) allow us to track audit records easily.</li>
            </ol>
            <em>Trade-off:</em> Raw SQL is slightly faster for complex reports, but for standard operations, Eloquent's benefits outweigh the overhead.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q2. Why did we use Session-Based Authentication instead of JWT (JSON Web Tokens)?</div>
        <div class="q-answer">
            This application is a server-rendered web portal. We chose <strong>Session-Based Authentication</strong> because:
            <ol>
                <li><strong>Implicit Security:</strong> Sessions store user state on the server, while the client only holds an encrypted cookie. JWTs must be stored on the client side (localStorage), making them vulnerable to Cross-Site Scripting (XSS) attacks.</li>
                <li><strong>Immediate Revocation:</strong> In a session-based system, if an Admin deactivates a user, we can call `Auth::logout()` or invalidate their session on the server. With standard JWTs, tokens remain valid until they expire, unless a complex blacklist is implemented.</li>
                <li><strong>Laravel Native Support:</strong> Laravel provides session authentication, secure CSRF matching, and session regeneration utilities out of the box, reducing custom code risks.</li>
            </ol>
            <em>Trade-off:</em> JWTs are better for stateless REST APIs, but session authentication is ideal for a server-rendered Blade template application.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q3. Why did we use Database Transactions with row-level locks (lockForUpdate) instead of basic query executions?</div>
        <div class="q-answer">
            In workflows like generating meter numbers (`MT-XXXXX`) or connection suffixes (`KV-CN-XXXXX`), multiple linemen or farmers could submit requests concurrently. 
            <ul>
                <li>If we query the database using a simple `max()` query and save the record in two separate requests, a race condition can cause both requests to select the same number, resulting in duplicate entries.</li>
                <li>By wrapping the logic inside `DB::transaction(function() { ... })` and calling `lockForUpdate()`, the database places an exclusive read/write lock on the target rows. Any concurrent requests must wait until the current transaction commits or rolls back. This ensures strict sequence numbering.</li>
            </ul>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 6: DIRECTORY MAPPING & CODE CUSTOMIZATION -->
    <h2 class="section-header">4. Directory Mapping & Customization Guide</h2>
    
    <h3 class="subsection-header">A. Where is What? (Core File Locations)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30%;">Feature Module</th>
                <th style="width: 35%;">Controller & Path</th>
                <th style="width: 35%;">Model & Views</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Authentication / Login</strong></td>
                <td><span class="file-tag">app/Http/Controllers/AuthController.php</span><br>Handles login, logout, and registration.</td>
                <td>Model: <span class="file-tag">app/Models/User.php</span><br>Views: <span class="file-tag">resources/views/auth/</span></td>
            </tr>
            <tr>
                <td><strong>SDO Billing / Approvals</strong></td>
                <td><span class="file-tag">app/Http/Controllers/OfficerController.php</span><br>Contains connection approvals and bill calculations.</td>
                <td>Models: `Bill`, `Connection`<br>Views: <span class="file-tag">resources/views/officer/</span></td>
            </tr>
            <tr>
                <td><strong>Farmer Portal / Outages</strong></td>
                <td><span class="file-tag">app/Http/Controllers/FarmerController.php</span><br>Handles connection requests and crowd-sourced outages.</td>
                <td>Models: `OutageReport`, `Complaint`<br>Views: <span class="file-tag">resources/views/farmer/</span></td>
            </tr>
            <tr>
                <td><strong>Lineman Operations</strong></td>
                <td><span class="file-tag">app/Http/Controllers/LinemanController.php</span><br>Handles meter readings and updating complaints.</td>
                <td>Model: <span class="file-tag">app/Models/MeterReading.php</span><br>Views: <span class="file-tag">resources/views/lineman/</span></td>
            </tr>
            <tr>
                <td><strong>Notification Engine</strong></td>
                <td><span class="file-tag">app/Http/Controllers/NotificationController.php</span><br>Handles unread count AJAX polling.</td>
                <td>Class: <span class="file-tag">app/Notifications/RealTimeNotification.php</span></td>
            </tr>
        </tbody>
    </table>

    <h3 class="subsection-header">B. How to Make Changes (Code Customizations)</h3>
    
    <div class="q-item">
        <div class="q-title">Scenario 1: How do you change the billing Tax Rate from 5% to 8%?</div>
        <div class="q-answer">
            Open <span class="file-tag">app/Http/Controllers/OfficerController.php</span>. Under the `generateBills` method, locate the line calculating tax:
            <div class="code-box">
                // OLD CODE:<br>
                $tax = ($ec + $fc) * 0.05;<br><br>
                // NEW CODE:<br>
                $tax = ($ec + $fc) * 0.08;
            </div>
            <strong>Result:</strong> Any future bills generated by SDOs will apply an 8% tax rate, updating the stored net payable amount in the `bills` table.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Scenario 2: How do you change the Outage Crowd-Source alert limit from 3 to 5 reports?</div>
        <div class="q-answer">
            Open <span class="file-tag">app/Http/Controllers/FarmerController.php</span>. Navigate to the `reportOutage` method and look for the check:
            <div class="code-box">
                // OLD CODE:<br>
                if ($reportCount >= 3) { ... }<br><br>
                // NEW CODE:<br>
                if ($reportCount >= 5) { ... }
            </div>
            <strong>Result:</strong> The system will now require at least 5 unique farmers in the same zone to file outage reports within 30 minutes before generating an automated high-priority ticket and SDO notification.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Scenario 3: How do you enforce a minimum password length of 8 instead of 6?</div>
        <div class="q-answer">
            Open <span class="file-tag">app/Http/Controllers/AuthController.php</span>. In both `register` and `resetPassword` methods, modify the validation array:
            <div class="code-box">
                'password' => 'required|min:8|confirmed'
            </div>
            <strong>Result:</strong> Registering farmers or resetting passwords will fail if the input password is less than 8 characters, displaying a validation error.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 7: VIVA QUESTIONS & ANSWERS (PART 1) -->
    <h2 class="section-header">5. Technical Viva Questions & Answers</h2>

    <div class="q-item">
        <div class="q-title">Q1. What version of Laravel does this project use? What are its key requirements?</div>
        <div class="q-answer">
            The project runs on <strong>Laravel 10</strong> and requires <strong>PHP 8.1</strong> or higher. The framework leverages PHP 8 features like constructor promotion, union types, and the `match` expression.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q2. What is the role of Middleware in Laravel, and how is it used in this project?</div>
        <div class="q-answer">
            Middleware acts as a request filter. In our project, we use:
            <ul>
                <li>`auth`: Ensures only logged-in users access internal portals.</li>
                <li>`role:admin|sdo|farmer|lineman`: Custom middleware restricting routes to specific user types. If a user tries to access a route assigned to another role, the middleware redirects them to their correct home dashboard.</li>
                <li>`throttle:5,1`: Limits API endpoints (login/register/password-reset) to 5 attempts per minute to block automated dictionary attacks.</li>
            </ul>
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q3. How is the database seeded with dummy records? What command do we run?</div>
        <div class="q-answer">
            We use seeders and model factories in <span class="file-tag">database/seeders/DatabaseSeeder.php</span> to populate default tariffs, zones, SDOs, linemen, and farmers. We run:
            <div class="code-box">php artisan db:seed</div>
            This populates our schema with demo credentials immediately after running migrations.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q4. How does Laravel protect against Cross-Site Request Forgery (CSRF) in this project?</div>
        <div class="q-answer">
            Laravel's `VerifyCsrfToken` middleware checks all incoming POST, PUT, and DELETE requests. In our blade forms, we include the `@csrf` directive. This renders a hidden input field containing an encrypted token. When submitted, the middleware validates this token against the one stored in the user's session.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q5. Explain the billing formula used by the SDO to generate bills.</div>
        <div class="q-answer">
            The formula calculated inside `generateBills()` is:
            <div class="code-box">
                Energy Charges (EC) = Units Consumed &times; Tariff Rate Per Unit<br>
                Fixed Charges (FC) = Sanctioned Load (kW) &times; Fixed Tariff Charge Per kW<br>
                Taxes = (EC + FC) &times; 5%<br>
                Net Payable = Max( 0, (EC + FC + Taxes) - Subsidy Amount )
            </div>
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q6. What happens if a farmer has no approved subsidy? How does the billing engine behave?</div>
        <div class="q-answer">
            If a farmer has no approved subsidy request but has an active agricultural connection, the billing engine automatically searches for active government schemes (like KUSUM or solar subsidies). It applies the best matching scheme to calculate the bill and logs this under the `consumer_subsidies` table for transparency.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q7. How do you handle file uploads, such as subsidy verification documents or meter photos?</div>
        <div class="q-answer">
            We use Laravel's `Storage` facade:
            <div class="code-box">$path = $request->file('avatar')->store('avatars', 'public');</div>
            This saves the file inside `storage/app/public/avatars/` and returns the relative path. We run the command `php artisan storage:link` to create a symlink from `public/storage` to `storage/app/public`, allowing the files to be accessed via the browser.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 8: VIVA QUESTIONS & ANSWERS (PART 2) -->
    <h2 class="section-header">5. Technical Viva Questions & Answers (Continued)</h2>

    <div class="q-item">
        <div class="q-title">Q8. How does the crowd-sourced outage tracking system prevent spam?</div>
        <div class="q-answer">
            In <span class="file-tag">FarmerController.php</span>, we enforce a time restriction of 30 minutes. If a farmer attempts to submit an outage report, the system queries the `outage_reports` table for a report from that user within the last 30 minutes:
            <div class="code-box">
                $recent = OutageReport::where('farmer_id', $user->id)->where('reported_at', '>=', now()->subMinutes(30))->exists();
            </div>
            If a record exists, the submission is rejected with a validation error.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q9. How are the usage charts on the farmer and admin dashboards rendered?</div>
        <div class="q-answer">
            We use <strong>Chart.js</strong> on the client side. The frontend makes an AJAX fetch request to `/farmer/usage/chart-data`, which returns a JSON array containing labels (the last 12 months) and values (total units consumed by the farmer's active connections). Chart.js parses this JSON to render the line/bar charts.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q10. What is the role of DB migration files in Laravel? What is the command to roll them back?</div>
        <div class="q-answer">
            Migration files act as version control for the database schema, defining database tables, columns, and indexes. To rollback the last migration batch, we run:
            <div class="code-box">php artisan migrate:rollback</div>
            To clear the database and re-run all migrations from scratch, we use:
            <div class="code-box">php artisan migrate:fresh</div>
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q11. Why do we store the password in the database as a hash? What hashing algorithm does Laravel use?</div>
        <div class="q-answer">
            Storing passwords in plain text is a severe security risk. Hashing is a one-way function, meaning a hashed password cannot be decrypted. Laravel uses the **bcrypt** hashing algorithm by default (configured via the `password` cast set to `hashed` in the User model).
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q12. What is the difference between eager loading and lazy loading in Eloquent?</div>
        <div class="q-answer">
            <ul>
                <li><strong>Lazy Loading:</strong> Laravel only queries related data when the relationship property is accessed. This can trigger the N+1 query issue (1 query for connection list + N queries for each connection's farmer details).</li>
                <li><strong>Eager Loading:</strong> We pre-fetch related data using the `with()` method (e.g., `Connection::with('consumer')->get()`). This reduces queries to just 2: one for the connections and one for the users.</li>
            </ul>
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q13. How does the payment simulation work when Razorpay keys are not set?</div>
        <div class="q-answer">
            In <span class="file-tag">FarmerController.php</span>, the system checks if the Razorpay API keys are configured in the `.env` file. If they are absent, the system skips the Razorpay API call, returns a fallback payment view, and generates a local transaction token:
            <div class="code-box">$txnId = 'TXN-' . now()->format('YmdHis') . '-' . $bill->id;</div>
            This marks the bill as paid in the database without calling Razorpay, facilitating local development.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q14. How are Admin audit logs stored, and what columns do they contain?</div>
        <div class="q-answer">
            Whenever an administrator activates/deactivates a user, adds a tariff, or changes a zone, a record is added to the `audit_logs` table. The model stores:
            <ul>
                <li>`user_id`: The ID of the admin who performed the action.</li>
                <li>`action`: E.g., 'deactivated_user', 'created_tariff'.</li>
                <li>`model_type` & `model_id`: Polymorphic references to the modified record.</li>
                <li>`old_values` & `new_values`: JSON columns storing the state before and after the action.</li>
                <li>`ip_address`: The IP address of the admin.</li>
            </ul>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 9: MULTIPLE CHOICE QUESTIONS (MCQ BANK - PART 1) -->
    <h2 class="section-header">6. Multiple Choice Questions (MCQ Bank)</h2>
    
    <div class="q-item">
        <div class="mcq-q">Q1. Which middleware in this Laravel project is responsible for preventing brute-force login attempts?</div>
        <div class="mcq-option">A) auth</div>
        <div class="mcq-option">B) guest</div>
        <div class="mcq-option mcq-correct">C) throttle:5,1</div>
        <div class="mcq-option">D) verifyCsrf</div>
        <div class="q-exp">
            <strong>Correct Answer: C</strong><br>
            The `throttle:5,1` middleware limits a user to 5 requests per minute for login and registration routes, preventing automated dictionary/brute-force attacks.
        </div>
    </div>

    <div class="q-item">
        <div class="mcq-q">Q2. In which folder are the blade templates of the application stored?</div>
        <div class="mcq-option">A) app/Views</div>
        <div class="mcq-option">B) public/views</div>
        <div class="mcq-option mcq-correct">C) resources/views</div>
        <div class="mcq-option">D) storage/views</div>
        <div class="q-exp">
            <strong>Correct Answer: C</strong><br>
            All blade views in a Laravel application are stored in the `resources/views` directory, which Laravel compiles into cached PHP files.
        </div>
    </div>

    <div class="q-item">
        <div class="mcq-q">Q3. SDOs trigger the billing process using the `generateBills` method. What happens to the Net Payable amount if a subsidy is applied?</div>
        <div class="mcq-option">A) Net Payable = Energy + Fixed + Tax + Subsidy</div>
        <div class="mcq-option">B) Net Payable = Energy + Fixed - Subsidy</div>
        <div class="mcq-option mcq-correct">C) Net Payable = Max( 0, (Energy + Fixed + Tax) - Subsidy )</div>
        <div class="mcq-option">D) Net Payable = Energy + Tax - Subsidy</div>
        <div class="q-exp">
            <strong>Correct Answer: C</strong><br>
            The net payable amount adds Energy Charges, Fixed Charges, and Taxes, subtracts the Subsidy Amount, and ensures the total is not negative using `max(0, ...)`.
        </div>
    </div>

    <div class="q-item">
        <div class="mcq-q">Q4. How does the lineman submit readings to the database? Which table is populated?</div>
        <div class="mcq-option">A) users</div>
        <div class="mcq-option">B) connections</div>
        <div class="mcq-option mcq-correct">C) meter_readings</div>
        <div class="mcq-option">D) bills</div>
        <div class="q-exp">
            <strong>Correct Answer: C</strong><br>
            When a lineman submits a reading, the system populates the `meter_readings` table, recording the current unit value, GPS lat/lng, and physical photo path.
        </div>
    </div>

    <div class="q-item">
        <div class="mcq-q">Q5. Which Laravel validation rule guarantees that no two users can register with the same email address?</div>
        <div class="mcq-option">A) required</div>
        <div class="mcq-option">B) email</div>
        <div class="mcq-option mcq-correct">C) unique:users</div>
        <div class="mcq-option">D) distinct</div>
        <div class="q-exp">
            <strong>Correct Answer: C</strong><br>
            The `unique:users` validation rule queries the database to verify that the email address is not already present in the `email` column of the `users` table.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 10: MCQ BANK (PART 2) & SILLY QUESTIONS -->
    <h2 class="section-header">6. MCQ Bank & Silly Questions</h2>
    
    <div class="q-item">
        <div class="mcq-q">Q6. What happens when the SDO approves a connection in OfficerController.php?</div>
        <div class="mcq-option">A) A bill is instantly generated</div>
        <div class="mcq-option mcq-correct">B) A sequential meter number starting with "MT-" is assigned using a DB transaction lock</div>
        <div class="mcq-option">C) The farmer is immediately logged out</div>
        <div class="mcq-option">D) The connection status is set to 'pending'</div>
        <div class="q-exp">
            <strong>Correct Answer: B</strong><br>
            Upon SDO approval, a sequential meter ID like `MT-10001` is assigned to the connection inside a transaction using a database row lock.
        </div>
    </div>

    <div class="q-item">
        <div class="mcq-q">Q7. What is the database engine recommended for this Laravel project in the environment settings?</div>
        <div class="mcq-option">A) SQLite</div>
        <div class="mcq-option mcq-correct">B) MySQL 8.0</div>
        <div class="mcq-option">C) PostgreSQL</div>
        <div class="mcq-option">D) MongoDB</div>
        <div class="q-exp">
            <strong>Correct Answer: B</strong><br>
            The project configuration is set up for **MySQL 8.0**, which handles e-governance transactions and relational indexing.
        </div>
    </div>

    <h2 class="section-header">7. Silly & Normal Questions (FAQ)</h2>

    <div class="q-item">
        <div class="q-title">Q1. What is the `.env` file, and why is it excluded from Git?</div>
        <div class="q-answer">
            The `.env` (Environment) file stores local system configurations, such as database credentials, SMTP server details, and Razorpay API secret keys. It is excluded from Git to prevent sensitive keys from being exposed in public repositories.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q2. What happens if I delete the `vendor` folder? How do I restore it?</div>
        <div class="q-answer">
            The `vendor` folder contains all framework files and third-party PHP packages (e.g., DomPDF, Razorpay SDK). If deleted, the application will crash. You can restore it by running:
            <div class="code-box">composer install</div>
            This reads the `composer.json` and `composer.lock` files to download the correct package versions.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q3. Why does the page style look broken when I clone the project on a new system?</div>
        <div class="q-answer">
            This project uses Tailwind CSS assets compiled using **Vite**. If the assets are not compiled, the CSS will fail to load. To fix this, run:
            <div class="code-box">npm install<br>npm run build</div>
            This compiles the CSS and JS files into the public directory, restoring the layouts.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q4. What is `php artisan key:generate` used for?</div>
        <div class="q-answer">
            This command generates a cryptographically secure 32-character string and updates the `APP_KEY` variable in the `.env` file. Laravel uses this key to encrypt user sessions, cookies, and other encrypted fields. Without it, the application will refuse to start.
        </div>
    </div>

    <div class="q-item">
        <div class="q-title">Q5. Where is the database name defined? How do I change it?</div>
        <div class="q-answer">
            The database name is defined in the `.env` file under the key `DB_DATABASE` (e.g., `DB_DATABASE=power_distribution`). You can change this value to point to another MySQL database schema.
        </div>
    </div>

</body>
</html>
HTML;

// Generate PDF
try {
    $pdf = Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'portrait');
    
    $outputPath = __DIR__ . '/project_viva_mcqs.pdf';
    echo "Compiling PDF using DomPDF...\n";
    $pdf->save($outputPath);
    echo "PDF generated successfully at: " . $outputPath . "\n";
} catch (\Exception $e) {
    echo "Error generating PDF: " . $e->getMessage() . "\n";
    Log::error("PDF Generation error: " . $e->getMessage());
}
