@extends('layouts.app')

@section('title', 'Canlı Skorlar')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-broadcast-tower me-2"></i>
                        Canlı Skorlar
                        @if(count($matches) > 0)
                            <span class="badge bg-danger ms-2" id="live-count">{{ count($matches) }} CANLI</span>
                        @endif
                    </h4>
                    <small class="text-muted">Her 30 saniyede otomatik güncellenir</small>
                </div>
                <div>
                    <button class="btn btn-outline-light btn-sm" onclick="refreshLiveScores()" id="refresh-btn">
                        <i class="fas fa-sync-alt me-1"></i> Yenile
                    </button>
                    <span class="badge bg-success ms-2" id="last-update">Son güncelleme: Şimdi</span>
                </div>
            </div>
        </div>
    </div>

    @if($error)
        <div class="col-12 mb-4">
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $error }}
            </div>
        </div>
    @endif

    <!-- Live Matches -->
    <div class="col-12">
        <div id="live-matches-container">
            @if(count($matches) > 0)
                <div class="row">
                    @foreach($matches as $match)
                        <div class="col-lg-6 mb-4">
                            @include('components.match-card', ['match' => $match])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-tv fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Şu anda canlı maç bulunmuyor</h3>
                        <p class="text-muted mb-4">
                            Maçlar başladığında buradan canlı skorları takip edebilirsiniz.<br>
                            Sayfa otomatik olarak güncellenir.
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('matches.by-date') }}" class="btn btn-primary">
                                        <i class="fas fa-calendar me-2"></i>
                                        Bugünün Maçlarını Gör
                                    </a>
                                    <a href="{{ route('leagues.popular') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-trophy me-2"></i>
                                        Popüler Ligleri İncele
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Loading overlay -->
        <div id="loading-overlay" class="d-none">
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                    <p class="text-muted mb-0">Canlı skorlar güncelleniyor...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Match Details Modal -->
<div class="modal fade" id="matchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Maç Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let refreshInterval;
    let lastUpdateTime = new Date();

    // Initialize auto-refresh
    document.addEventListener('DOMContentLoaded', function() {
        startAutoRefresh();
        updateLastUpdateTime();
    });

    function startAutoRefresh() {
        // Clear existing interval
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }

        // Start new interval (30 seconds)
        refreshInterval = setInterval(function() {
            refreshLiveScores();
        }, 30000);

        console.log('Auto-refresh started (30 seconds interval)');
    }

    function refreshLiveScores() {
        const refreshBtn = document.getElementById('refresh-btn');
        const container = document.getElementById('live-matches-container');
        const loadingOverlay = document.getElementById('loading-overlay');

        // Show loading state
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Güncelleniyor...';
        refreshBtn.disabled = true;

        // Show loading overlay if no matches currently
        if (container.children.length === 0 || container.innerHTML.includes('Şu anda canlı maç bulunmuyor')) {
            loadingOverlay.classList.remove('d-none');
            container.style.opacity = '0.5';
        }

        fetch('{{ route("api.live-scores") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateLiveMatches(data.data);
                    updateLastUpdateTime();
                } else {
                    showError('Güncelleme sırasında bir hata oluştu: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Live scores refresh error:', error);
                showError('Bağlantı hatası. Lütfen internet bağlantınızı kontrol edin.');
            })
            .finally(() => {
                // Reset button state
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Yenile';
                refreshBtn.disabled = false;
                
                // Hide loading overlay
                loadingOverlay.classList.add('d-none');
                container.style.opacity = '1';
            });
    }

    function updateLiveMatches(matches) {
        const container = document.getElementById('live-matches-container');
        const liveCount = document.getElementById('live-count');

        if (matches.length === 0) {
            container.innerHTML = `
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-tv fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Şu anda canlı maç bulunmuyor</h3>
                        <p class="text-muted mb-4">
                            Maçlar başladığında buradan canlı skorları takip edebilirsiniz.<br>
                            Sayfa otomatik olarak güncellenir.
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('matches.by-date') }}" class="btn btn-primary">
                                        <i class="fas fa-calendar me-2"></i>
                                        Bugünün Maçlarını Gör
                                    </a>
                                    <a href="{{ route('leagues.popular') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-trophy me-2"></i>
                                        Popüler Ligleri İncele
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            if (liveCount) liveCount.style.display = 'none';
        } else {
            let html = '<div class="row">';
            matches.forEach(match => {
                html += '<div class="col-lg-6 mb-4">' + generateMatchCardHtml(match) + '</div>';
            });
            html += '</div>';
            
            container.innerHTML = html;
            
            // Update live count
            if (liveCount) {
                liveCount.textContent = `${matches.length} CANLI`;
                liveCount.style.display = 'inline';
            }
        }
    }

    function generateMatchCardHtml(match) {
        const status = getMatchStatus(match.fixture.status.short);
        const homeScore = match.goals.home ?? '-';
        const awayScore = match.goals.away ?? '-';
        let matchTime = '';
        
        if (match.fixture.status.short === 'NS') {
            matchTime = new Date(match.fixture.date).toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
        } else if (['1H', '2H', 'ET'].includes(match.fixture.status.short)) {
            matchTime = match.fixture.status.elapsed ? match.fixture.status.elapsed + "'" : '';
        }
        
        return `
            <div class="card match-card">
                <div class="match-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-futbol me-2"></i>
                            ${match.league.name}
                        </div>
                        <small>${match.league.country}</small>
                    </div>
                </div>
                <div class="match-body">
                    <div class="team-info">
                        <img src="${match.teams.home.logo}" alt="${match.teams.home.name}" class="team-logo" onerror="this.style.display='none';">
                        <div class="team-name">${match.teams.home.name}</div>
                        <div class="score">${homeScore}</div>
                    </div>
                    
                    <div class="team-info">
                        <img src="${match.teams.away.logo}" alt="${match.teams.away.name}" class="team-logo" onerror="this.style.display='none';">
                        <div class="team-name">${match.teams.away.name}</div>
                        <div class="score">${awayScore}</div>
                    </div>
                    
                    <div class="match-status">
                        <span class="status-badge ${status.class}">
                            ${status.text}
                            ${matchTime ? '- ' + matchTime : ''}
                        </span>
                        <div class="text-muted mt-1" style="font-size: 0.8rem;">
                            ${new Date(match.fixture.date).toLocaleDateString('tr-TR')} ${new Date(match.fixture.date).toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'})}
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="/matches/${match.fixture.id}" class="btn btn-outline-primary">
                                <i class="fas fa-info-circle me-1"></i> Detaylar
                            </a>
                            ${(match.fixture.status.short === 'FT' || ['1H', '2H', 'HT', 'ET'].includes(match.fixture.status.short)) ? 
                                `<button type="button" class="btn btn-outline-success" onclick="showStatistics(${match.fixture.id})">
                                    <i class="fas fa-chart-bar me-1"></i> İstatistik
                                </button>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getMatchStatus(statusCode) {
        const statusMap = {
            'NS': { text: 'Başlamamış', class: 'status-scheduled' },
            '1H': { text: 'İlk Yarı', class: 'status-live' },
            'HT': { text: 'Devre Arası', class: 'status-live' },
            '2H': { text: 'İkinci Yarı', class: 'status-live' },
            'ET': { text: 'Uzatma', class: 'status-live' },
            'FT': { text: 'Bitti', class: 'status-finished' },
            'AET': { text: 'Uzatmalarda Bitti', class: 'status-finished' },
            'PEN': { text: 'Penaltılarda Bitti', class: 'status-finished' },
            'LIVE': { text: 'CANLI', class: 'status-live' }
        };
        
        return statusMap[statusCode] || { text: statusCode, class: 'status-scheduled' };
    }

    function updateLastUpdateTime() {
        lastUpdateTime = new Date();
        const updateElement = document.getElementById('last-update');
        if (updateElement) {
            updateElement.textContent = 'Son güncelleme: ' + lastUpdateTime.toLocaleTimeString('tr-TR');
        }
    }

    function showError(message) {
        const container = document.getElementById('live-matches-container');
        const errorHtml = `
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Hata</h5>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary" onclick="refreshLiveScores()">
                        <i class="fas fa-sync-alt me-1"></i> Tekrar Dene
                    </button>
                </div>
            </div>
        `;
        
        container.innerHTML = errorHtml;
    }

    function showStatistics(fixtureId) {
        window.location.href = `/matches/${fixtureId}#statistics`;
    }

    // Cleanup interval on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
@endsection 