@extends('layouts.app')

@section('title', 'Maç Takvimi')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-calendar me-2"></i>
                    Maç Takvimi
                </h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">Belirli bir tarihteki maçları görüntüleyin</p>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('matches.by-date') }}" class="d-flex">
                            <input type="date" 
                                   class="form-control me-2" 
                                   name="date" 
                                   value="{{ $date }}"
                                   max="{{ date('Y-m-d', strtotime('+7 days')) }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
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

    <!-- Date Navigation -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d', strtotime($date . ' -1 day')) }}" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-chevron-left me-1"></i> Önceki Gün
                    </a>
                    
                    <div class="text-center">
                        <h5 class="mb-1">{{ date('d F Y', strtotime($date)) }}</h5>
                        <small class="text-muted">{{ date('l', strtotime($date)) }}</small>
                    </div>
                    
                    <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d', strtotime($date . ' +1 day')) }}" 
                       class="btn btn-outline-primary">
                        Sonraki Gün <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Date Selection -->
    <div class="col-12 mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d', strtotime('-1 day')) }}" 
               class="btn btn-sm {{ $date === date('Y-m-d', strtotime('-1 day')) ? 'btn-primary' : 'btn-outline-secondary' }}">
                Dün
            </a>
            <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d') }}" 
               class="btn btn-sm {{ $date === date('Y-m-d') ? 'btn-primary' : 'btn-outline-secondary' }}">
                Bugün
            </a>
            <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d', strtotime('+1 day')) }}" 
               class="btn btn-sm {{ $date === date('Y-m-d', strtotime('+1 day')) ? 'btn-primary' : 'btn-outline-secondary' }}">
                Yarın
            </a>
            <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d', strtotime('+2 days')) }}" 
               class="btn btn-sm {{ $date === date('Y-m-d', strtotime('+2 days')) ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ date('d.m', strtotime('+2 days')) }}
            </a>
            <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d', strtotime('+3 days')) }}" 
               class="btn btn-sm {{ $date === date('Y-m-d', strtotime('+3 days')) ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ date('d.m', strtotime('+3 days')) }}
            </a>
        </div>
    </div>

    <!-- Matches -->
    <div class="col-12">
        @if(count($matches) > 0)
            @php
                $groupedMatches = collect($matches)->groupBy('league.name');
            @endphp
            
            @foreach($groupedMatches as $leagueName => $leagueMatches)
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        @if(isset($leagueMatches[0]['league']['logo']))
                            <img src="{{ $leagueMatches[0]['league']['logo'] }}" 
                                 alt="{{ $leagueName }}" 
                                 class="me-3"
                                 style="width: 30px; height: 30px; object-fit: contain;">
                        @endif
                        <div>
                            <h5 class="mb-0">{{ $leagueName }}</h5>
                            <small class="text-light">
                                {{ $leagueMatches[0]['league']['country'] }} - {{ count($leagueMatches) }} Maç
                            </small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row">
                            @foreach($leagueMatches as $match)
                                <div class="col-lg-6 p-3">
                                    @include('components.match-card', ['match' => $match])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Load More Button (if there are many matches) -->
            @if(count($matches) >= 50)
                <div class="text-center mt-4">
                    <p class="text-muted">İlk 50 maç gösteriliyor</p>
                    <small class="text-muted">Daha spesifik tarih aralığı seçerek daha az sonuç görüntüleyebilirsiniz</small>
                </div>
            @endif
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">{{ date('d F Y', strtotime($date)) }} tarihinde maç bulunmuyor</h3>
                    <p class="text-muted mb-4">
                        Seçtiğiniz tarihte herhangi bir maç planlanmamış.<br>
                        Farklı bir tarih seçmeyi deneyin.
                    </p>
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('matches.by-date') }}?date={{ date('Y-m-d') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-day me-2"></i>
                            Bugünün Maçları
                        </a>
                        <a href="{{ route('live-scores') }}" class="btn btn-outline-primary">
                            <i class="fas fa-broadcast-tower me-2"></i>
                            Canlı Skorlar
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Match Statistics Summary -->
    @if(count($matches) > 0)
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Günün Özeti
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ count($matches) }}</h3>
                                <small class="text-muted">Toplam Maç</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-success mb-1">{{ $groupedMatches->count() }}</h3>
                                <small class="text-muted">Farklı Lig</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                @php
                                    $liveMatches = collect($matches)->where('fixture.status.short', '!=', 'NS')->where('fixture.status.short', '!=', 'FT');
                                @endphp
                                <h3 class="text-warning mb-1">{{ $liveMatches->count() }}</h3>
                                <small class="text-muted">Devam Eden</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                @php
                                    $finishedMatches = collect($matches)->where('fixture.status.short', 'FT');
                                @endphp
                                <h3 class="text-info mb-1">{{ $finishedMatches->count() }}</h3>
                                <small class="text-muted">Tamamlanan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh for live matches on current date
    @if($date === date('Y-m-d'))
        setInterval(function() {
            // Only refresh if there are live matches
            const liveMatches = document.querySelectorAll('.status-live');
            if (liveMatches.length > 0) {
                location.reload();
            }
        }, 60000); // Refresh every minute
    @endif

    // Date input validation
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('input[type="date"]');
        if (dateInput) {
            // Set min date to 30 days ago
            const minDate = new Date();
            minDate.setDate(minDate.getDate() - 30);
            dateInput.min = minDate.toISOString().split('T')[0];
            
            // Set max date to 7 days from now
            const maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + 7);
            dateInput.max = maxDate.toISOString().split('T')[0];
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft') {
            // Previous day
            const prevButton = document.querySelector('a[href*="' + '{{ date('Y-m-d', strtotime($date . ' -1 day')) }}' + '"]');
            if (prevButton && !event.ctrlKey && !event.metaKey) {
                window.location.href = prevButton.href;
            }
        } else if (event.key === 'ArrowRight') {
            // Next day
            const nextButton = document.querySelector('a[href*="' + '{{ date('Y-m-d', strtotime($date . ' +1 day')) }}' + '"]');
            if (nextButton && !event.ctrlKey && !event.metaKey) {
                window.location.href = nextButton.href;
            }
        }
    });

    // Smooth scroll to league sections
    function scrollToLeague(leagueName) {
        const element = document.querySelector(`[data-league="${leagueName}"]`);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>

<style>
    .card-header img {
        border-radius: 0.25rem;
    }
    
    .btn-group-sm .btn {
        font-size: 0.8rem;
    }
    
    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        
        .d-flex.flex-wrap.gap-2 {
            justify-content: center !important;
        }
    }
</style>
@endsection 