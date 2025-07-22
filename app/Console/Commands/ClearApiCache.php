<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearApiCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:clear-cache {--force : Force clear all API cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear API-Football cache to free up daily request limits';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 API Cache temizleniyor...');
        
        // API cache'lerini temizle
        $cacheKeys = [
            'football_api_*', // Tüm football API cache'leri
            'api_requests_count_*' // Günlük istek sayacını da sıfırla
        ];
        
        $cleared = 0;
        
        // Cache store'dan tüm API ile ilgili cache'leri temizle
        foreach ($cacheKeys as $pattern) {
            $keys = Cache::getRedis()->keys($pattern);
            foreach ($keys as $key) {
                Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
                $cleared++;
            }
        }
        
        // Alternatif: File cache için tüm cache'i temizle
        if ($cleared === 0 || $this->option('force')) {
            $this->warn('⚠️  File cache sistemi tespit edildi. Tüm cache temizleniyor...');
            Artisan::call('cache:clear');
            $this->info('✅ Tüm cache temizlendi');
        } else {
            $this->info("✅ $cleared API cache entry temizlendi");
        }
        
        // Günlük istek sayacını manuel sıfırla
        $today = now()->format('Y-m-d');
        $requestCountKey = "api_requests_count_$today";
        Cache::forget($requestCountKey);
        
        $this->info('📊 Günlük istek sayacı sıfırlandı');
        
        $this->line('');
        $this->info('🎉 İşlem tamamlandı!');
        $this->line('');
        $this->comment('💡 İpuçları:');
        $this->comment('   • Artık yeni API istekleri gönderebilirsiniz');
        $this->comment('   • Free plan: 100 istek/gün limiti var');
        $this->comment('   • Cache sürelerini artırmak için plan yükseltin');
        $this->line('');
        $this->comment('🔍 API durumunu kontrol etmek için:');
        $this->comment('   php debug_api.php');
        
        return 0;
    }
}
