<?php

namespace App\Http\Controllers;

use App\Services\FootballApiService;
use Illuminate\Http\Request;

class LiveScoreController extends Controller
{
    private $footballApi;

    public function __construct(FootballApiService $footballApi)
    {
        $this->footballApi = $footballApi;
    }

    /**
     * Canlı skorlar sayfası
     */
    public function index()
    {
        try {
            $liveMatches = $this->footballApi->getLiveMatches();

            return view('live-scores', [
                'matches' => $liveMatches['response'] ?? [],
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('live-scores', [
                'matches' => [],
                'error' => 'Canlı skorlar yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX ile canlı skorları getir
     */
    public function getLiveScores()
    {
        try {
            $liveMatches = $this->footballApi->getLiveMatches();
            
            return response()->json([
                'success' => true,
                'data' => $liveMatches['response'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Belirli bir tarihteki maçları getir
     */
    public function getMatchesByDate(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        
        try {
            $matches = $this->footballApi->getMatchesByDate($date);

            return view('matches-by-date', [
                'matches' => $matches['response'] ?? [],
                'date' => $date,
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('matches-by-date', [
                'matches' => [],
                'date' => $date,
                'error' => 'Maçlar yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
} 