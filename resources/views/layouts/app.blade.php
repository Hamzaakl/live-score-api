<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Canlı Skor') - LiveScore</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --dark-color: #1a252f;
            --light-color: #ecf0f1;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color)) !important;
            box-shadow: var(--shadow);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
        }

        .navbar-nav .nav-link.active {
            color: white !important;
            background-color: var(--secondary-color);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        /* Match Cards */
        .match-card {
            margin-bottom: 1rem;
            border-radius: 1rem;
            overflow: hidden;
        }

        .match-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .match-body {
            padding: 1.5rem;
            background: white;
        }

        .team-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .team-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 1rem;
            border-radius: 0.5rem;
        }

        .team-name {
            font-weight: 600;
            color: var(--primary-color);
            flex: 1;
        }

        .score {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            min-width: 30px;
            text-align: center;
        }

        .match-status {
            text-align: center;
            margin: 1rem 0;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-live {
            background-color: var(--danger-color);
            color: white;
            animation: pulse 2s infinite;
        }

        .status-finished {
            background-color: var(--success-color);
            color: white;
        }

        .status-scheduled {
            background-color: var(--warning-color);
            color: white;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* League Cards */
        .league-card {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .league-card:hover {
            color: inherit;
        }

        .league-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 1rem;
            border-radius: 0.5rem;
        }

        .league-info h5 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .league-country {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            border: none;
            border-radius: 0.5rem;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            margin-bottom: 1rem;
        }

        /* Error Alert */
        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .match-body {
                padding: 1rem;
            }
            
            .team-info {
                margin-bottom: 0.75rem;
            }
            
            .team-logo {
                width: 35px;
                height: 35px;
            }
            
            .score {
                font-size: 1.3rem;
            }
        }

        /* Standings Table */
        .standings-table {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .standings-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 500;
            padding: 1rem 0.75rem;
        }

        .standings-table td {
            border: none;
            padding: 0.75rem;
            vertical-align: middle;
        }

        .standings-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .standings-table tr:hover {
            background-color: #e9ecef;
        }

        .position {
            font-weight: bold;
            color: var(--primary-color);
        }

        .team-cell {
            display: flex;
            align-items: center;
        }

        .team-cell img {
            width: 25px;
            height: 25px;
            margin-right: 0.5rem;
            object-fit: contain;
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-futbol me-2"></i>
                LiveScore
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i> Ana Sayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('live-scores') ? 'active' : '' }}" href="{{ route('live-scores') }}">
                            <i class="fas fa-broadcast-tower me-1"></i> Canlı Skorlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('leagues.*') ? 'active' : '' }}" href="{{ route('leagues.popular') }}">
                            <i class="fas fa-trophy me-1"></i> Ligeler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('matches.by-date') }}">
                            <i class="fas fa-calendar me-1"></i> Maç Takvimi
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-futbol me-2"></i>LiveScore</h5>
                    <p class="mb-0">Canlı futbol skorları, istatistikler ve daha fazlası...</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">© {{ date('Y') }} LiveScore. Tüm hakları saklıdır.</p>
                    <small class="text-muted">API-Football tarafından desteklenmektedir</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh for live scores
        if (window.location.pathname.includes('live-scores') || window.location.pathname === '/') {
            setInterval(function() {
                if (typeof refreshLiveScores === 'function') {
                    refreshLiveScores();
                }
            }, 30000); // 30 saniye
        }

        // Loading spinner helper
        function showLoading(element) {
            element.innerHTML = `
                <div class="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                    <div>Yükleniyor...</div>
                </div>
            `;
        }

        // Error message helper
        function showError(element, message) {
            element.innerHTML = `
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            `;
        }
    </script>

    @yield('scripts')
</body>
</html> 