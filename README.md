<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🏥 Hyu PACS & MCU Radiology Workstation

Sistem Manajemen Radiologi & Standalone PACS berbasis **Laravel 11**, **Python 3.10 AI (PyTorch/OpenCV)**, dan protokol **DICOM C-STORE / C-FIND / C-ECHO**.

---

## 🚀 Cara Menjalankan Program (Quick Start)

### Metode 1: 1-Click Launcher (Paling Mudah)
Cukup **double-click** file:
👉 **[`run.bat`](file:///d:/laragon/www/belajar-laravel/run.bat)**
> File ini akan otomatis mengatur PHP 8.3 dari Laragon, menyalakan web server di `http://127.0.0.1:8000`, dan membuka browser langsung ke halaman **Start Screen & Health Center**.

---

### Metode 2: Lewat PowerShell / Terminal
Buka PowerShell di folder project:
```powershell
# 1. Atur PATH ke PHP 8.3 Laragon
$env:PATH = "D:\laragon\bin\php\php-8.3.33-Win32-vs16-x64;" + $env:PATH

# 2. Jalankan Server Laravel
php artisan serve
```
Buka browser ke:
* **Start Screen & Health Check**: [http://127.0.0.1:8000/start](http://127.0.0.1:8000/start)
* **Dashboard Pasien & Viewer**: [http://127.0.0.1:8000/scans](http://127.0.0.1:8000/scans)

---

### Metode 3: Lewat Laragon GUI (Apache / Virtual Host)
1. Buka aplikasi **Laragon**.
2. Pastikan versi PHP aktif adalah **PHP 8.3** (*Klik kanan di Laragon > PHP > Version > pilih `php-8.3.33`*).
3. Klik tombol **"Start All"**.
4. Buka browser ke:
   - `http://belajar-laravel.test/start` atau `http://localhost/belajar-laravel/public/start`

---

## 📡 Menjalankan DICOM PACS Server (Port 4242)

Anda **tidak perlu** menjalankan script terminal secara manual.
1. Buka halaman **Start Screen** ([http://127.0.0.1:8000/start](http://127.0.0.1:8000/start)).
2. Pada panel **DICOM PACS Listener**, klik tombol **"Start DICOM Server"**.
3. Klik tombol **"Ping (C-ECHO)"** untuk memverifikasi handshake DICOM.
4. Klik **"Buka Antrian Pasien & Viewer"** untuk masuk ke modul klinis radiologi.

*(Alternatif manual via terminal jika diinginkan: `python hyu_dicom_server.py`)*

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
