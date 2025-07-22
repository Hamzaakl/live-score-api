<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class FootballApiService
{
    private $baseUrl = 'https://v3.football.api-sports.io';
    private $headers;

    public function __construct()
    {
        $this->headers = [
            'X-RapidAPI-Host' => 'v3.football.api-sports.io',
            'X-RapidAPI-Key' => env('RAPIDAPI_KEY')
        ];
    }

    /**
     * API'ye istek gönder  
     */
    private function makeRequest($endpoint, $params = [])
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('API request failed: ' . $response->status());
        } catch (Exception $e) {
            throw new Exception('API Error: ' . $e->getMessage());
        }
    }

    /**
     * Cache'li API isteği
     * Free plan için optimized - longer cache times
     */
    private function makeRequestWithCache($endpoint, $params = [], $cacheMinutes = 15)
    {
        $cacheKey = 'football_api_' . md5($endpoint . serialize($params));
        
        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($endpoint, $params) {
            // Free plan günlük istek limitini kontrol et
            $this->checkDailyLimit();
            return $this->makeRequest($endpoint, $params);
        });
    }

    /**
     * Günlük API istek limitini kontrol et (Free plan: 100 istek/gün)
     */
    private function checkDailyLimit()
    {
        $today = now()->format('Y-m-d');
        $requestCountKey = "api_requests_count_$today";
        $currentCount = Cache::get($requestCountKey, 0);
        
        // Free plan limiti (günlük 100 istek)
        if ($currentCount >= 95) { // 95'te dur, güvenlik için
            \Log::warning('API daily limit approaching', [
                'date' => $today,
                'requests' => $currentCount
            ]);
            
            throw new Exception('API günlük limit aşılıyor! Lütfen cache\'in temizlenmesini bekleyin veya plan yükseltin.');
        }
        
        // İstek sayısını artır
        Cache::put($requestCountKey, $currentCount + 1, now()->endOfDay());
    }

    /**
     * Canlı maçları getir
     */
    public function getLiveMatches()
    {
        return $this->makeRequest('/fixtures', ['live' => 'all']);
    }

    /**
     * Bugünün maçlarını getir
     * Free plan için cache süresi artırıldı (10dk → 30dk)
     */
    public function getTodayMatches()
    {
        return $this->makeRequestWithCache('/fixtures', [
            'date' => now()->format('Y-m-d')
        ], 30); // Free plan için cache artırıldı
    }

    /**
     * Belirli bir tarihteki maçları getir
     * Free plan için cache süresi artırıldı (30dk → 60dk)
     */
    public function getMatchesByDate($date)
    {
        return $this->makeRequestWithCache('/fixtures', [
            'date' => $date
        ], 60); // Free plan için cache artırıldı
    }

    /**
     * Tüm ligleri getir
     * Free plan için cache süresi artırıldı (24 saat → 48 saat)
     */
    public function getLeagues($season = null)
    {
        $params = [];
        if ($season) {
            $params['season'] = $season;
        }
        
        return $this->makeRequestWithCache('/leagues', $params, 2880); // 48 saat cache (Free plan optimized)
    }

    /**
     * Belirli bir ligin puan durumunu getir
     * Free plan için cache süresi artırıldı (1 saat → 4 saat)
     */
    public function getStandings($leagueId, $season)
    {
        return $this->makeRequestWithCache('/standings', [
            'league' => $leagueId,
            'season' => $season
        ], 240); // 4 saat cache (Free plan optimized)
    }

    /**
     * Maç detaylarını getir
     * Free plan için cache süresi artırıldı (5dk → 15dk)
     */
    public function getMatchDetails($fixtureId)
    {
        return $this->makeRequestWithCache('/fixtures', [
            'id' => $fixtureId
        ], 15); // Free plan için cache artırıldı
    }

    /**
     * Maç istatistiklerini getir
     * Free plan için cache süresi artırıldı (5dk → 30dk)
     */
    public function getMatchStatistics($fixtureId)
    {
        return $this->makeRequestWithCache('/fixtures/statistics', [
            'fixture' => $fixtureId
        ], 30); // Free plan için cache artırıldı
    }

    /**
     * Maç event'lerini getir (goller, kartlar vs.)
     * Free plan için cache süresi artırıldı (5dk → 30dk)
     */
    public function getMatchEvents($fixtureId)
    {
        return $this->makeRequestWithCache('/fixtures/events', [
            'fixture' => $fixtureId
        ], 30); // Free plan için cache artırıldı
    }

    /**
     * Takım bilgilerini getir
     */
    public function getTeam($teamId)
    {
        return $this->makeRequestWithCache('/teams', [
            'id' => $teamId
        ], 1440);
    }

    /**
     * Takımın maçlarını getir
     */
    public function getTeamFixtures($teamId, $season, $league = null)
    {
        $params = [
            'team' => $teamId,
            'season' => $season
        ];
        
        if ($league) {
            $params['league'] = $league;
        }
        
        return $this->makeRequestWithCache('/fixtures', $params, 60);
    }

    /**
     * Popüler ligleri getir
     */
    public function getPopularLeagues()
    {
        $popularLeagueIds = [
            39,  // Premier League
            140, // La Liga
            78,  // Bundesliga
            135, // Serie A
            61,  // Ligue 1
            2,   // UEFA Champions League
            3,   // UEFA Europa League
            203, // Turkish Super Lig
        ];

        $leagues = [];
        foreach ($popularLeagueIds as $leagueId) {
            try {
                $league = $this->makeRequestWithCache('/leagues', [
                    'id' => $leagueId
                ], 2880); // 48 saat cache (Free plan optimized)
                
                if (isset($league['response'][0])) {
                    $leagues[] = $league['response'][0];
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return ['response' => $leagues];
    }

    /**
     * Ülkeleri getir
     */
    public function getCountries()
    {
        return $this->makeRequestWithCache('/countries', [], 2880); // 48 saat cache
    }

    /**
     * API durumunu kontrol et
     */
    public function getApiStatus()
    {
        try {
            $response = $this->makeRequest('/status');
            return $response;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
} 