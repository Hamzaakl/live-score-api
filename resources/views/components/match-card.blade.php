@php
    // Match status mapping
    $statusMap = [
        'NS' => ['text' => 'Başlamamış', 'class' => 'status-scheduled'],
        '1H' => ['text' => 'İlk Yarı', 'class' => 'status-live'],
        'HT' => ['text' => 'Devre Arası', 'class' => 'status-live'],
        '2H' => ['text' => 'İkinci Yarı', 'class' => 'status-live'],
        'ET' => ['text' => 'Uzatma', 'class' => 'status-live'],
        'FT' => ['text' => 'Bitti', 'class' => 'status-finished'],
        'AET' => ['text' => 'Uzatmalarda Bitti', 'class' => 'status-finished'],
        'PEN' => ['text' => 'Penaltılarda Bitti', 'class' => 'status-finished'],
        'LIVE' => ['text' => 'CANLI', 'class' => 'status-live']
    ];
    
    $status = $statusMap[$match['fixture']['status']['short']] ?? ['text' => $match['fixture']['status']['short'], 'class' => 'status-scheduled'];
    $homeScore = $match['goals']['home'] ?? '-';
    $awayScore = $match['goals']['away'] ?? '-';
    $matchTime = '';
    
    // Format match time
    if ($match['fixture']['status']['short'] === 'NS') {
        $matchTime = date('H:i', strtotime($match['fixture']['date']));
    } elseif (in_array($match['fixture']['status']['short'], ['1H', '2H', 'ET'])) {
        $matchTime = $match['fixture']['status']['elapsed'] . "'";
    }
@endphp

<div class="card match-card">
    <div class="match-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-futbol me-2"></i>
                {{ $match['league']['name'] }}
            </div>
            <small>{{ $match['league']['country'] }}</small>
        </div>
    </div>
    <div class="match-body">
        <!-- Home Team -->
        <div class="team-info">
            @if(isset($match['teams']['home']['logo']))
                <img src="{{ $match['teams']['home']['logo'] }}" 
                     alt="{{ $match['teams']['home']['name'] }}" 
                     class="team-logo"
                     onerror="this.style.display='none';">
            @else
                <div class="team-logo d-flex align-items-center justify-content-center bg-light">
                    <i class="fas fa-shield-alt text-muted"></i>
                </div>
            @endif
            <div class="team-name">{{ $match['teams']['home']['name'] }}</div>
            <div class="score">{{ $homeScore }}</div>
        </div>
        
        <!-- Away Team -->
        <div class="team-info">
            @if(isset($match['teams']['away']['logo']))
                <img src="{{ $match['teams']['away']['logo'] }}" 
                     alt="{{ $match['teams']['away']['name'] }}" 
                     class="team-logo"
                     onerror="this.style.display='none';">
            @else
                <div class="team-logo d-flex align-items-center justify-content-center bg-light">
                    <i class="fas fa-shield-alt text-muted"></i>
                </div>
            @endif
            <div class="team-name">{{ $match['teams']['away']['name'] }}</div>
            <div class="score">{{ $awayScore }}</div>
        </div>
        
        <!-- Match Status -->
        <div class="match-status">
            <span class="status-badge {{ $status['class'] }}">
                {{ $status['text'] }}
                @if($matchTime)
                    - {{ $matchTime }}
                @endif
            </span>
            @if(isset($match['fixture']['date']))
                <div class="text-muted mt-1" style="font-size: 0.8rem;">
                    {{ date('d.m.Y H:i', strtotime($match['fixture']['date'])) }}
                </div>
            @endif
        </div>
        
        <!-- Action Buttons -->
        <div class="text-center mt-3">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('matches.show', $match['fixture']['id']) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-info-circle me-1"></i> Detaylar
                </a>
                @if($match['fixture']['status']['short'] === 'FT' || in_array($match['fixture']['status']['short'], ['1H', '2H', 'HT', 'ET']))
                    <button type="button" 
                            class="btn btn-outline-success"
                            onclick="showStatistics({{ $match['fixture']['id'] }})">
                        <i class="fas fa-chart-bar me-1"></i> İstatistik
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Additional Info for Live Matches -->
        @if(in_array($match['fixture']['status']['short'], ['1H', '2H', 'HT', 'ET']) && isset($match['score']))
            <div class="row mt-2 text-center">
                <div class="col-6">
                    <small class="text-muted">İlk Yarı</small><br>
                    <strong>{{ $match['score']['halftime']['home'] ?? 0 }} - {{ $match['score']['halftime']['away'] ?? 0 }}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted">Genel</small><br>
                    <strong>{{ $match['score']['fulltime']['home'] ?? $homeScore }} - {{ $match['score']['fulltime']['away'] ?? $awayScore }}</strong>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function showStatistics(fixtureId) {
    // Statistics modal or redirect functionality
    window.location.href = `/matches/${fixtureId}#statistics`;
}
</script> 