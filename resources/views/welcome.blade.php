<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry of Power & Agriculture | Rural Power Distribution</title>
    <meta name="description" content="A Ministry of Power and Agriculture platform for agricultural electricity connections, billing, subsidies, usage monitoring, and grievance redressal.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --paper: #f8f4e9;
            --paper-deep: #eee5d3;
            --ink: #1e241d;
            --muted: #667060;
            --leaf: #234817;
            --leaf-soft: #4f6f31;
            --moss: #7a8f4a;
            --wheat: #d8bd78;
            --copper: #9a5933;
            --clay: #b9764e;
            --line: rgba(47, 59, 39, 0.16);
            --glass: rgba(255, 253, 246, 0.58);
            --glass-strong: rgba(255, 253, 246, 0.78);
            --shadow: 0 24px 70px rgba(56, 48, 33, 0.16);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--paper);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page-shell {
            width: min(1220px, calc(100% - 32px));
            margin: 0 auto;
        }

        .glass {
            border: 1px solid rgba(255, 255, 255, 0.72);
            background: var(--glass);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78), 0 18px 45px rgba(54, 45, 29, 0.13);
            backdrop-filter: blur(22px) saturate(1.08);
            -webkit-backdrop-filter: blur(22px) saturate(1.08);
        }

        .site-header {
            position: sticky;
            top: 14px;
            z-index: 20;
            padding: 14px 0 2px;
        }

        .nav {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
            padding: 12px 16px 12px 18px;
            border-radius: 26px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 224px;
        }

        .emblem {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            color: var(--leaf);
            border-radius: 16px;
            background: rgba(246, 241, 226, 0.88);
            border: 1px solid rgba(35, 72, 23, 0.16);
        }

        .brand-title {
            margin: 0;
            font-size: 13px;
            line-height: 1.18;
            font-weight: 800;
            letter-spacing: 0;
        }

        .brand-title span {
            display: block;
            color: var(--muted);
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex: 1;
        }

        .nav-links a {
            padding: 10px 14px;
            border-radius: 999px;
            color: #3e473a;
            font-size: 13px;
            font-weight: 700;
            transition: background 180ms ease, color 180ms ease, transform 180ms ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--leaf);
            background: rgba(35, 72, 23, 0.09);
            transform: translateY(-1px);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            transition: transform 180ms ease, background 180ms ease, border-color 180ms ease, color 180ms ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            color: #fffaf0;
            background: var(--leaf);
            border-color: rgba(255, 255, 255, 0.22);
            box-shadow: 0 14px 30px rgba(35, 72, 23, 0.22);
        }

        .btn-primary:hover {
            background: #17310f;
        }

        .btn-plain {
            color: var(--leaf);
            background: rgba(255, 253, 246, 0.52);
            border-color: rgba(35, 72, 23, 0.18);
        }

        .hero {
            position: relative;
            overflow: hidden;
            margin-top: 18px;
            min-height: 690px;
            display: grid;
            grid-template-columns: minmax(0, 0.88fr) minmax(460px, 1.12fr);
            gap: 34px;
            padding: 60px 54px 44px;
            border-radius: 38px;
            background: rgba(255, 253, 246, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.84);
            box-shadow: var(--shadow);
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0 52% 0 0;
            pointer-events: none;
            background: rgba(255, 253, 246, 0.62);
            border-right: 1px solid rgba(35, 72, 23, 0.08);
        }

        .hero-copy,
        .hero-visual {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 26px;
            padding: 9px 14px;
            border-radius: 999px;
            color: var(--leaf);
            background: rgba(229, 218, 174, 0.34);
            border: 1px solid rgba(35, 72, 23, 0.12);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1,
        h2 {
            margin: 0;
            color: var(--ink);
            font-family: "Libre Baskerville", Georgia, serif;
            letter-spacing: 0;
        }

        h1 {
            max-width: 560px;
            font-size: clamp(44px, 5.3vw, 78px);
            line-height: 1.02;
        }

        h1 span,
        h2 span {
            color: var(--leaf);
        }

        .hero-copy p {
            max-width: 500px;
            margin: 24px 0 34px;
            color: #50594d;
            font-size: 18px;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .underlink {
            position: relative;
            color: var(--ink);
            font-weight: 800;
            font-size: 13px;
            padding: 14px 0;
        }

        .underlink::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 8px;
            height: 2px;
            background: var(--leaf);
            transform-origin: left;
            transition: transform 180ms ease;
        }

        .underlink:hover::after {
            transform: scaleX(0.58);
        }

        .hero-note {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 56px;
            max-width: 520px;
        }

        .mini-stat {
            min-height: 96px;
            padding: 18px;
            border-radius: 24px;
        }

        .mini-stat strong {
            display: block;
            color: var(--leaf);
            font-family: "Libre Baskerville", Georgia, serif;
            font-size: 27px;
            line-height: 1;
        }

        .mini-stat span {
            display: block;
            margin-top: 8px;
            color: #5f685b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
        }

        .hero-visual {
            min-height: 540px;
        }

        .image-sculpture {
            position: absolute;
            inset: 0 0 0 22px;
        }

        .main-photo,
        .power-photo {
            position: absolute;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            border: 1px solid rgba(255, 255, 255, 0.72);
            box-shadow: 0 24px 58px rgba(65, 57, 41, 0.18);
        }

        .main-photo {
            right: 0;
            top: 18px;
            width: min(520px, 84%);
            height: 520px;
            border-radius: 46% 30% 42% 24%;
            background-image: url("https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=82");
            animation: floatSlow 7s ease-in-out infinite;
        }

        .power-photo {
            left: 0;
            top: 82px;
            width: 360px;
            height: 410px;
            border-radius: 42% 34% 28% 46%;
            background-image: url("https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=900&q=82");
            background-position: 52% 50%;
            filter: sepia(0.26) saturate(0.86);
            animation: floatSlow 8.5s ease-in-out infinite reverse;
        }

        .leaf-pin {
            position: absolute;
            right: 24px;
            top: -4px;
            width: 130px;
            height: 84px;
            border-radius: 70% 0 70% 0;
            background: #6e8738;
            transform: rotate(-16deg);
            border: 1px solid rgba(35, 72, 23, 0.22);
            box-shadow: 0 18px 34px rgba(57, 74, 34, 0.18);
        }

        .leaf-pin::after {
            content: "";
            position: absolute;
            left: 15px;
            right: 15px;
            top: 42px;
            height: 1px;
            background: rgba(255, 253, 246, 0.58);
            transform: rotate(-22deg);
        }

        .floating-card {
            position: absolute;
            z-index: 3;
            display: grid;
            gap: 8px;
            padding: 22px;
            border-radius: 28px;
            color: var(--ink);
            animation: liftIn 700ms ease both;
        }

        .floating-card i {
            color: var(--leaf);
            font-size: 24px;
        }

        .floating-card strong {
            font-size: 15px;
            line-height: 1.35;
        }

        .card-one {
            left: 24px;
            top: 290px;
            width: 205px;
        }

        .card-two {
            right: 16px;
            bottom: 112px;
            width: 226px;
            animation-delay: 120ms;
        }

        .seal {
            position: absolute;
            left: 218px;
            bottom: 0;
            width: 152px;
            height: 152px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: var(--leaf);
            background: rgba(255, 253, 246, 0.74);
            border: 1px solid rgba(35, 72, 23, 0.18);
            box-shadow: inset 0 0 0 12px rgba(231, 222, 197, 0.58), 0 20px 42px rgba(65, 57, 41, 0.18);
            animation: turnSeal 22s linear infinite;
        }

        .seal span {
            width: 96px;
            height: 96px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            border: 1px solid rgba(35, 72, 23, 0.28);
            font-size: 34px;
        }

        .section {
            padding: 68px 0 0;
        }

        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 26px;
            margin-bottom: 28px;
        }

        .section h2 {
            max-width: 620px;
            font-size: clamp(30px, 3vw, 46px);
            line-height: 1.14;
        }

        .label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            padding: 8px 13px;
            border-radius: 999px;
            color: #6e641f;
            background: rgba(216, 189, 120, 0.24);
            border: 1px solid rgba(154, 89, 51, 0.13);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .step-card {
            position: relative;
            min-height: 230px;
            padding: 28px 24px;
            border-radius: 32px;
            overflow: hidden;
            transition: transform 180ms ease, border-color 180ms ease;
        }

        .step-card:hover {
            transform: translateY(-6px);
            border-color: rgba(35, 72, 23, 0.22);
        }

        .step-card::after {
            content: "";
            position: absolute;
            width: 84px;
            height: 84px;
            right: -22px;
            top: -18px;
            border-radius: 50%;
            background: rgba(216, 189, 120, 0.20);
            border: 1px solid rgba(255, 255, 255, 0.62);
        }

        .step-card i {
            color: var(--leaf-soft);
            font-size: 30px;
        }

        .step-card .number {
            position: absolute;
            right: 24px;
            top: 24px;
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: var(--copper);
            background: rgba(255, 253, 246, 0.72);
            border: 1px solid rgba(154, 89, 51, 0.14);
            font-family: "Libre Baskerville", Georgia, serif;
            font-size: 18px;
            font-weight: 700;
        }

        .step-card h3,
        .feature h3 {
            margin: 28px 0 10px;
            color: var(--ink);
            font-size: 17px;
        }

        .step-card p,
        .feature p {
            margin: 0;
            color: #616a5d;
            line-height: 1.65;
            font-size: 14px;
        }

        .why-grid {
            display: grid;
            grid-template-columns: 0.9fr 1.4fr;
            gap: 32px;
            align-items: stretch;
        }

        .why-copy {
            padding: 34px;
            border-radius: 34px;
            background: #efe7d5;
            border: 1px solid rgba(35, 72, 23, 0.12);
        }

        .why-copy p {
            max-width: 430px;
            color: #5c6558;
            line-height: 1.72;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .feature {
            padding: 28px;
            border-radius: 30px;
        }

        .feature i {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 20px;
            color: var(--leaf);
            background: rgba(216, 189, 120, 0.20);
            border: 1px solid rgba(35, 72, 23, 0.12);
            font-size: 21px;
        }

        .impact {
            margin: 68px 0 38px;
            display: grid;
            grid-template-columns: 1.1fr repeat(4, 1fr);
            gap: 1px;
            overflow: hidden;
            border-radius: 30px;
            background: rgba(255, 253, 246, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.26);
            box-shadow: 0 20px 52px rgba(35, 72, 23, 0.18);
        }

        .impact-cell {
            min-height: 132px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 26px;
            color: #fff9ea;
            background: var(--leaf);
        }

        .impact-cell:first-child {
            gap: 10px;
            background: #203f16;
        }

        .impact-cell strong {
            font-family: "Libre Baskerville", Georgia, serif;
            color: var(--wheat);
            font-size: 32px;
            line-height: 1;
        }

        .impact-cell span {
            margin-top: 9px;
            color: rgba(255, 249, 234, 0.78);
            font-size: 13px;
            line-height: 1.45;
        }

        footer {
            padding: 0 0 34px;
            color: #68705f;
            font-size: 13px;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .reveal {
            animation: rise 720ms ease both;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatSlow {
            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-14px) rotate(1deg);
            }
        }

        @keyframes liftIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes turnSeal {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 980px) {
            .nav {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .nav-links {
                order: 3;
                width: 100%;
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 2px;
            }

            .hero {
                grid-template-columns: 1fr;
                padding: 42px 24px;
            }

            .hero-visual {
                min-height: 520px;
            }

            .image-sculpture {
                inset: 0;
            }

            .steps,
            .features {
                grid-template-columns: repeat(2, 1fr);
            }

            .why-grid,
            .impact {
                grid-template-columns: 1fr;
            }

            .impact {
                gap: 1px;
            }
        }

        @media (max-width: 640px) {
            .page-shell {
                width: min(100% - 22px, 1220px);
            }

            .site-header {
                top: 8px;
            }

            .brand {
                min-width: 0;
            }

            .nav-actions {
                width: 100%;
            }

            .nav-actions .btn {
                flex: 1;
            }

            .hero {
                min-height: 0;
                border-radius: 28px;
                padding: 34px 18px 24px;
            }

            h1 {
                font-size: 42px;
            }

            .hero-copy p {
                font-size: 16px;
            }

            .hero-note,
            .steps,
            .features {
                grid-template-columns: 1fr;
            }

            .hero-visual {
                min-height: 450px;
            }

            .main-photo {
                width: 88%;
                height: 390px;
            }

            .power-photo {
                width: 58%;
                height: 310px;
                top: 70px;
            }

            .card-one {
                top: 240px;
                left: 4px;
            }

            .card-two {
                right: 2px;
                bottom: 46px;
            }

            .seal {
                left: 38%;
                width: 116px;
                height: 116px;
            }

            .seal span {
                width: 74px;
                height: 74px;
                font-size: 26px;
            }

            .section-head,
            .footer-inner {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <header class="site-header">
        <nav class="page-shell nav glass" aria-label="Primary navigation">
            <a class="brand" href="#">
                <span class="emblem" aria-hidden="true"><i class="fa-solid fa-landmark"></i></span>
                <p class="brand-title">Ministry of Power <span>& Agriculture</span></p>
            </a>

            <div class="nav-links">
                <a class="active" href="#home">Home</a>
                <a href="#services">Services</a>
                <a href="#process">Process</a>
                <a href="#impact">Impact</a>
                <a href="#contact">Contact</a>
            </div>

            <div class="nav-actions">
                <a class="btn btn-plain" href="{{ route('login') }}"><i class="fa-regular fa-user"></i> Sign In</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Apply Now <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </nav>
    </header>

    <main id="home" class="page-shell">
        <section class="hero reveal">
            <div class="hero-copy">
                <div class="eyebrow"><i class="fa-solid fa-seedling"></i> Rural Power Distribution</div>
                <h1>Reliable Power for Every <span>Field, Pump & Village.</span></h1>
                <p>
                    A modern public service desk for farmers, officers, and administrators to manage electricity connections,
                    subsidy support, billing, usage, and field complaints with clarity.
                </p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('register') }}">Start Your Application <i class="fa-solid fa-arrow-right"></i></a>
                    <a class="underlink" href="{{ route('login') }}">Open citizen portal</a>
                </div>

                <div class="hero-note" aria-label="Platform highlights">
                    <div class="mini-stat glass"><strong>24x7</strong><span>Connection and billing access</span></div>
                    <div class="mini-stat glass"><strong>6</strong><span>Core farmer services unified</span></div>
                    <div class="mini-stat glass"><strong>100%</strong><span>Role-based ministry workflow</span></div>
                </div>
            </div>

            <div class="hero-visual" aria-hidden="true">
                <div class="image-sculpture">
                    <div class="leaf-pin"></div>
                    <div class="power-photo"></div>
                    <div class="main-photo"></div>
                    <div class="floating-card card-one glass">
                        <i class="fa-solid fa-bolt-lightning"></i>
                        <strong>Feeder-aware power service for agricultural demand.</strong>
                    </div>
                    <div class="floating-card card-two glass">
                        <i class="fa-solid fa-hand-holding-droplet"></i>
                        <strong>Subsidies and grievances handled with accountable tracking.</strong>
                    </div>
                    <div class="seal"><span><i class="fa-solid fa-wheat-awn"></i></span></div>
                </div>
            </div>
        </section>

        <section id="process" class="section">
            <div class="section-head">
                <div>
                    <div class="label"><i class="fa-solid fa-route"></i> How It Works</div>
                    <h2>Simple field-to-office flow for <span>agricultural electricity.</span></h2>
                </div>
                <a class="btn btn-plain" href="{{ route('register') }}">View Services <i class="fa-solid fa-table-cells-large"></i></a>
            </div>

            <div class="steps">
                <article class="step-card glass">
                    <i class="fa-solid fa-file-signature"></i>
                    <span class="number">01</span>
                    <h3>Apply for Connection</h3>
                    <p>Submit farm, pump, and location details for a new agricultural electricity connection.</p>
                </article>
                <article class="step-card glass">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span class="number">02</span>
                    <h3>Officer Verification</h3>
                    <p>SDO teams review the request, update status, and keep the applicant informed.</p>
                </article>
                <article class="step-card glass">
                    <i class="fa-solid fa-receipt"></i>
                    <span class="number">03</span>
                    <h3>Billing & Subsidies</h3>
                    <p>Farmers can view bills, track payments, and access eligible support schemes.</p>
                </article>
                <article class="step-card glass">
                    <i class="fa-solid fa-headset"></i>
                    <span class="number">04</span>
                    <h3>Resolve Complaints</h3>
                    <p>Report supply, meter, or billing issues and follow every update through resolution.</p>
                </article>
            </div>
        </section>

        <section id="services" class="section">
            <div class="why-grid">
                <div class="why-copy">
                    <div class="label"><i class="fa-solid fa-shield-heart"></i> Why Choose Us</div>
                    <h2>Built for India. Built for <span>accountable delivery.</span></h2>
                    <p>
                        The platform respects the practical rhythm of agricultural power: seasonal load, subsidy policy,
                        local office verification, and farmer-first service visibility.
                    </p>
                    <a class="underlink" href="{{ route('login') }}">Know more about the portal</a>
                </div>

                <div class="features">
                    <article class="feature glass">
                        <i class="fa-solid fa-plug-circle-bolt"></i>
                        <h3>Connection Desk</h3>
                        <p>Apply, track, and manage all agricultural power connections from one citizen account.</p>
                    </article>
                    <article class="feature glass">
                        <i class="fa-solid fa-scale-balanced"></i>
                        <h3>Transparent Tariffs</h3>
                        <p>Clear billing, payment records, and subsidy visibility for every registered farmer.</p>
                    </article>
                    <article class="feature glass">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <h3>Zone Operations</h3>
                        <p>Administrators organize users, tariffs, schemes, and operational zones with confidence.</p>
                    </article>
                    <article class="feature glass">
                        <i class="fa-solid fa-comments"></i>
                        <h3>Grievance Tracking</h3>
                        <p>Complaints move through a visible workflow so unresolved issues do not disappear.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="impact" class="impact" aria-label="Platform impact">
            <div class="impact-cell">
                <strong><i class="fa-solid fa-map"></i></strong>
                <span>Empowering agricultural communities through reliable public infrastructure.</span>
            </div>
            <div class="impact-cell"><strong>25K+</strong><span>Farmer records ready for service</span></div>
            <div class="impact-cell"><strong>10K+</strong><span>Payments and bill workflows</span></div>
            <div class="impact-cell"><strong>500+</strong><span>Local field operations supported</span></div>
            <div class="impact-cell"><strong>35+</strong><span>Tariff and subsidy categories</span></div>
        </section>
    </main>

    <footer id="contact">
        <div class="page-shell footer-inner">
            <span>© {{ date('Y') }} Ministry of Power & Agriculture. All rights reserved.</span>
            <span>Distribution of Electric Power for Agriculture</span>
        </div>
    </footer>
</body>

</html>
