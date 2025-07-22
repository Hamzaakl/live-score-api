@extends('layouts.app')

@section('title', 'Ana Sayfa')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-home me-2"></i>
                    Hoş Geldiniz - Canlı Futbol Skorları
                </h4>
            </div>
            <div class="card-body">
                <p class="mb-0">En güncel canlı skorları, maç sonuçlarını ve lig bilgilerini takip edin.</p>
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

    <!-- Live Matches Section -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-broadcast-tower me-2"></i>
                    Canlı Maçlar
                    @if(count($liveMatches) > 0)
                        <span class="badge bg-danger ms-2">{{ count($liveMatches) }} CANLI</span>
                    @endif
                </h5>
                <a href="{{ route('live-scores') }}" class="btn btn-outline-light btn-sm">
                    Tümünü Gör <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body" id="live-matches-container">
                @if(count($liveMatches) > 0)
                    @foreach(array_slice($liveMatches, 0, 5) as $match)
                        @include('components.match-card', ['match' => $match])
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-tv fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Şu anda canlı maç bulunmuyor</h5>
                        <p class="text-muted">Maç saatlerinde buradan canlı skorları takip edebilirsiniz.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Today's Matches -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day me-2"></i>
                    Bugünün Maçları
                </h5>
            </div>
            <div class="card-body">
                @if(count($todayMatches) > 0)
                    @foreach(array_slice($todayMatches, 0, 8) as $match)
                        @include('components.match-card', ['match' => $match])
                    @endforeach
                    
                    @if(count($todayMatches) > 8)
                        <div class="text-center mt-3">
                            <a href="{{ route('matches.by-date') }}" class="btn btn-primary">
                                Tüm Maçları Gör ({{ count($todayMatches) }} maç)
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Bugün maç bulunmuyor</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Popular Leagues Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    Popüler Ligeler
                </h5>
            </div>
            <div class="card-body">
                @if(count($popularLeagues) > 0)
                    @foreach($popularLeagues as $league)
                        <a href="{{ route('leagues.show', $league['league']['id']) }}" class="league-card">
                            <div class="d-flex align-items-center p-3 mb-2 rounded" style="background: #f8f9fa; transition: all 0.3s ease;">
                                @if(isset($league['league']['logo']))
                                    <img src="{{ $league['league']['logo'] }}" alt="{{ $league['league']['name'] }}" class="league-logo me-3">
                                @else
                                    <div class="league-logo me-3 d-flex align-items-center justify-content-center bg-light rounded">
                                        <i class="fas fa-trophy text-muted"></i>
                                    </div>
                                @endif
                                <div class="league-info flex-grow-1">
                                    <h6 class="mb-1 text-dark fw-bold">{{ $league['league']['name'] }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-flag me-1"></i>
                                        {{ $league['country']['name'] ?? 'Uluslararası' }}
                                    </small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('leagues.popular') }}" class="btn btn-outline-primary">
                            Tüm Ligler <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-trophy fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Ligeler yükleniyor...</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Hızlı İstatistikler
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="p-3">
                            <h3 class="text-primary mb-1">{{ count($liveMatches) }}</h3>
                            <small class="text-muted">Canlı Maç</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3">
                            <h3 class="text-success mb-1">{{ count($todayMatches) }}</h3>
                            <small class="text-muted">Bugünkü Maç</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 border-top">
                            <h3 class="text-warning mb-1">{{ count($popularLeagues) }}</h3>
                            <small class="text-muted">Popüler Lig</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 border-top">
                            <h3 class="text-info mb-1">30s</h3>
                            <small class="text-muted">Güncelleme</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live scores refresh function
    function refreshLiveScores() {
        fetch('{{ route("api.live-scores") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    updateLiveMatches(data.data);
                }
            })
            .catch(error => {
                console.error('Live scores refresh error:', error);
            });
    }

    function updateLiveMatches(matches) {
        const container = document.getElementById('live-matches-container');
        if (!container) return;

        // Update live matches display
        let html = '';
        matches.slice(0, 5).forEach(match => {
            html += generateMatchCardHtml(match);
        });

        if (html === '') {
            html = `
                <div class="text-center py-4">
                    <i class="fas fa-tv fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Şu anda canlı maç bulunmuyor</h5>
                    <p class="text-muted">Maç saatlerinde buradan canlı skorları takip edebilirsiniz.</p>
                </div>
            `;
        }

        container.innerHTML = html;

        // Update badge count
        const badge = document.querySelector('.badge.bg-danger');
        if (badge) {
            badge.textContent = `${matches.length} CANLI`;
        }
    }

    function generateMatchCardHtml(match) {
        const status = getMatchStatus(match.fixture.status.short);
        const homeScore = match.goals.home ?? '-';
        const awayScore = match.goals.away ?? '-';
        
        return `
            <div class="card match-card">
                <div class="match-header">
                    <i class="fas fa-futbol me-2"></i>
                    ${match.league.name} - ${match.league.country}
                </div>
                <div class="match-body">
                    <div class="team-info">
                        <img src="${match.teams.home.logo}" alt="${match.teams.home.name}" class="team-logo">
                        <div class="team-name">${match.teams.home.name}</div>
                        <div class="score">${homeScore}</div>
                    </div>
                    
                    <div class="team-info">
                        <img src="${match.teams.away.logo}" alt="${match.teams.away.name}" class="team-logo">
                        <div class="team-name">${match.teams.away.name}</div>
                        <div class="score">${awayScore}</div>
                    </div>
                    
                    <div class="match-status">
                        <span class="status-badge ${status.class}">${status.text}</span>
                    </div>
                    
                    <div class="text-center">
                        <a href="/matches/${match.fixture.id}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-info-circle me-1"></i> Detaylar
                        </a>
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

    // Initialize refresh if there are live matches
    @if(count($liveMatches) > 0)
        console.log('Live matches detected, auto-refresh enabled');
    @endif
</script>
@endsection 