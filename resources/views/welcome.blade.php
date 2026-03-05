<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EzMenu System - NgopiKode Enterprise</title>

    <!-- Fonts: Plus Jakarta Sans (Lebih Techy & Modern dari Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #6366f1; /* Indigo 500 */
            --primary-dark: #4f46e5; /* Indigo 600 */
            --surface: #ffffff;
            --surface-glass: rgba(255, 255, 255, 0.7);
            --text-main: #0f172a; /* Slate 900 */
            --text-sub: #64748b; /* Slate 500 */
            --border: #e2e8f0;
            --highlight: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background-color: #F8FAFC;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* --- Abstract Animated Background --- */
        .bg-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            background: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.08) 0px, transparent 50%),
            radial-gradient(at 50% 50%, rgba(255, 255, 255, 0.8) 0px, transparent 50%);
            background-color: #F8FAFC;
        }

        /* --- Navbar (Floating Glass) --- */
        .navbar-floating {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05),
            0 2px 4px -1px rgba(0, 0, 0, 0.02),
            inset 0 0 0 1px rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            padding: 0.75rem 1.5rem;
            z-index: 1000;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-sub);
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* --- Hero Section --- */
        .hero-section {
            padding-top: 10rem;
            padding-bottom: 5rem;
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .hero-label {
            font-family: 'Courier New', monospace;
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 600;
            background: rgba(99, 102, 241, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            background: linear-gradient(180deg, var(--text-main) 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: var(--text-sub);
            line-height: 1.7;
            margin-bottom: 2.5rem;
            max-width: 480px;
        }

        .btn-access {
            background: var(--text-main);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.3);
            border: 1px solid transparent;
        }

        .btn-access:hover {
            background: #1e293b;
            transform: translateY(-2px);
            color: white;
        }

        /* --- Glass Dashboard Mockup --- */
        .glass-mockup {
            position: relative;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.5);
            padding: 20px;
            transform: perspective(2000px) rotateY(-10deg) rotateX(5deg);
            transition: transform 0.5s ease;
        }

        .glass-mockup:hover {
            transform: perspective(2000px) rotateY(-5deg) rotateX(2deg);
        }

        /* Internal Dashboard UI */
        .dash-layout {
            display: flex;
            gap: 20px;
            height: 350px;
        }

        .dash-nav {
            width: 60px;
            background: white;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            gap: 15px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .nav-item {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f1f5f9;
            transition: 0.2s;
        }

        .nav-item.active {
            background: var(--primary);
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
        }

        .dash-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .dash-header {
            height: 50px;
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            px;
            padding: 0 20px;
            justify-content: space-between;
        }

        .dash-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            flex: 1;
        }

        .dash-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        /* Floating Elements inside Mockup */
        .float-stat {
            position: absolute;
            right: -20px;
            bottom: 40px;
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
            z-index: 10;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* --- Bento Grid Modules --- */
        .bento-section {
            padding: 5rem 0;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-sub);
            letter-spacing: 1px;
            margin-bottom: 2rem;
        }

        .bento-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            height: 100%;
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .bento-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .bento-icon {
            width: 48px;
            height: 48px;
            background: #F8FAFC;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
        }

        .bento-card h5 {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .bento-card p {
            font-size: 0.9rem;
            color: var(--text-sub);
            line-height: 1.6;
            margin: 0;
        }

        /* --- Footer --- */
        .footer-minimal {
            border-top: 1px solid var(--border);
            padding: 2rem 0;
            background: white;
            font-size: 0.85rem;
            color: var(--text-sub);
        }

        /* Responsive Fixes */
        @media (max-width: 991px) {
            .navbar-floating {
                width: 95%;
                top: 10px;
                padding: 0.75rem 1rem;
            }

            .hero-section {
                flex-direction: column;
                text-align: center;
                padding-top: 8rem;
            }

            .hero-desc {
                margin: 0 auto 2.5rem;
            }

            .glass-mockup {
                transform: none;
                margin-top: 3rem;
                width: 100%;
            }

            .glass-mockup:hover {
                transform: none;
            }

            .float-stat {
                display: none;
            }

            .hero-title {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Animated Mesh Background -->
<div class="bg-mesh"></div>

<!-- Floating Navbar -->
<nav class="navbar navbar-expand-lg navbar-floating">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <div class="bg-dark text-white rounded-3 d-flex align-items-center justify-content-center"
                 style="width: 32px; height: 32px;">
                <i class="bi bi-layers-fill" style="font-size: 0.9rem;"></i>
            </div>
            <span class="fw-bold fs-6 text-dark tracking-tight">EzMenu</span>
        </a>

        <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navContent">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
                <li class="nav-item"><a class="nav-link" href="#">Dokumentasi</a></li>
                <li class="nav-item"><a class="nav-link" href="#">API Reference</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Security</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <div class="status-badge d-none d-lg-flex">
                    <div class="status-dot"></div>
                    Systems Operational
                </div>
                <a href="/login" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">Login Portal</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">

            <!-- Text Content -->
            <div class="col-lg-5" data-aos="fade-up" data-aos-duration="800">
                <span class="hero-label">NGOPIKODE_INTERNAL_V2.4</span>
                <h1 class="hero-title">
                    Enterprise <br>Restaurant OS
                </h1>
                <p class="hero-desc">
                    Platform manajemen terpusat untuk jaringan outlet NgopiKode. Mengelola POS, Inventory, dan Digital
                    Ordering dalam satu environment yang aman.
                </p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                    <a href="/login" class="btn btn-access d-flex align-items-center gap-2">
                        Masuk ke System <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="mt-4 pt-2 border-top border-light-subtle d-flex gap-4 text-muted small">
                    <span><i class="bi bi-shield-lock-fill me-1"></i> SSO Enabled</span>
                    <span><i class="bi bi-cloud-check-fill me-1"></i> 99.9% Uptime</span>
                </div>
            </div>

            <!-- 3D Glass Mockup -->
            <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div class="glass-mockup">
                    <!-- UI Dashboard Abstraksi -->
                    <div class="dash-layout">
                        <!-- Sidebar -->
                        <div class="dash-nav">
                            <div class="nav-item active"></div>
                            <div class="nav-item"></div>
                            <div class="nav-item"></div>
                            <div class="nav-item"></div>
                            <div style="flex:1"></div>
                            <div class="nav-item" style="border-radius: 50%"></div>
                        </div>
                        <!-- Main -->
                        <div class="dash-main">
                            <div class="dash-header">
                                <div style="width: 100px; height: 10px; background: #e2e8f0; border-radius: 4px;"></div>
                                <div style="width: 30px; height: 30px; background: #f1f5f9; border-radius: 50%;"></div>
                            </div>
                            <div class="dash-grid">
                                <div class="dash-card p-3 d-flex flex-column gap-2">
                                    <div
                                        style="width: 40%; height: 8px; background: #e2e8f0; border-radius: 4px;"></div>
                                    <div
                                        style="height: 100px; background: linear-gradient(180deg, rgba(99,102,241,0.05), transparent); border-radius: 8px; border: 1px dashed #e2e8f0;"></div>
                                    <div class="d-flex gap-2">
                                        <div
                                            style="flex:1; height: 8px; background: #f1f5f9; border-radius: 4px;"></div>
                                        <div
                                            style="flex:1; height: 8px; background: #f1f5f9; border-radius: 4px;"></div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <div class="dash-card p-3" style="flex:1">
                                        <div
                                            style="width: 60%; height: 8px; background: #e2e8f0; border-radius: 4px; margin-bottom: 10px;"></div>
                                        <div
                                            style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 4px;"></div>
                                    </div>
                                    <div class="dash-card p-3" style="flex:1">
                                        <div
                                            style="width: 50%; height: 8px; background: #e2e8f0; border-radius: 4px; margin-bottom: 10px;"></div>
                                        <div
                                            style="width: 80%; height: 6px; background: #f1f5f9; border-radius: 4px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Badge -->
                    <div class="float-stat d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 text-success rounded p-2">
                            <i class="bi bi-graph-up-arrow fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-muted fw-bold">Live Revenue</div>
                            <div class="fw-bold text-dark">+24.5%</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Bento Grid Modules Section -->
<section class="bento-section">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center text-lg-start">
                <div class="section-title">Core Modules</div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Module 1: POS -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="bento-card">
                    <div class="bento-icon">
                        <i class="bi bi-calculator"></i>
                    </div>
                    <h5>Cloud POS System</h5>
                    <p>Kasir terintegrasi dengan sinkronisasi data real-time ke server pusat.</p>
                </div>
            </div>

            <!-- Module 2: Kitchen -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="bento-card">
                    <div class="bento-icon text-warning" style="color: #f59e0b;">
                        <i class="bi bi-fire"></i>
                    </div>
                    <h5>Kitchen Display (KDS)</h5>
                    <p>Manajemen pesanan dapur tanpa kertas, mengurangi human error.</p>
                </div>
            </div>

            <!-- Module 3: Inventory -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="bento-card">
                    <div class="bento-icon text-info" style="color: #06b6d4;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h5>Smart Inventory</h5>
                    <p>Pelacakan stok bahan baku otomatis berdasarkan resep menu.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Minimal -->
<footer class="footer-minimal">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0 d-flex align-items-center gap-2">
                <i class="bi bi-layers-fill text-dark"></i>
                <span class="fw-bold text-dark">EzMenu System</span>
            </div>

            <div class="d-flex gap-4">
                <a href="#" class="text-decoration-none text-muted">System Status</a>
                <a href="#" class="text-decoration-none text-muted">Internal Docs</a>
                <span class="text-muted opacity-50">&copy; 2024 NgopiKode</span>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
        offset: 40
    });
</script>
</body>
</html>
