<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveScoreController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\MatchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Ana sayfa
Route::get('/', [HomeController::class, 'index'])->name('home');

// Canlı skorlar
Route::get('/live-scores', [LiveScoreController::class, 'index'])->name('live-scores');
Route::get('/matches/date', [LiveScoreController::class, 'getMatchesByDate'])->name('matches.by-date');

// Ligeler
Route::get('/leagues', [LeagueController::class, 'index'])->name('leagues.index');
Route::get('/leagues/popular', [LeagueController::class, 'popular'])->name('leagues.popular');
Route::get('/leagues/{id}', [LeagueController::class, 'show'])->name('leagues.show');

// Premier League 2022 sezonu için doğrudan erişim
Route::get('/premier-league-2022', function() {
    return redirect()->route('leagues.show', ['id' => 39, 'season' => 2022]);
})->name('premier.league.2022');

// Maç detayları
Route::get('/matches/{id}', [MatchController::class, 'show'])->name('matches.show');

// AJAX Routes
Route::get('/api/live-scores', [LiveScoreController::class, 'getLiveScores'])->name('api.live-scores');
Route::get('/api/matches/{id}/statistics', [MatchController::class, 'getStatistics'])->name('api.matches.statistics');
Route::get('/api/matches/{id}/events', [MatchController::class, 'getEvents'])->name('api.matches.events');

// =============================================================================
// DEBUG ROUTES - Sadece local environment için
// =============================================================================

if (app()->environment('local')) {
    // API debug sayfası
    Route::get('/debug/api', function() {
        $footballApi = app(\App\Services\FootballApiService::class);
        
        $results = [];
                 $tests = [
             'API Status' => function() use ($footballApi) {
                 return $footballApi->getApiStatus();
             },
             'Popular Leagues' => function() use ($footballApi) {
                 return $footballApi->getPopularLeagues();
             },
             'Premier League 2022 Details' => function() use ($footballApi) {
                 $leagues = $footballApi->getLeagues(2022);
                 // Premier League'i filtrele (ID: 39)
                 $premierLeague = collect($leagues['response'] ?? [])->firstWhere('league.id', 39);
                 return ['response' => $premierLeague ? [$premierLeague] : [], 'results' => $premierLeague ? 1 : 0];
             },
             'Premier League 2022 Standings' => function() use ($footballApi) {
                 return $footballApi->getStandings(39, 2022);
             },
             'Premier League 2022 Fixtures (Sample)' => function() use ($footballApi) {
                 // 2022 sezonundan örnek maçlar
                 return $footballApi->getMatchesByDate('2022-08-06'); // Premier League ilk hafta
             }
         ];
        
        foreach ($tests as $testName => $testFunction) {
            try {
                $start = microtime(true);
                $result = $testFunction();
                $duration = round((microtime(true) - $start) * 1000, 2);
                
                $results[] = [
                    'name' => $testName,
                    'status' => 'success',
                    'duration' => $duration . 'ms',
                    'result_count' => isset($result['results']) ? $result['results'] : (count($result['response'] ?? [])),
                    'error' => null,
                    'data' => json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'name' => $testName,
                    'status' => 'error',
                    'duration' => '0ms',
                    'result_count' => 0,
                    'error' => $e->getMessage(),
                    'data' => null
                ];
            }
        }
        
        return view('debug.api', ['results' => $results]);
    })->name('debug.api');
    
    // API key test
    Route::get('/debug/api-key', function() {
        $apiKey = env('RAPIDAPI_KEY');
        $masked = $apiKey ? substr($apiKey, 0, 10) . '...' . substr($apiKey, -5) : 'NOT SET';
        
        return response()->json([
            'api_key_set' => !empty($apiKey),
            'api_key_masked' => $masked,
            'base_url' => 'https://v3.football.api-sports.io',
            'headers' => [
                'X-RapidAPI-Host' => 'v3.football.api-sports.io',
                'X-RapidAPI-Key' => $apiKey ? 'SET' : 'NOT SET'
            ]
        ]);
    })->name('debug.api-key');
    
    // Cache status (JSON)
    Route::get('/debug/cache', function() {
        $cacheDriver = config('cache.default');
        $cacheKeys = [];
        
        // Cache'deki football API key'lerini say
        if ($cacheDriver === 'file') {
            $cacheDir = storage_path('framework/cache/data');
            if (is_dir($cacheDir)) {
                $files = glob($cacheDir . '/*');
                $footballCacheCount = 0;
                foreach ($files as $file) {
                    if (strpos(file_get_contents($file), 'football_api_') !== false) {
                        $footballCacheCount++;
                    }
                }
                $cacheKeys['football_api_count'] = $footballCacheCount;
            }
        }
        
        return response()->json([
            'cache_driver' => $cacheDriver,
            'cache_keys' => $cacheKeys,
            'today_requests' => \Cache::get('api_requests_count_' . now()->format('Y-m-d'), 0)
        ]);
    })->name('debug.cache');
    
    // Cache status (HTML sayfası)
    Route::get('/debug/cache-page', function() {
        return view('debug.cache');
    })->name('debug.cache.page');
}
