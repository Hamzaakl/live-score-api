# 🚀 LiveScore - Detaylı Kurulum Rehberi

Bu rehber, LiveScore projesini sıfırdan kurmanız için adım adım talimatlar içerir.

---

## 📋 Gereksinimler

- **PHP 8.1+** (önerilen: 8.2)
- **Composer** (PHP paket yöneticisi)
- **Web Server** (Apache/Nginx) veya Laravel'in built-in server'ı
- **RapidAPI hesabı** (ücretsiz)

---

## 🛠️ Adım Adım Kurulum

### 1. Proje Dosyalarını İndirin

```bash
git clone https://github.com/username/live-score-api.git
cd live-score-api
```

### 2. Composer Bağımlılıklarını Yükleyin

```bash
composer install
```

### 3. Environment Dosyasını Oluşturun

```bash
cp .env.example .env
```

**Eğer .env.example dosyası yoksa, .env dosyasını manuel oluşturun:**

```bash
touch .env
```

### 4. .env Dosyasını Düzenleyin

`.env` dosyasını açın ve şu içeriği ekleyin:

```env
APP_NAME="LiveScore"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=live_score_api
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# API-Football Configuration
RAPIDAPI_KEY=your_rapidapi_key_here
```

### 5. Application Key Oluşturun

```bash
php artisan key:generate
```

### 6. RapidAPI Key Alın

1. **RapidAPI'ye kayıt olun**: [https://rapidapi.com](https://rapidapi.com)
2. **API-Football'a gidin**: [https://rapidapi.com/api-sports/api/api-football](https://rapidapi.com/api-sports/api/api-football)
3. **Plan seçin**:
   - **Free Plan**: 100 istek/gün (Ücretsiz)
   - **Pro Plan**: 7,500 istek/gün ($19/ay)
   - **Ultra Plan**: 75,000 istek/gün ($29/ay)
4. **API Key kopyalayın**
5. **.env dosyasında değiştirin**:
   ```env
   RAPIDAPI_KEY=sizin_api_key_iniz_buraya
   ```

### 7. Sunucuyu Başlatın

```bash
php artisan serve
```

### 8. Tarayıcıda Test Edin

```
http://localhost:8000
```

---

## ✅ Kurulum Doğrulama

### 1. Ana Sayfa Kontrolü

- Ana sayfada popüler ligeler görünüyor mu?
- Bugünün maçları listeleniyor mu?

### 2. Canlı Skorlar Kontrolü

- `/live-scores` sayfasına gidin
- Canlı maçlar (varsa) görünüyor mu?

### 3. Ligeler Kontrolü

- `/leagues/popular` sayfasına gidin
- Premier League, La Liga gibi ligeler görünüyor mu?

### 4. API Bağlantı Testi

Proje kök dizininde test scripti oluşturun:

```php
<?php
// test_api.php
$apiKey = 'YOUR_API_KEY_HERE';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://v3.football.api-sports.io/status",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: v3.football.api-sports.io",
        "X-RapidAPI-Key: " . $apiKey
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
?>
```

Çalıştırın:

```bash
php test_api.php
```

HTTP Code 200 ise başarılı!

---

## 🔧 Sorun Giderme

### Genel Hatalar

**1. "Class not found" Hatası**

```bash
composer dump-autoload
```

**2. "Permission denied" Hatası**

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

**3. Cache Sorunları**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### API Bağlantı Sorunları

**1. "API Error: HTTP 401"**

- API key'iniz yanlış
- RapidAPI dashboard'unuzdan doğru key'i kopyalayın

**2. "API Error: HTTP 403"**

- Günlük limit aşıldı
- Plan yükseltin veya yarın deneyin

**3. "API Error: HTTP 429"**

- Çok fazla istek gönderdiniz
- Bir dakika bekleyip tekrar deneyin

**4. Ligeler Yüklenmiyor**

- Header konfigürasyonunu kontrol edin
- `X-RapidAPI-Host` doğru mu?

### Performans Sorunları

**1. Yavaş Yükleme**

- Cache sürelerini artırın
- Free plan kullanıyorsanız istekleri sınırlayın

**2. Memory Sorunları**

```ini
# php.ini
memory_limit = 256M
max_execution_time = 60
```

---

## 🚀 Prodüksiyon Kurulumu

### 1. Environment Ayarları

```env
APP_ENV=production
APP_DEBUG=false
```

### 2. Cache Optimizasyonu

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Web Server Konfigürasyonu

**Apache (.htaccess):**

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

**Nginx:**

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

## 📈 Performans İpuçları

### 1. Cache Stratejisi

- **Canlı maçlar**: Cache kullanmayın
- **Günlük maçlar**: 10-15 dakika cache
- **Ligeler**: 24 saat cache
- **İstatistikler**: 5 dakika cache

### 2. İstek Limitlerini Yönetin

```php
// Free plan için günlük limiti kontrol edin
// 100 istek/gün = saatte ~4 istek
```

### 3. Error Handling

- Tüm API çağrılarında try-catch kullanın
- Fallback içerik hazırlayın
- Kullanıcı dostu hata mesajları gösterin

---

## 📞 Yardım Lazım?

### Sık Sorulan Sorular

**S: Free plan ile kaç kullanıcı destekler?**
C: Cache ile ~50-100 günlük ziyaretçi

**S: Hangi ligler desteklenir?**
C: 1000+ lig, tüm büyük Avrupa ligleri dahil

**S: Mobile uyumlu mu?**
C: Evet, responsive tasarım ile tüm cihazlarda çalışır

### İletişim

- 🐛 **Bug**: GitHub Issues kullanın
- 💡 **Öneriler**: Feature request açın
- 📧 **Destek**: README dosyasındaki iletişim bilgileri

---

## ✅ Kurulum Tamamlandı!

Tebrikler! LiveScore siteniz artık hazır.

**Sonraki Adımlar:**

1. ⭐ GitHub'da star verin
2. 🔧 Kendi özelleştirmelerinizi yapın
3. 📱 Mobile cihazlarda test edin
4. 🚀 Prodüksiyona deploy edin

---

**Son Güncelleme:** 2025 | **Proje:** LiveScore v1.0
