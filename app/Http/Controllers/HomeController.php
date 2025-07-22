<?php

namespace App\Http\Controllers;

use App\Services\FootballApiService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $footballApi;

    public function __construct(FootballApiService $footballApi)
    {
        $this->footballApi = $footballApi;
    }

    /**
     * Ana sayfa
     */
    public function index()
    {
        try {
            // Canlı maçları getir
            $liveMatches = $this->footballApi->getLiveMatches();
            
            // Bugünün maçlarını getir
            $todayMatches = $this->footballApi->getTodayMatches();
            
            // Popüler ligleri getir
            $popularLeagues = $this->footballApi->getPopularLeagues();

            return view('home', [
                'liveMatches' => $liveMatches['response'] ?? [],
                'todayMatches' => $todayMatches['response'] ?? [],
                'popularLeagues' => $popularLeagues['response'] ?? [],
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('home', [
                'liveMatches' => [],
                'todayMatches' => [],
                'popularLeagues' => [],
                'error' => 'Veriler yüklenirken bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
} 