# ⚽ LiveScore - Canlı Futbol Skoru Sitesi

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap" alt="Bootstrap">
  <img src="https://img.shields.io/badge/API-Football-green?style=for-the-badge" alt="API-Football">
</p>

<p align="center">
  Modern, responsive ve gerçek zamanlı futbol skoru takip sitesi. API-Football entegrasyonu ile dünya çapında 1000+ ligden canlı skorlar, istatistikler ve detaylı maç bilgileri.
</p>

---

## 🌟 Özellikler

### 📱 **Canlı Skorlar**

- ⚡ 30 saniye otomatik güncelleme
- 🔴 Gerçek zamanlı canlı maçlar
- ⏱️ Dakika bazında güncellenen skorlar
- 📊 Anlık maç durumu (İlk yarı, devre arası, bitiş vb.)

### 🏆 **Lig Yönetimi**

- 🌍 1000+ futbol ligi desteği
- ⭐ Popüler ligeler (Premier League, La Liga, Bundesliga, Serie A, Ligue 1)
- 📈 Detaylı puan durumu tabloları
- 🏅 Şampiyonlar Ligi ve Avrupa Ligi takibi

### ⚽ **Maç Detayları**

- 📋 Kapsamlı maç istatistikleri
- ⚽ Gol, kart ve oyuncu değişiklikleri
- 📊 Top hakimiyeti, şut sayısı, korner vb.
- 🏟️ Stadyum bilgileri ve hakem detayları

### 📅 **Maç Takvimi**

- 📆 Tarihe göre maç arama
- ⬅️➡️ Kolay tarih navigasyonu
- 🔍 Lig bazında filtreleme
- 📱 Responsive tasarım

### 🎨 **Modern UI/UX**

- 📱 Mobile-first responsive tasarım
- 🌈 Gradient ve modern CSS efektleri
- ⚡ Hızlı yükleme ve smooth animasyonlar
- 🎯 Kullanıcı dostu arayüz

---

## 🚀 Hızlı Başlangıç

### Gereksinimler

- PHP 8.1 veya üzeri
- Composer
- Laravel 10.x
- RapidAPI hesabı ve API-Football aboneliği

### 📦 Kurulum

1. **Projeyi klonlayın**

```bash
git clone https://github.com/username/live-score-api.git
cd live-score-api
```

2. **Bağımlılıkları yükleyin**

```bash
composer install
```

3. **Environment dosyasını ayarlayın**

```bash
cp .env.example .env
php artisan key:generate
```

4. **API ayarlarını yapın**

```env
# .env dosyasına ekleyin
RAPIDAPI_KEY=your_rapidapi_key_here
```

5. **Sunucuyu başlatın**

```bash
php artisan serve
```

6. **Tarayıcıda açın**

```
http://localhost:8000
```

---

## 🔧 API Konfigürasyonu

### RapidAPI Key Alma

