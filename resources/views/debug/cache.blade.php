@extends('layouts.app')

@section('title', 'Cache Debug - LiveScore')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 text-gradient mb-2">💾 Cache Debug Panel</h1>
                    <p class="text-muted">Cache durumu ve API istek sayıları</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Yenile
                    </button>
                    <button class="btn btn-outline-danger" onclick="clearCache()">
                        <i class="fas fa-trash"></i> Cache Temizle
                    </button>
                </div>
            </div>

            <!-- Cache Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Cache Driver</h6>
                                    <h4 class="mb-0" id="cache-driver">...</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-football-ball"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Football API Cache</h6>
                                    <h4 class="mb-0" id="football-cache">...</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-counter"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Bugünkü İstekler</h6>
                                    <h4 class="mb-0" id="today-requests">...</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cache Details -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Cache Detayları</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Cache Bilgileri</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Driver:</strong> <span id="cache-driver-detail">...</span>
                                </li>
                                <li class="mb-2">
                                    <strong>Football Cache Count:</strong> <span id="football-count">...</span>
                                </li>
                                <li class="mb-2">
                                    <strong>Cache Directory:</strong> 
                                    <code>storage/framework/cache/data</code>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">API Limitleri</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Günlük Limit:</strong> 100 istek (Free Plan)
                                </li>
                                <li class="mb-2">
                                    <strong>Kullanılan:</strong> <span id="used-requests">...</span>
                                </li>
                                <li class="mb-2">
                                    <strong>Kalan:</strong> <span id="remaining-requests">...</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Cache İşlemleri</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-block">
                        <button class="btn btn-outline-warning" onclick="clearApiCache()">
                            <i class="fas fa-broom me-2"></i>API Cache Temizle
                        </button>
                        <button class="btn btn-outline-danger" onclick="clearAllCache()">
                            <i class="fas fa-trash me-2"></i>Tüm Cache Temizle
                        </button>
                        <button class="btn btn-outline-info" onclick="resetRequestCounter()">
                            <i class="fas fa-counter me-2"></i>İstek Sayacını Sıfırla
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.text-gradient {
    background: linear-gradient(45deg, #007bff, #6f42c1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCacheData();
    setInterval(loadCacheData, 30000); // 30 saniyede bir güncelle
});

function loadCacheData() {
    fetch('/debug/cache')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cache-driver').textContent = data.cache_driver;
            document.getElementById('cache-driver-detail').textContent = data.cache_driver;
            document.getElementById('football-cache').textContent = data.cache_keys.football_api_count || 0;
            document.getElementById('football-count').textContent = data.cache_keys.football_api_count || 0;
            document.getElementById('today-requests').textContent = data.today_requests + '/100';
            document.getElementById('used-requests').textContent = data.today_requests;
            document.getElementById('remaining-requests').textContent = (100 - data.today_requests);
        })
        .catch(e => {
            console.error('Cache data yüklenemedi:', e);
        });
}

function clearApiCache() {
    if (confirm('API cache temizlensin mi?')) {
        alert('Terminal\'de çalıştırın: php artisan api:clear-cache');
    }
}

function clearAllCache() {
    if (confirm('Tüm cache temizlensin mi?')) {
        alert('Terminal\'de çalıştırın: php artisan cache:clear');
    }
}

function resetRequestCounter() {
    if (confirm('Günlük istek sayacı sıfırlansın mı?')) {
        // Bu işlem cache temizleme ile yapılabilir
        alert('Terminal\'de çalıştırın: php artisan api:clear-cache');
    }
}
</script>
@endpush 