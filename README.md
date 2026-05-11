# 🚀 Postify - Modern Web 2.0 İçerik Yönetim Sistemi (CMS)

Postify, klasik ve hantal blog yapılarının aksine; asenkron veri akışına sahip, güvenli ve kullanıcı deneyimi (UX) odaklı modern bir İçerik Yönetim Sistemi prototipidir. Yazılım Mühendisliği prensipleri, Clean Code standartları ve RESTful API benzeri bir mimari gözetilerek geliştirilmiştir.

## 🌟 Öne Çıkan Mühendislik Özellikleri

* **Asenkron Etkileşim (Fetch API):** Kullanıcıların yazıları beğenme, kaydetme ve yorum yapma işlemleri sayfa yenilenmeden (No-Reload) arka planda gerçekleşir.
* **Sürüm Kontrol Sistemi (Version History):** Tıpkı GitHub gibi, güncellenen hikayelerin eski sürümleri veritabanında (`post_versions`) saklanır. 'Diff' algoritması ile canlı yazı ve eski sürüm arasındaki metin farkları renkli olarak arayüzde gösterilir.
* **Double Submit Koruması (Anti-Spam):** Form gönderimlerinde kullanıcı art arda tıklayıp veritabanını spamlamasın diye butonlar anında kilitlenir (disabled) ve Spinner animasyonu ile görsel geri bildirim sağlanır. İşlem sonucuna göre DOM Restorasyonu yapılır.
* **İstemci Tarafı Filtreleme (Client-Side Filtering):** API'den çekilen toplu veriler, sunucuya ekstra yük bindirmemek için JavaScript'in `.filter()` metoduyla doğrudan istemci tarafında süzülerek arayüze basılır.
* **Defensive Programming:** JavaScript tarafında Null Pointer hatalarını önlemek için DOM manipülasyonlarından önce katı boşluk kontrolleri (Null Checks) uygulanmıştır.

## 🛡️ Güvenlik Mimarisi (Security)

Sistem dışarıdan gelebilecek saldırılara karşı uygulama katmanında korunmaktadır:
* **SQL Injection Koruması:** Veritabanı sorguları PDO (PHP Data Objects) ve Prepared Statements kullanılarak izole edilmiştir.
* **Veri Sanitizasyonu:** URL üzerinden gelen GET parametreleri `intval()` ile zorla Integer tipine dönüştürülerek (Type Casting) zararlı payload'lar engellenmiştir.
* **Validation & Hata Yönetimi:** Eksik veya hatalı API isteklerinde sistemin çökmesi (HTTP 500) engellenmiş, bunun yerine JSON formatında kontrollü hata mesajları döndürülmüştür.

## 💻 Kullanılan Teknolojiler (Tech Stack)

**Frontend (Önyüz):**
* HTML5 & CSS3
* Bootstrap 5 (Flexbox & Fluid Layout Mimarısi)
* Vanilla JavaScript (ES6, Fetch API, DOM Manipulation)
* Diff.js (Sürüm karşılaştırma algoritması için)

**Backend (Arka Plan):**
* PHP 8+ (API Uç Noktaları ve İş Mantığı)
* RESTful Mimari Yaklaşımı

**Veritabanı:**
* MySQL (Relational Database)
* PDO (PHP Data Objects)

## ⚙️ Kurulum ve Çalıştırma

Projeyi kendi yerel sunucunuzda (Localhost) çalıştırmak için aşağıdaki adımları izleyin:

1. Bu depoyu (repository) klonlayın veya `.zip` olarak indirip XAMPP/MAMP `htdocs` klasörünün içine çıkartın.
2. `phpMyAdmin` arayüzünü açın ve `blogsitesi` adında yeni bir veritabanı oluşturun.
3. Proje ana dizininde bulunan `veritabani_guncel.sql` dosyasını bu veritabanına içe aktarın (Import).
4. `api/baglanti.php` dosyasını açın ve kendi yerel veritabanı bilgilerinizi (kullanıcı adı, şifre) kontrol edin.
5. Tarayıcınızda `http://localhost/blogsitesi` adresine giderek projeyi başlatın.

## 📂 Klasör Yapısı

```text
/blogsitesi
│
├── api/                   # RESTful API uç noktaları (Backend)
│   ├── baglanti.php       # PDO Veritabanı bağlantı konfigürasyonu
│   ├── tek_yazi_getir.php # Detay sayfası için JSON veri sağlayıcı
│   ├── yazi_guncelle.php  # İçerik güncelleme ve versiyonlama API'si
│   └── ...
│
├── css/                   # Özel stil dosyaları
├── js/                    # İstemci tarafı scriptleri
│
├── index.php              # Ana sayfa (Keşfet)
├── detay.php              # Dinamik yazı okuma ve sürüm geçmişi sayfası
├── yazilarim.php          # Kullanıcıya özel içerik yönetim paneli
└── ...
  
  
