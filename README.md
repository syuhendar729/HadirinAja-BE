# Backend Aplikasi HadirinAja (Laravel 13)

Cara menjalankan:
- Pastikan PHP, Laravel, dan MySQL sudah terinstall
- Copy `.env.example` menjadi `.env`
- Ubah config database sesuai yang sedang berjalan
```
# Contoh:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_hadirinaja
DB_USERNAME=user
DB_PASSWORD=xxx
```
- Jalankan:
```
composer install

# (kalau gada error lanjut)
php artisan migrate

# (kalau gada error lanjut)
composer run dev
```



Seluruh API Dokumentasi ada di repository berikut:
https://github.com/syuhendar729/HadirinAja-API-Docs/