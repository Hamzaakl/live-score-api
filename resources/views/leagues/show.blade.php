@extends('layouts.app')

@section('title', $league ? $league['league']['name'] . ' - Puan Durumu' : 'Lig Detayları')

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

    @if($league)
        <!-- League Header -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                @if(isset($league['league']['logo']))
                                    <img src="{{ $league['league']['logo'] }}" 
                                         alt="{{ $league['league']['name'] }}" 
                                         class="me-3"
                                         style="width: 60px; height: 60px; object-fit: contain;">
                                @endif
                                <div>
                                    <h3 class="mb-1">{{ $league['league']['name'] }}</h3>
                                    <div class="d-flex align-items-center text-muted">
                                        @if(isset($league['country']['flag']))
                                            <img src="{{ $league['country']['flag'] }}" 
                                                 alt="{{ $league['country']['name'] }}" 
                                                 class="me-2"
                                                 style="width: 20px; height: 15px; object-fit: cover;">
                                        @endif
                                        <span>{{ $league['country']['name'] }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $season }} Sezonu</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="btn-group" role="group">
                                <a href="{{ route('matches.by-date') }}?league={{ $league['league']['id'] }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-calendar me-1"></i> Maçlar
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        {{ $season }}
                                    </button>
                                    <ul class="dropdown-menu">
                                        @for($year = date('Y'); $year >= date('Y') - 3; $year--)
                                            <li>
                                                <a class="dropdown-item {{ $season == $year ? 'active' : '' }}" 
                                                   href="{{ route('leagues.show', $league['league']['id']) }}?season={{ $year }}">
                                                    {{ $year }} Sezonu
                                                </a>
                                            </li>
                                        @endfor
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Standings Table -->
        @if(count($standings) > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>
                            Puan Durumu
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table standings-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Sıra</th>
                                        <th>Takım</th>
                                        <th class="text-center">O</th>
                                        <th class="text-center">G</th>
                                        <th class="text-center">B</th>
                                        <th class="text-center">M</th>
                                        <th class="text-center">A</th>
                                        <th class="text-center">Y</th>
                                        <th class="text-center">AV</th>
                                        <th class="text-center">P</th>
                                        <th class="text-center">Form</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($standings as $standing)
                                        <tr class="position-row position-{{ $standing['rank'] }}"
                                            data-position="{{ $standing['rank'] }}">
                                            <td class="position">
                                                <div class="d-flex align-items-center">
                                                    <span class="rank-number">{{ $standing['rank'] }}</span>
                                                    @if($standing['rank'] <= 4 && in_array($league['league']['id'], [39, 140, 78, 135, 61]))
                                                        <span class="badge bg-success ms-2" title="Şampiyonlar Ligi">CL</span>
                                                    @elseif($standing['rank'] <= 6 && in_array($league['league']['id'], [39, 140, 78, 135, 61]))
                                                        <span class="badge bg-warning ms-2" title="Avrupa Ligi">EL</span>
                                                    @elseif($standing['rank'] > count($standings) - 3)
                                                        <span class="badge bg-danger ms-2" title="Küme Düşme">↓</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="team-cell">
                                                @if(isset($standing['team']['logo']))
                                                    <img src="{{ $standing['team']['logo'] }}" 
                                                         alt="{{ $standing['team']['name'] }}"
                                                         onerror="this.style.display='none';">
                                                @endif
                                                <span class="team-name fw-bold">{{ $standing['team']['name'] }}</span>
                                            </td>
                                            <td class="text-center">{{ $standing['all']['played'] }}</td>
                                            <td class="text-center text-success">{{ $standing['all']['win'] }}</td>
                                            <td class="text-center text-warning">{{ $standing['all']['draw'] }}</td>
                                            <td class="text-center text-danger">{{ $standing['all']['lose'] }}</td>
                                            <td class="text-center">{{ $standing['all']['goals']['for'] }}</td>
                                            <td class="text-center">{{ $standing['all']['goals']['against'] }}</td>
                                            <td class="text-center {{ $standing['goalsDiff'] > 0 ? 'text-success' : ($standing['goalsDiff'] < 0 ? 'text-danger' : '') }}">
                                                {{ $standing['goalsDiff'] > 0 ? '+' : '' }}{{ $standing['goalsDiff'] }}
                                            </td>
                                            <td class="text-center fw-bold">{{ $standing['points'] }}</td>
                                            <td class="text-center">
                                                @if(isset($standing['form']))
                                                    @foreach(str_split($standing['form']) as $result)
                                                        <span class="badge 
                                                            @if($result === 'W') bg-success
                                                            @elseif($result === 'D') bg-warning
                                                            @elseif($result === 'L') bg-danger
                                                            @else bg-secondary
                                                            @endif
                                                            me-1" style="font-size: 0.7rem;">{{ $result }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Toplam Takım</small>
                                <strong>{{ count($standings) }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Toplam Maç</small>
                                <strong>{{ collect($standings)->sum('all.played') / 2 }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Toplam Gol</small>
                                <strong>{{ collect($standings)->sum('all.goals.for') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Maç Ortalaması</small>
                                <strong>{{ round(collect($standings)->sum('all.goals.for') / max(collect($standings)->sum('all.played'), 1) * 2, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="card mt-3">
                    <div class="card-body py-2">
                        <small class="text-muted">
                            <strong>Kısaltmalar:</strong>
                            O: Oynadı, G: Galibiyet, B: Beraberlik, M: Mağlubiyet, A: Attığı, Y: Yediği, AV: Averaj, P: Puan
                        </small>
                        <br>
                        <small class="text-muted">
                            <strong>Renkler:</strong>
                            <span class="badge bg-success ms-1">CL</span> Şampiyonlar Ligi,
                            <span class="badge bg-warning ms-1">EL</span> Avrupa Ligi,
                            <span class="badge bg-danger ms-1">↓</span> Küme Düşme
                        </small>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="col-12 mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-crown me-2"></i>En İyi Performans</h6>
                            </div>
                            <div class="card-body">
                                @php $topTeam = collect($standings)->first(); @endphp
                                @if($topTeam)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $topTeam['team']['logo'] }}" 
                                             style="width: 30px; height: 30px; object-fit: contain;" 
                                             class="me-2">
                                        <div>
                                            <strong>{{ $topTeam['team']['name'] }}</strong>
                                            <small class="d-block text-muted">{{ $topTeam['points'] }} Puan</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-futbol me-2"></i>En Golcü</h6>
                            </div>
                            <div class="card-body">
                                @php $topScorer = collect($standings)->sortByDesc('all.goals.for')->first(); @endphp
                                @if($topScorer)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $topScorer['team']['logo'] }}" 
                                             style="width: 30px; height: 30px; object-fit: contain;" 
                                             class="me-2">
                                        <div>
                                            <strong>{{ $topScorer['team']['name'] }}</strong>
                                            <small class="d-block text-muted">{{ $topScorer['all']['goals']['for'] }} Gol</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>En Sağlam Savunma</h6>
                            </div>
                            <div class="card-body">
                                @php $bestDefense = collect($standings)->sortBy('all.goals.against')->first(); @endphp
                                @if($bestDefense)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $bestDefense['team']['logo'] }}" 
                                             style="width: 30px; height: 30px; object-fit: contain;" 
                                             class="me-2">
                                        <div>
                                            <strong>{{ $bestDefense['team']['name'] }}</strong>
                                            <small class="d-block text-muted">{{ $bestDefense['all']['goals']['against'] }} Yenen</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-table fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Puan durumu bulunamadı</h3>
                        <p class="text-muted mb-4">
                            {{ $season }} sezonu için puan durumu henüz mevcut değil.<br>
                            Farklı bir sezon seçmeyi deneyin.
                        </p>
                        <a href="{{ route('leagues.popular') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Popüler Liglere Dön
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger mb-4"></i>
                    <h3 class="text-danger mb-3">Lig bulunamadı</h3>
                    <p class="text-muted mb-4">
                        Aradığınız lig bulunamadı veya erişim sırasında bir hata oluştu.
                    </p>
                    <div class="d-grid gap-2 d-md-block">
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Tekrar Dene
                        </button>
                        <a href="{{ route('leagues.popular') }}" class="btn btn-outline-primary">
                            <i class="fas fa-trophy me-2"></i>
                            Popüler Liglere Dön
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
    .position-row {
        transition: background-color 0.3s ease;
    }
    
    .position-row:hover {
        background-color: rgba(0,123,255,0.1);
    }
    
    .rank-number {
        font-weight: bold;
        min-width: 20px;
        text-align: center;
        display: inline-block;
    }
    
    .team-name {
        color: var(--primary-color);
    }
    
    .position-1 { 
        background: linear-gradient(90deg, rgba(40, 167, 69, 0.1), transparent);
        border-left: 4px solid #28a745;
    }
    
    .badge {
        font-size: 0.65rem !important;
    }
    
    @media (max-width: 768px) {
        .table th, .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
        }
        
        .team-cell img {
            width: 20px !important;
            height: 20px !important;
        }
        
        .team-name {
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Smooth scroll to team position
    function scrollToTeam(teamName) {
        const rows = document.querySelectorAll('.team-name');
        rows.forEach(row => {
            if (row.textContent.includes(teamName)) {
                row.closest('tr').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
    
    // Add click handlers for team rows
    document.addEventListener('DOMContentLoaded', function() {
        const teamRows = document.querySelectorAll('.position-row');
        teamRows.forEach(row => {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function() {
                // You could add team detail functionality here
                console.log('Team clicked:', this.querySelector('.team-name').textContent);
            });
        });
    });
    
    // Auto-refresh if current season
    @if($season == date('Y'))
        setTimeout(() => {
            location.reload();
        }, 300000); // 5 minutes
    @endif
</script>
@endsection 