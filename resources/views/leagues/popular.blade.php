@extends('layouts.app')

@section('title', 'Popüler Ligeler')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    Popüler Ligeler
                </h4>
            </div>
            <div class="card-body">
                <p class="mb-0">En popüler futbol liglerini keşfedin ve puan durumlarını takip edin</p>
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

    <!-- Quick Links -->
    <div class="col-12 mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="{{ route('leagues.popular') }}" class="btn btn-primary">
                <i class="fas fa-star me-1"></i> Popüler Ligeler
            </a>
            <a href="{{ route('leagues.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-1"></i> Tüm Ligeler
            </a>
            <a href="{{ route('live-scores') }}" class="btn btn-outline-success">
                <i class="fas fa-broadcast-tower me-1"></i> Canlı Skorlar
            </a>
            <a href="{{ route('matches.by-date') }}" class="btn btn-outline-info">
                <i class="fas fa-calendar me-1"></i> Maç Takvimi
            </a>
        </div>
    </div>

    <!-- Leagues Grid -->
    <div class="col-12">
        @if(count($leagues) > 0)
            <div class="row">
                @foreach($leagues as $league)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @if(isset($league['league']['logo']))
                                        <img src="{{ $league['league']['logo'] }}" 
                                             alt="{{ $league['league']['name'] }}" 
                                             class="league-logo me-3">
                                    @else
                                        <div class="league-logo me-3 d-flex align-items-center justify-content-center bg-light rounded">
                                            <i class="fas fa-trophy text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">{{ $league['league']['name'] }}</h5>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="fas fa-flag me-1"></i>
                                            <small>{{ $league['country']['name'] ?? 'Uluslararası' }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- League Info -->
                                <div class="row text-center mb-3">
                                    @if(isset($league['seasons'][0]))
                                        <div class="col-4">
                                            <small class="text-muted d-block">Sezon</small>
                                            <strong>{{ $league['seasons'][0]['year'] ?? date('Y') }}</strong>
                                        </div>
                                    @endif
                                    <div class="col-4">
                                        <small class="text-muted d-block">Tip</small>
                                        <strong>{{ $league['league']['type'] ?? 'Lig' }}</strong>
                                    </div>
                                    @if(isset($league['seasons'][0]['start']) && isset($league['seasons'][0]['end']))
                                        <div class="col-4">
                                            <small class="text-muted d-block">Durum</small>
                                            @php
                                                $now = now();
                                                $start = \Carbon\Carbon::parse($league['seasons'][0]['start']);
                                                $end = \Carbon\Carbon::parse($league['seasons'][0]['end']);
                                                $isActive = $now->between($start, $end);
                                            @endphp
                                            <strong class="{{ $isActive ? 'text-success' : 'text-muted' }}">
                                                {{ $isActive ? 'Aktif' : 'Pasif' }}
                                            </strong>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('leagues.show', $league['league']['id']) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-table me-2"></i>
                                        Puan Durumu
                                    </a>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('matches.by-date') }}?league={{ $league['league']['id'] }}" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-calendar me-1"></i> Maçlar
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-secondary btn-sm"
                                                onclick="showLeagueInfo({{ $league['league']['id'] }})">
                                            <i class="fas fa-info-circle me-1"></i> Detaylar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- League Badge/Type -->
                            <div class="position-absolute top-0 end-0 m-3">
                                @if(str_contains(strtolower($league['league']['name']), 'champions'))
                                    <span class="badge bg-warning">
                                        <i class="fas fa-crown me-1"></i> Elit
                                    </span>
                                @elseif(str_contains(strtolower($league['league']['name']), 'europa'))
                                    <span class="badge bg-info">
                                        <i class="fas fa-globe me-1"></i> Avrupa
                                    </span>
                                @elseif(in_array($league['league']['id'], [39, 140, 78, 135, 61]))
                                    <span class="badge bg-success">
                                        <i class="fas fa-star me-1"></i> Top 5
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-shield-alt me-1"></i> Lig
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Additional Info Section -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Lig Bilgileri
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-crown me-2 text-warning"></i>Elite Ligeler</h6>
                                    <ul class="list-unstyled">
                                        <li><small>• UEFA Champions League</small></li>
                                        <li><small>• UEFA Europa League</small></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-star me-2 text-success"></i>Top 5 Ligeler</h6>
                                    <ul class="list-unstyled">
                                        <li><small>• Premier League (İngiltere)</small></li>
                                        <li><small>• La Liga (İspanya)</small></li>
                                        <li><small>• Bundesliga (Almanya)</small></li>
                                        <li><small>• Serie A (İtalya)</small></li>
                                        <li><small>• Ligue 1 (Fransa)</small></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                İstatistikler
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h3 class="text-primary mb-1">{{ count($leagues) }}</h3>
                                <small class="text-muted">Popüler Lig</small>
                            </div>
                            <div class="mb-3">
                                <h3 class="text-success mb-1">
                                    {{ collect($leagues)->pluck('country.name')->unique()->count() }}
                                </h3>
                                <small class="text-muted">Farklı Ülke</small>
                            </div>
                            <div>
                                <h3 class="text-info mb-1">
                                    {{ collect($leagues)->where('league.type', 'League')->count() }}
                                </h3>
                                <small class="text-muted">Ulusal Lig</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-trophy fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">Ligeler yüklenemiyor</h3>
                    <p class="text-muted mb-4">
                        Popüler ligeler şu anda yüklenemiyor.<br>
                        Lütfen daha sonra tekrar deneyin.
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
        @endif
    </div>
