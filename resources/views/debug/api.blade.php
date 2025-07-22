@extends('layouts.app')

@section('title', 'API Debug - LiveScore')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 text-gradient mb-2">🔍 API Debug Panel</h1>
                    <p class="text-muted">API bağlantı durumu ve endpoint testleri</p>
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

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-plug"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">API Status</h6>
                                    <span class="badge bg-{{ $results[0]['status'] == 'success' ? 'success' : 'danger' }}">
                                        {{ $results[0]['status'] == 'success' ? 'Aktif' : 'Hatalı' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Başarılı Testler</h6>
                                    <h4 class="mb-0 text-success">{{ collect($results)->where('status', 'success')->count() }}/{{ count($results) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Ortalama Süre</h6>
                                    <h4 class="mb-0 text-warning">
                                        {{ number_format(collect($results)->where('status', 'success')->avg(function($r) { return (float)str_replace('ms', '', $r['duration']); }), 0) }}ms
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Bugünkü İstekler</h6>
                                    <h4 class="mb-0 text-info" id="daily-requests">...</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-vial me-2"></i>Test Sonuçları</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($results as $index => $result)
                    <div class="border-bottom">
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle me-3 {{ $result['status'] == 'success' ? 'bg-success' : 'bg-danger' }} text-white">
                                        <i class="fas fa-{{ $result['status'] == 'success' ? 'check' : 'times' }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $result['name'] }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $result['duration'] }}
                                            @if($result['result_count'] > 0)
                                                <i class="fas fa-list ms-3 me-1"></i>{{ $result['result_count'] }} sonuç
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                @if($result['data'])
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#result-{{ $index }}" aria-expanded="false">
                                    <i class="fas fa-eye"></i> Detay
                                </button>
                                @endif
                            </div>

                            @if($result['error'])
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Hata:</strong> {{ $result['error'] }}
                            </div>
                            @endif

                            @if($result['data'])
                            <div class="collapse" id="result-{{ $index }}">
                                <div class="bg-light rounded p-3 mt-3">
                                    <h6 class="mb-2">JSON Response:</h6>
                                    <pre class="mb-0 small" style="max-height: 400px; overflow-y: auto;"><code>{{ $result['data'] }}</code></pre>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Hızlı İşlemler</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="window.open('/debug/api-key', '_blank')">
                                    <i class="fas fa-key me-2"></i>API Key Kontrol Et
                                </button>
                                <button class="btn btn-outline-info" onclick="window.open('/debug/cache', '_blank')">
                                    <i class="fas fa-database me-2"></i>Cache Durumu
                                </button>
                                <button class="btn btn-outline-warning" onclick="clearApiCache()">
                                    <i class="fas fa-broom me-2"></i>API Cache Temizle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Yardımcı Bilgiler</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-circle text-success me-2" style="font-size: 0.5rem;"></i>
                                    <small>Tüm testler başarılı: API çalışıyor</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-circle text-warning me-2" style="font-size: 0.5rem;"></i>
                                    <small>Bazı testler başarısız: Rate limit kontrolü</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-circle text-danger me-2" style="font-size: 0.5rem;"></i>
                                    <small>Çoğu test başarısız: API key veya plan kontrol</small>
                                </li>
                            </ul>
                        </div>
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

pre code {
    color: #666;
    font-size: 0.85rem;
}

.alert {
    border: none;
    border-radius: 8px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Günlük istek sayısını yükle
    fetch('/debug/cache')
        .then(response => response.json())
        .then(data => {
            document.getElementById('daily-requests').textContent = data.today_requests + '/100';
        })
        .catch(e => {
            document.getElementById('daily-requests').textContent = 'Error';
        });
});

function clearCache() {
    if (confirm('Tüm cache temizlensin mi?')) {
        fetch('/api/cache/clear', {method: 'POST'})
            .then(() => {
                alert('Cache temizlendi!');
                location.reload();
            })
            .catch(e => {
                alert('Cache temizlenirken hata oluştu!');
            });
    }
}

function clearApiCache() {
    if (confirm('API cache temizlensin mi? Bu işlem yeni API istekleri göndermenizi sağlar.')) {
        // Laravel Artisan command çalıştırılamaz, kullanıcıya talimat göster
        alert('Terminal\'de şu komutu çalıştırın:\n\nphp artisan api:clear-cache\n\nVeya\n\nphp artisan cache:clear');
    }
}
</script>
@endpush 