@extends('layouts.app')

@section('title', 
    $match ? 
        $match['teams']['home']['name'] . ' vs ' . $match['teams']['away']['name'] . ' - Maç Detayları' 
        : 'Maç Detayları'
)

@section('content')
<div class="row">
    @if($error)
        <div class="col-12 mb-4">
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $error }}
            </div>
        </div>
    @endif

    @if($match)
        <!-- Match Header -->
        <div class="col-12 mb-4">
            <div class="card match-detail-header">
                <div class="card-body">
                    <!-- League Info -->
                    <div class="text-center mb-3">
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            @if(isset($match['league']['logo']))
                                <img src="{{ $match['league']['logo'] }}" 
                                     alt="{{ $match['league']['name'] }}" 
                                     style="width: 30px; height: 30px; object-fit: contain;"
                                     class="me-2">
                            @endif
                            <span class="fw-bold">{{ $match['league']['name'] }}</span>
                        </div>
                        <small class="text-muted">
                            {{ $match['league']['country'] }} • 
                            {{ date('d F Y, H:i', strtotime($match['fixture']['date'])) }}
                        </small>
                    </div>

                    <!-- Teams and Score -->
                    <div class="row align-items-center text-center">
                        <!-- Home Team -->
                        <div class="col-4">
                            <div class="team-section">
                                @if(isset($match['teams']['home']['logo']))
                                    <img src="{{ $match['teams']['home']['logo'] }}" 
                                         alt="{{ $match['teams']['home']['name'] }}" 
                                         class="team-logo-large mb-3">
                                @endif
                                <h4 class="team-name">{{ $match['teams']['home']['name'] }}</h4>
                            </div>
                        </div>

                        <!-- Score and Status -->
                        <div class="col-4">
                            <div class="score-section">
                                <div class="score-display">
                                    <span class="score-home">{{ $match['goals']['home'] ?? '-' }}</span>
                                    <span class="score-separator">:</span>
                                    <span class="score-away">{{ $match['goals']['away'] ?? '-' }}</span>
                                </div>
                                
                                @php
                                    $statusMap = [
                                        'NS' => ['text' => 'Başlamamış', 'class' => 'status-scheduled'],
                                        '1H' => ['text' => 'İlk Yarı', 'class' => 'status-live'],
                                        'HT' => ['text' => 'Devre Arası', 'class' => 'status-live'],
                                        '2H' => ['text' => 'İkinci Yarı', 'class' => 'status-live'],
                                        'ET' => ['text' => 'Uzatma', 'class' => 'status-live'],
                                        'FT' => ['text' => 'Bitti', 'class' => 'status-finished'],
                                        'AET' => ['text' => 'Uzatmalarda Bitti', 'class' => 'status-finished'],
                                        'PEN' => ['text' => 'Penaltılarda Bitti', 'class' => 'status-finished']
                                    ];
                                    $status = $statusMap[$match['fixture']['status']['short']] ?? ['text' => $match['fixture']['status']['short'], 'class' => 'status-scheduled'];
                                @endphp

                                <div class="match-status-large">
                                    <span class="status-badge-large {{ $status['class'] }}">
                                        {{ $status['text'] }}
                                        @if(in_array($match['fixture']['status']['short'], ['1H', '2H', 'ET']) && isset($match['fixture']['status']['elapsed']))
                                            - {{ $match['fixture']['status']['elapsed'] }}'
                                        @endif
                                    </span>
                                </div>

                                <!-- Score Details -->
                                @if(isset($match['score']) && ($match['score']['halftime']['home'] !== null || $match['score']['fulltime']['home'] !== null))
                                    <div class="score-details mt-3">
                                        @if($match['score']['halftime']['home'] !== null)
                                            <div class="score-detail-row">
                                                <small class="text-muted">İlk Yarı</small>
                                                <span class="score-small">
                                                    {{ $match['score']['halftime']['home'] }} - {{ $match['score']['halftime']['away'] }}
                                                </span>
                                            </div>
                                        @endif
                                        @if(isset($match['score']['penalty']) && $match['score']['penalty']['home'] !== null)
                                            <div class="score-detail-row">
                                                <small class="text-muted">Penaltı</small>
                                                <span class="score-small">
                                                    {{ $match['score']['penalty']['home'] }} - {{ $match['score']['penalty']['away'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Away Team -->
                        <div class="col-4">
                            <div class="team-section">
                                @if(isset($match['teams']['away']['logo']))
                                    <img src="{{ $match['teams']['away']['logo'] }}" 
                                         alt="{{ $match['teams']['away']['name'] }}" 
                                         class="team-logo-large mb-3">
                                @endif
                                <h4 class="team-name">{{ $match['teams']['away']['name'] }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Match Info -->
                    <div class="row mt-4 text-center">
                        @if(isset($match['fixture']['venue']['name']))
                            <div class="col-md-4">
                                <small class="text-muted d-block">Stadyum</small>
                                <strong>{{ $match['fixture']['venue']['name'] }}</strong>
                                @if(isset($match['fixture']['venue']['city']))
                                    <small class="d-block text-muted">{{ $match['fixture']['venue']['city'] }}</small>
                                @endif
                            </div>
                        @endif
                        @if(isset($match['fixture']['referee']))
                            <div class="col-md-4">
                                <small class="text-muted d-block">Hakem</small>
                                <strong>{{ $match['fixture']['referee'] }}</strong>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <small class="text-muted d-block">Hafta</small>
                            <strong>{{ $match['league']['round'] ?? 'Bilinmiyor' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-fill" id="matchTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview">
                                <i class="fas fa-info-circle me-1"></i> Genel Bakış
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events">
                                <i class="fas fa-list me-1"></i> Maç Olayları
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="statistics-tab" data-bs-toggle="tab" data-bs-target="#statistics">
                                <i class="fas fa-chart-bar me-1"></i> İstatistikler
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="col-12">
            <div class="tab-content" id="matchTabsContent">
                
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row">
                        <!-- Match Events Summary -->
                        @if(count($events) > 0)
                            <div class="col-lg-8 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-clock me-2"></i>
                                            Önemli Olaylar
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="events-timeline">
                                            @foreach(array_slice($events, 0, 10) as $event)
                                                <div class="timeline-item">
                                                    <div class="timeline-marker">
                                                        @if($event['type'] === 'Goal')
                                                            <i class="fas fa-futbol text-success"></i>
                                                        @elseif($event['type'] === 'Card')
                                                            <i class="fas fa-square text-{{ $event['detail'] === 'Yellow Card' ? 'warning' : 'danger' }}"></i>
                                                        @elseif($event['type'] === 'subst')
                                                            <i class="fas fa-exchange-alt text-primary"></i>
                                                        @else
                                                            <i class="fas fa-circle text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $event['time']['elapsed'] ?? '' }}'</strong>
                                                                {{ $event['player']['name'] ?? '' }}
                                                                @if($event['type'] === 'Goal')
                                                                    ⚽ GOL
                                                                @elseif($event['type'] === 'Card')
                                                                    {{ $event['detail'] === 'Yellow Card' ? '🟨' : '🟥' }} 
                                                                    {{ $event['detail'] }}
                                                                @elseif($event['type'] === 'subst')
                                                                    🔄 Oyuncu Değişikliği
                                                                @endif
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $event['team']['name'] }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Quick Stats -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-tachometer-alt me-2"></i>
                                        Hızlı İstatistik
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(count($statistics) > 0)
                                        @php 
                                            $homeStats = collect($statistics)->where('team.name', $match['teams']['home']['name'])->first();
                                            $awayStats = collect($statistics)->where('team.name', $match['teams']['away']['name'])->first();
                                        @endphp
                                        
                                        @if($homeStats && $awayStats)
                                            @php
                                                $homeShots = collect($homeStats['statistics'])->where('type', 'Shots on Goal')->first()['value'] ?? 0;
                                                $awayShots = collect($awayStats['statistics'])->where('type', 'Shots on Goal')->first()['value'] ?? 0;
                                                $homePossession = collect($homeStats['statistics'])->where('type', 'Ball Possession')->first()['value'] ?? '0%';
                                                $awayPossession = collect($awayStats['statistics'])->where('type', 'Ball Possession')->first()['value'] ?? '0%';
                                            @endphp
                                            
                                            <div class="stat-row mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $homeShots }}</span>
                                                    <small class="text-muted">Kaleye Şut</small>
                                                    <span>{{ $awayShots }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="stat-row mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $homePossession }}</span>
                                                    <small class="text-muted">Top Hakimiyeti</small>
                                                    <span>{{ $awayPossession }}</span>
                                                </div>
                                                <div class="progress mt-1" style="height: 8px;">
                                                    @php $homePercent = (int)str_replace('%', '', $homePossession); @endphp
                                                    <div class="progress-bar bg-primary" style="width: {{ $homePercent }}%"></div>
                                                    <div class="progress-bar bg-danger" style="width: {{ 100 - $homePercent }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    <div class="d-grid">
                                        <button class="btn btn-outline-primary btn-sm" onclick="$('#statistics-tab').click()">
                                            <i class="fas fa-chart-bar me-1"></i> Detaylı İstatistikler
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events Tab -->
                <div class="tab-pane fade" id="events" role="tabpanel">
                    @if(count($events) > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Tüm Maç Olayları</h5>
                            </div>
                            <div class="card-body">
                                <div class="events-full-list">
                                    @foreach($events as $event)
                                        <div class="event-item {{ $event['team']['name'] === $match['teams']['home']['name'] ? 'home-event' : 'away-event' }}">
                                            <div class="event-time">
                                                <strong>{{ $event['time']['elapsed'] ?? '' }}'</strong>
                                                @if(isset($event['time']['extra']))
                                                    +{{ $event['time']['extra'] }}
                                                @endif
                                            </div>
                                            <div class="event-icon">
                                                @if($event['type'] === 'Goal')
                                                    <i class="fas fa-futbol text-success"></i>
                                                @elseif($event['type'] === 'Card')
                                                    <i class="fas fa-square text-{{ $event['detail'] === 'Yellow Card' ? 'warning' : 'danger' }}"></i>
                                                @elseif($event['type'] === 'subst')
                                                    <i class="fas fa-exchange-alt text-primary"></i>
                                                @else
                                                    <i class="fas fa-circle text-secondary"></i>
                                                @endif
                                            </div>
                                            <div class="event-details">
                                                <div class="event-player">{{ $event['player']['name'] ?? 'Bilinmiyor' }}</div>
                                                <div class="event-type">{{ $event['detail'] ?? $event['type'] }}</div>
                                                @if(isset($event['assist']['name']))
                                                    <small class="text-muted">Asist: {{ $event['assist']['name'] }}</small>
                                                @endif
                                            </div>
                                            <div class="event-team">
                                                <img src="{{ $event['team']['logo'] }}" 
                                                     alt="{{ $event['team']['name'] }}" 
                                                     style="width: 20px; height: 20px; object-fit: contain;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Henüz maç olayı bulunmuyor</h5>
                                <p class="text-muted">Maç başladığında burada olayları görebilirsiniz.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Statistics Tab -->
                <div class="tab-pane fade" id="statistics" role="tabpanel">
                    @if(count($statistics) > 0 && count($statistics) >= 2)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Detaylı İstatistikler</h5>
                            </div>
                            <div class="card-body">
                                @php 
                                    $homeStats = collect($statistics)->where('team.name', $match['teams']['home']['name'])->first();
                                    $awayStats = collect($statistics)->where('team.name', $match['teams']['away']['name'])->first();
                                @endphp
                                
                                @if($homeStats && $awayStats)
                                    <div class="stats-comparison">
                                        <div class="row mb-3">
                                            <div class="col-4 text-center">
                                                <img src="{{ $homeStats['team']['logo'] }}" style="width: 40px; height: 40px;">
                                                <h6 class="mt-2">{{ $homeStats['team']['name'] }}</h6>
                                            </div>
                                            <div class="col-4 text-center">
                                                <h6>İstatistikler</h6>
                                            </div>
                                            <div class="col-4 text-center">
                                                <img src="{{ $awayStats['team']['logo'] }}" style="width: 40px; height: 40px;">
                                                <h6 class="mt-2">{{ $awayStats['team']['name'] }}</h6>
                                            </div>
                                        </div>

                                        @php
                                            $statTypes = [
                                                'Shots on Goal' => 'Kaleye Şut',
                                                'Total Shots' => 'Toplam Şut',
                                                'Ball Possession' => 'Top Hakimiyeti',
                                                'Corner Kicks' => 'Korner',
                                                'Offsides' => 'Ofsayt',
                                                'Yellow Cards' => 'Sarı Kart',
                                                'Red Cards' => 'Kırmızı Kart',
                                                'Goalkeeper Saves' => 'Kaleci Kurtarışı',
                                                'Total passes' => 'Toplam Pas',
                                                'Passes accurate' => 'İsabetli Pas',
                                                'Passes %' => 'Pas İsabet %'
                                            ];
                                        @endphp

                                        @foreach($statTypes as $statKey => $statName)
                                            @php
                                                $homeStat = collect($homeStats['statistics'])->where('type', $statKey)->first()['value'] ?? '-';
                                                $awayStat = collect($awayStats['statistics'])->where('type', $statKey)->first()['value'] ?? '-';
                                            @endphp
                                            
                                            @if($homeStat !== '-' || $awayStat !== '-')
                                                <div class="stat-comparison-row mb-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-3 text-end">
                                                            <strong>{{ $homeStat }}</strong>
                                                        </div>
                                                        <div class="col-6 text-center">
                                                            <small class="text-muted">{{ $statName }}</small>
                                                            @if(in_array($statKey, ['Ball Possession', 'Passes %']))
                                                                @php 
                                                                    $homePercent = is_numeric(str_replace('%', '', $homeStat)) ? (int)str_replace('%', '', $homeStat) : 50;
                                                                    $awayPercent = 100 - $homePercent;
                                                                @endphp
                                                                <div class="progress mt-1" style="height: 6px;">
                                                                    <div class="progress-bar bg-primary" style="width: {{ $homePercent }}%"></div>
                                                                    <div class="progress-bar bg-danger" style="width: {{ $awayPercent }}%"></div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-3 text-start">
                                                            <strong>{{ $awayStat }}</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">İstatistik bilgisi bulunmuyor</h5>
                                <p class="text-muted">Maç istatistikleri henüz mevcut değil.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-futbol fa-4x text-danger mb-4"></i>
                    <h3 class="text-danger mb-3">Maç bulunamadı</h3>
                    <p class="text-muted mb-4">
                        Aradığınız maç bulunamadı veya erişim sırasında bir hata oluştu.
                    </p>
                    <div class="d-grid gap-2 d-md-block">
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Tekrar Dene
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>
                            Ana Sayfa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .match-detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .team-logo-large {
        width: 80px;
        height: 80px;
        object-fit: contain;
    }

    .score-display {
        font-size: 3rem;
        font-weight: bold;
        margin: 1rem 0;
    }

    .score-separator {
        margin: 0 1rem;
        opacity: 0.7;
    }

    .status-badge-large {
        font-size: 1.1rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
    }

    .score-details {
        border-top: 1px solid rgba(255,255,255,0.2);
        padding-top: 1rem;
    }

    .score-detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .events-timeline .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .timeline-marker {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .event-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        border-left: 4px solid transparent;
    }

    .home-event {
        background: rgba(0,123,255,0.05);
        border-left-color: #007bff;
    }

    .away-event {
        background: rgba(220,53,69,0.05);
        border-left-color: #dc3545;
    }

    .event-time {
        min-width: 60px;
        font-weight: bold;
    }

    .event-icon {
        min-width: 40px;
        text-align: center;
        font-size: 1.2rem;
    }

    .event-details {
        flex: 1;
        margin: 0 1rem;
    }

    .event-player {
        font-weight: 600;
    }

    .event-type {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .stat-comparison-row {
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
    }

    @media (max-width: 768px) {
        .score-display {
            font-size: 2rem;
        }
        
        .team-logo-large {
            width: 60px;
            height: 60px;
        }
        
        .event-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .event-time {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Auto-refresh for live matches
    @if(in_array($match['fixture']['status']['short'] ?? '', ['1H', '2H', 'HT', 'ET']))
        setInterval(function() {
            // Refresh events and statistics
            refreshMatchData();
        }, 30000); // 30 seconds
    @endif

    function refreshMatchData() {
        // This would typically make AJAX calls to update live data
        console.log('Refreshing match data...');
    }

    // Hash navigation for direct links to tabs
    $(document).ready(function() {
        if (window.location.hash) {
            const hash = window.location.hash;
            if (hash === '#statistics' || hash === '#events' || hash === '#overview') {
                $(hash + '-tab').click();
            }
        }
    });

    // Update URL when tab changes
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).data('bs-target');
        window.history.pushState(null, null, target);
    });
</script>
@endsection 