</div>

<!-- League Info Modal -->
<div class="modal fade" id="leagueInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lig Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="league-modal-body">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showLeagueInfo(leagueId) {
        const modal = new bootstrap.Modal(document.getElementById('leagueInfoModal'));
        const modalBody = document.getElementById('league-modal-body');
        
        // Find league data
        const leagues = @json($leagues);
        const league = leagues.find(l => l.league.id === leagueId);
        
        if (league) {
            modalBody.innerHTML = generateLeagueInfoHtml(league);
            modal.show();
        }
    }

    function generateLeagueInfoHtml(league) {
        const season = league.seasons && league.seasons[0] ? league.seasons[0] : null;
        
        return `
            <div class="d-flex align-items-center mb-4">
                <img src="${league.league.logo}" alt="${league.league.name}" class="me-3" style="width: 60px; height: 60px; object-fit: contain;">
                <div>
                    <h4 class="mb-1">${league.league.name}</h4>
                    <p class="text-muted mb-0">${league.country.name}</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Genel Bilgiler</h6>
                    <table class="table table-sm">
                        <tr><td>Lig ID:</td><td>${league.league.id}</td></tr>
                        <tr><td>Tip:</td><td>${league.league.type}</td></tr>
                        <tr><td>Ülke:</td><td>${league.country.name}</td></tr>
                        ${league.country.flag ? `<tr><td>Bayrak:</td><td><img src="${league.country.flag}" style="width: 20px;"></td></tr>` : ''}
                    </table>
                </div>
                <div class="col-md-6">
                    ${season ? `
                        <h6>Sezon Bilgileri</h6>
                        <table class="table table-sm">
                            <tr><td>Sezon:</td><td>${season.year}</td></tr>
                            <tr><td>Başlangıç:</td><td>${new Date(season.start).toLocaleDateString('tr-TR')}</td></tr>
                            <tr><td>Bitiş:</td><td>${new Date(season.end).toLocaleDateString('tr-TR')}</td></tr>
                            <tr><td>Güncel:</td><td>${season.current ? 'Evet' : 'Hayır'}</td></tr>
                        </table>
                    ` : '<p>Sezon bilgisi bulunmuyor</p>'}
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="/leagues/${league.league.id}" class="btn btn-primary">
                    <i class="fas fa-table me-2"></i>Puan Durumu
                </a>
                <a href="/matches/date?league=${league.league.id}" class="btn btn-outline-secondary">
                    <i class="fas fa-calendar me-2"></i>Maçları Gör
                </a>
            </div>
        `;
    }

    // Add hover effects
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'transform 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection 