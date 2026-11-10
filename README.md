# INFORA-SN — Portal Berita & Manajemen Artikel (MVC Native)

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%2FMariaDB-orange.svg)](https://mariadb.org)

**INFORA-SN** adalah platform portal berita berbasis web yang dibangun menggunakan arsitektur **PHP MVC Native**. Platform ini dilengkapi dengan sistem otentikasi multi-user (Client/Penulis & Admin), pengelolaan artikel, manajemen kategori, tag dinamis, sistem iklan (ads), serta halaman redaksi dan dokumen legal.

---

## 📸 Tampilan Aplikasi

| Halaman Utama Publik | Dashboard Admin |
|---|---|
| ![Home Page](doc/Screenshot%202026-11-10%20161947.png)

---

## ✨ Fitur Utama

- **Publik**:
  - Beranda berita dengan slider/banner iklan dinamis.
  - Pembacaan artikel berdasarkan kategori & tag.
  - Halaman Tim Redaksi & Dokumen Legalitas (AHU Law Firm).
- **Client / Author**:
  - Dashboard pembuatan & manajemen draft artikel.
  - Pengunggah gambar & thumbnail cropper.
- **Admin**:
  - Moderasi artikel (Review, Approve, Revisi, Reject).
  - Manajemen kategori, tag, pengguna (clients), dan iklan (ads).

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP (Custom MVC Framework)
- **Database**: MySQL / MariaDB (Port 3311 / 3306)
- **Frontend**: HTML5, CSS3, Tailwind CSS / Bootstrap, JavaScript
- **Web Server**: Apache (XAMPP)

---

## ⚙️ Cara Instalasi & Pengaturan Lokal

1. **Clone Repositori**
   ```bash
   git clone [https://github.com/Alam0000/infora-sn.git](https://github.com/Alam0000/infora-sn.git)
   cd infora-sn