1. [RapidAPI](https://rapidapi.com) hesabı oluşturun
2. [API-Football](https://rapidapi.com/api-sports/api/api-football) sayfasına gidin
3. Bir plan seçin (Free plan mevcut - 100 istek/gün)
4. API key'inizi kopyalayın
5. `.env` dosyasına `RAPIDAPI_KEY` olarak ekleyin

### Desteklenen Plan Türleri

| Plan      | Fiyat | Günlük Limit  | Özellikler       |
| --------- | ----- | ------------- | ---------------- |
| **Free**  | $0    | 100 istek     | Temel özellikler |
| **Pro**   | $19   | 7,500 istek   | Tüm özellikler   |
| **Ultra** | $29   | 75,000 istek  | Yüksek limit     |
| **Mega**  | $39   | 150,000 istek | Maksimum limit   |

---

## 📁 Proje Yapısı

```
live-score-api/
├── app/
│   ├── Http/Controllers/
│   │   ├── HomeController.php          # Ana sayfa
│   │   ├── LiveScoreController.php     # Canlı skorlar
│   │   ├── LeagueController.php        # Lig yönetimi
│   │   └── MatchController.php         # Maç detayları
│   └── Services/
│       └── FootballApiService.php      # API entegrasyonu
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php              # Ana layout
│   ├── components/
│   │   └── match-card.blade.php       # Maç kartı bileşeni
│   ├── home.blade.php                 # Ana sayfa
│   ├── live-scores.blade.php          # Canlı skorlar
│   ├── matches-by-date.blade.php      # Maç takvimi
│   ├── leagues/
│   │   ├── popular.blade.php          # Popüler ligeler
│   │   └── show.blade.php             # Lig detayları
│   └── matches/
│       └── show.blade.php             # Maç detayları
└── routes/
    └── web.php                        # Web rotaları
```

---

## 🎯 Kullanılan Teknolojiler

### Backend

- **Laravel 10.x** - PHP Framework
- **PHP 8.1+** - Backend dili
- **Guzzle HTTP** - API istekleri
- **Laravel Cache** - Performans optimizasyonu

### Frontend

- **Bootstrap 5.3** - CSS Framework
- **Font Awesome 6.4** - İkonlar
- **Vanilla JavaScript** - Dinamik özellikler
- **Blade Templates** - Server-side templating

### API & Servisler

- **API-Football** - Futbol verileri
- **RapidAPI** - API yönetimi
- **HTTP Client** - API entegrasyonu

---

## 🔄 Önemli Fonksiyonlar

### Cache Sistemi

```php
// Otomatik cache ile API çağrıları
private function makeRequestWithCache($endpoint, $params = [], $cacheMinutes = 15)
{
    $cacheKey = 'football_api_' . md5($endpoint . serialize($params));

    return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($endpoint, $params) {
        return $this->makeRequest($endpoint, $params);
    });
}
```

### Real-time Güncelleme

```javascript
// 30 saniye otomatik güncelleme
setInterval(function () {
  if (typeof refreshLiveScores === "function") {
    refreshLiveScores();
  }
}, 30000);
```

### Error Handling

```php
try {
    $response = Http::withHeaders($this->headers)->get($url, $params);
    return $response->json();
} catch (Exception $e) {
    throw new Exception('API Error: ' . $e->getMessage());
}
```

---

## 📊 API Endpoint'leri

### Kullanılan API-Football Endpoints

| Endpoint                    | Açıklama           | Cache Süresi |
| --------------------------- | ------------------ | ------------ |
| `/status`                   | API durumu         | -            |
| `/fixtures?live=all`        | Canlı maçlar       | Realtime     |
| `/fixtures?date=YYYY-MM-DD` | Günlük maçlar      | 10 dakika    |
| `/leagues`                  | Ligeler            | 24 saat      |
| `/standings`                | Puan durumu        | 1 saat       |
| `/fixtures/statistics`      | Maç istatistikleri | 5 dakika     |
| `/fixtures/events`          | Maç olayları       | 5 dakika     |

---

## 🎨 Sayfalar ve Özellikler

### 🏠 **Ana Sayfa** (`/`)

- Canlı maçların özeti
- Bugünün öne çıkan maçları
- Popüler ligeler sidebar'ı
- Hızlı istatistikler

### 📺 **Canlı Skorlar** (`/live-scores`)

- Tüm canlı maçlar
- 30 saniye otomatik güncelleme
- Manuel yenileme butonu
- Son güncelleme zamanı

### 📅 **Maç Takvimi** (`/matches/date`)

- Tarihe göre maç listeleme
- Önceki/sonraki gün navigasyonu
- Hızlı tarih seçimi
- Lig bazında gruplama

### 🏆 **Popüler Ligeler** (`/leagues/popular`)

- Top 5 Avrupa ligi
- Şampiyonlar Ligi
- Türkiye Süper Lig
- Lig detay kartları

### 📊 **Lig Puan Durumu** (`/leagues/{id}`)

- Detaylı puan tablosu
- Şampiyonlar Ligi/Avrupa Ligi pozisyonları
- Küme düşme çizgisi
- Form durumu (son 5 maç)

### ⚽ **Maç Detayları** (`/matches/{id}`)

- Tab'lı arayüz (Genel/Olaylar/İstatistikler)
- Gerçek zamanlı skor
- Maç olayları timeline'ı
- Detaylı istatistik karşılaştırması

---

## 🚀 Performans Optimizasyonları

### Cache Stratejisi

- **Canlı maçlar**: Cache yok (realtime)
- **Günlük maçlar**: 10 dakika cache
- **Ligeler**: 24 saat cache
- **Puan durumu**: 1 saat cache
- **İstatistikler**: 5 dakika cache

### Frontend Optimizasyonu

- **Lazy loading** için görüntü optimizasyonu
- **Minimal JavaScript** kullanımı
- **CSS optimizasyonu** ve minification
- **Responsive images** ve WebP desteği

---

## 🔧 Geliştirici Notları

### Özelleştirme

1. **Yeni lig ekleme:**

```php
// FootballApiService.php içinde
$popularLeagueIds = [
    39,  // Premier League
    203, // Turkish Super Lig
    // Yeni lig ID'sini buraya ekleyin
];
```

2. **Cache sürelerini değiştirme:**

```php
// Her metod için cache süresini ayarlayabilirsiniz
return $this->makeRequestWithCache('/leagues', $params, 60); // 60 dakika
```

3. **Yeni sayfa ekleme:**

```php
// routes/web.php
Route::get('/yeni-sayfa', [YeniController::class, 'index']);
```

### Debug Modu

```bash
# Hata ayıklama için
php artisan config:clear
php artisan cache:clear
```

---

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/YeniOzellik`)
3. Commit yapın (`git commit -am 'Yeni özellik eklendi'`)
4. Branch'i push edin (`git push origin feature/YeniOzellik`)
5. Pull Request oluşturun

---

## 📞 Destek ve İletişim

- 🐛 **Bug Report**: Issues sekmesini kullanın
- 💡 **Feature Request**: Issues ile öneri paylaşın
- 📧 **İletişim**: [email@domain.com](mailto:email@domain.com)
- 📚 **Dokümantasyon**: Bu README dosyası

---

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

---

## 🙏 Teşekkürler

- **API-Football** - Kaliteli futbol verileri için
- **RapidAPI** - API yönetim platformu için
- **Laravel Community** - Mükemmel framework için
- **Bootstrap Team** - Responsive CSS framework için

---

<p align="center">
  <strong>⭐ Bu projeyi beğendiyseniz star vermeyi unutmayın! ⭐</strong>
</p>

---

**Son Güncelleme:** 2025 | **Versiyon:** 1.0.0 | **Laravel:** 10.x
