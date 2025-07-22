<?php

namespace App\Http\Controllers;

use App\Services\FootballApiService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    private $footballApi;

    public function __construct(FootballApiService $footballApi)
    {
        $this->footballApi = $footballApi;
    }

    /**
     * Maç detayları sayfası
     */
    public function show($id)
    {
        try {
            // Maç detaylarını getir
            $matchData = $this->footballApi->getMatchDetails($id);
            $match = $matchData['response'][0] ?? null;

            if (!$match) {
                throw new \Exception('Maç bulunamadı');
            }

            // Maç istatistiklerini getir
            $statistics = $this->footballApi->getMatchStatistics($id);
            
            // Maç olaylarını getir (goller, kartlar vs.)
            $events = $this->footballApi->getMatchEvents($id);

            return view('matches.show', [
                'match' => $match,
                'statistics' => $statistics['response'] ?? [],
                'events' => $events['response'] ?? [],
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('matches.show', [
                'match' => null,
                'statistics' => [],
                'events' => [],
                'error' => 'Maç bilgileri yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Maç istatistiklerini AJAX ile getir
     */
    public function getStatistics($id)
    {
        try {
            $statistics = $this->footballApi->getMatchStatistics($id);
            
            return response()->json([
                'success' => true,
                'data' => $statistics['response'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Maç olaylarını AJAX ile getir
     */
    public function getEvents($id)
    {
        try {
            $events = $this->footballApi->getMatchEvents($id);
            
            return response()->json([
                'success' => true,
                'data' => $events['response'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 