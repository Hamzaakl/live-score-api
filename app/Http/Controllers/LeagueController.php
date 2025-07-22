<?php

namespace App\Http\Controllers;

use App\Services\FootballApiService;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    private $footballApi;

    public function __construct(FootballApiService $footballApi)
    {
        $this->footballApi = $footballApi;
    }

    /**
     * Tüm ligeler sayfası
     */
    public function index(Request $request)
    {
        try {
            // Varsayılan sezon 2022 (Premier League 2022-23 sezonu)
            $season = $request->get('season', '2022');
            $leagues = $this->footballApi->getLeagues($season);

            return view('leagues.index', [
                'leagues' => $leagues['response'] ?? [],
                'season' => $season,
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('leagues.index', [
                'leagues' => [],
                'season' => $season ?? '2022',
                'error' => 'Ligeler yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lig detayları ve puan durumu
     */
    public function show($id, Request $request)
    {
        try {
            // Varsayılan sezon 2022 (Premier League 2022-23 sezonu)
            $season = $request->get('season', '2022');
            
            // Lig bilgilerini getir
            $leagueData = $this->footballApi->getLeagues($season);
            $league = collect($leagueData['response'] ?? [])->firstWhere('league.id', (int)$id);
            
            if (!$league) {
                throw new \Exception('Lig bulunamadı');
            }

            // Puan durumunu getir
            $standings = $this->footballApi->getStandings($id, $season);

            return view('leagues.show', [
                'league' => $league,
                'standings' => $standings['response'][0]['league']['standings'][0] ?? [],
                'season' => $season,
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('leagues.show', [
                'league' => null,
                'standings' => [],
                'season' => $season ?? '2022',
                'error' => 'Lig bilgileri yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Popüler ligeler
     */
    public function popular()
    {
        try {
            $popularLeagues = $this->footballApi->getPopularLeagues();

            return view('leagues.popular', [
                'leagues' => $popularLeagues['response'] ?? [],
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('leagues.popular', [
                'leagues' => [],
                'error' => 'Popüler ligeler yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
} 