# 📰 Rekap Media Online - Automasi Laporan Lapas

Aplikasi berbasis web untuk mengotomatisasi pencatatan dan rekapitulasi tautan berita dari berbagai media online. Dibangun untuk menggantikan proses manual pengisian data ke Microsoft Excel, aplikasi ini secara otomatis mengekstrak nama media dan tanggal publikasi langsung dari URL berita.

## ✨ Fitur Utama

- **Bulk Link Processing:** Input puluhan tautan berita sekaligus dalam satu kali proses (*copy-paste* langsung).
- **Auto-Scraping:** Otomatis mendeteksi dan mengekstrak *Nama Media* (dari domain) dan *Tanggal Publikasi* (dari meta tag HTML).
- **Smart Publisher Management:** Otomatis membuat master data media baru jika belum terdaftar di *database*.
- **Excel-like Dashboard:** Menampilkan rekapitulasi data bulanan dalam bentuk tabel grid (kolom tanggal 1-31) yang persis dengan format laporan Excel standar.

## 🚀 Teknologi yang Digunakan

- **Framework:** [Laravel 10.x / 11.x](https://laravel.com/)
- **Frontend/Reactivity:** [Livewire 3](https://livewire.laravel.com/) + [Volt](https://livewire.laravel.com/docs/volt)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/)
- **Database:** MySQL / SQLite

## 🛠️ Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di mesin lokal:

1. **Clone repositori ini:**
   ```
   git clone repo
   cd repo-rekap-media

2. **Install dependensi PHP dan Node.js:**
    ```
    composer install
    npm install && npm run build


3. **Setup Environment:**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi *database* kamu.
    ```
    cp .env.example .env
    php artisan key:generate

4. **Jalankan Migrasi Database:**
    ```
    php artisan migrate
    

5. **Jalankan Server Lokal:**
    ```
    php artisan serve

Aplikasi dapat diakses melalui `http://localhost:8000`.

## 💡 Cara Penggunaan

1. Buka halaman utama *dashboard*.
2. Pada kolom **Input Link Berita Harian**, *paste* daftar URL berita yang diberikan. Pastikan setiap URL berada di baris baru (*enter*).
3. Klik tombol **Proses Link Terlampir**.
4. Sistem akan memproses data di latar belakang dan tabel rekapitulasi di bawahnya akan otomatis diperbarui sesuai dengan tanggal publikasi masing-masing berita.

---

**Crafted by Ngopikode** *Ngopi Santai, Ngoding Serius, Bikin Solusi.*
