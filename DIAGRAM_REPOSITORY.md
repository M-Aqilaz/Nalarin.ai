# Diagram Pelajarin.ai dengan Bahasa Sederhana

Dokumen ini menjelaskan alur aplikasi Pelajarin.ai berdasarkan isi repository. Bahasa yang dipakai sengaja dibuat sederhana agar mudah dipahami oleh pengguna, dosen, reviewer, atau tim non-teknis.

## 1. Gambaran Singkat Aplikasi

Pelajarin.ai adalah aplikasi belajar yang membantu pengguna mengubah materi menjadi ringkasan, chat tutor AI, flashcard, kuis, sesi fokus, ruang belajar bersama, dan pencarian teman belajar.

Secara umum, aplikasi ini punya beberapa bagian:

| Bagian | Penjelasan Sederhana |
| --- | --- |
| Halaman umum | Halaman awal, harga paket, daftar akun, dan masuk akun |
| Area pengguna | Tempat pengguna belajar, upload materi, chat AI, kuis, flashcard, dan fokus belajar |
| Ruang belajar | Tempat pengguna membuat atau ikut room belajar bersama |
| Cari teman belajar | Tempat pengguna mencari partner belajar berdasarkan topik |
| Notifikasi | Tempat pengguna melihat kabar terbaru dari chat, room, atau AI |
| Area admin | Tempat admin melihat statistik, memantau AI, mengelola user, dan dokumen |
| Bantuan AI | Bagian yang membantu membuat ringkasan, jawaban chat, flashcard, dan kuis |
| Chat langsung | Bagian yang membuat pesan dan status mengetik muncul secara real-time |

## 2. Struktur Folder Repository

```text
app/                       Isi utama aplikasi
  Http/Controllers/         Pengatur alur saat user membuka atau mengirim data
  Models/                   Bentuk data yang disimpan aplikasi
  Services/                 Bantuan proses seperti baca file, AI, dan cari partner
  Jobs/                     Tugas yang dikerjakan di belakang layar
  Events/                   Pengirim kabar real-time
  Notifications/            Pengirim notifikasi
  Support/                  Bantuan kecil seperti batas pemakaian dan format pesan
database/                  Tabel database, data contoh, dan factory test
resources/views/           Tampilan halaman
resources/js/              Interaksi halaman, timer, planner, dan chat real-time
resources/css/             Tampilan visual aplikasi
routes/                    Daftar alamat halaman dan aksi aplikasi
config/                    Pengaturan aplikasi
tests/                     Pengujian fitur
public/                    File yang langsung dibuka browser
```

## 3. Use Case Diagram

Diagram ini menunjukkan siapa saja yang memakai aplikasi dan hal apa yang bisa mereka lakukan.

### Aktor

| Aktor | Arti |
| --- | --- |
| Pengunjung | Orang yang belum masuk akun |
| Pengguna | Orang yang sudah masuk akun |
| Pengguna Premium | Pengguna yang punya paket lebih tinggi |
| Admin | Pengelola aplikasi |
| AI | Layanan pintar yang membantu membuat isi belajar |
| Pembaca File | Alat yang membantu membaca isi dokumen atau gambar |
| Sistem Chat Langsung | Sistem yang membuat pesan muncul tanpa refresh halaman |

```mermaid
flowchart LR
    Pengunjung[Pengunjung]
    Pengguna[Pengguna]
    Premium[Pengguna Premium]
    Admin[Admin]
    AI[AI]
    PembacaFile[Pembaca File]
    ChatLangsung[Sistem Chat Langsung]

    subgraph Umum[Hal yang bisa dilakukan pengunjung]
        U1[Melihat halaman awal]
        U2[Melihat harga paket]
        U3[Membuat akun]
        U4[Masuk akun]
        U5[Masuk dengan Google atau Discord]
        U6[Lupa password]
    end

    subgraph Belajar[Hal yang bisa dilakukan pengguna]
        B1[Melihat dashboard]
        B2[Mengunggah materi]
        B3[Melihat materi yang sudah diunggah]
        B4[Membaca ringkasan]
        B5[Membuat ruang chat dengan tutor AI]
        B6[Bertanya ke tutor AI]
        B7[Membuat flashcard]
        B8[Mengulang flashcard]
        B9[Membuat kuis]
        B10[Mengerjakan kuis]
        B11[Menggunakan timer fokus]
        B12[Membuat rencana belajar]
        B13[Melihat perkembangan fokus]
        B14[Membuat room belajar]
        B15[Masuk atau keluar room belajar]
        B16[Chat di room belajar]
        B17[Mengatur profil cari teman belajar]
        B18[Mencari partner belajar]
        B19[Mencari partner acak]
        B20[Chat dengan partner belajar]
        B21[Blokir atau laporkan partner]
        B22[Mengubah profil akun]
        B23[Membaca notifikasi]
    end

    subgraph PaketPremium[Tambahan untuk pengguna premium]
        P1[Membaca lebih banyak halaman dokumen]
        P2[Menggunakan AI dengan batas lebih besar]
        P3[Mencari partner tanpa cepat kehabisan kuota]
    end

    subgraph Pengelolaan[Hal yang bisa dilakukan admin]
        A1[Melihat dashboard admin]
        A2[Memantau penggunaan AI]
        A3[Melihat statistik belajar]
        A4[Menonaktifkan atau mengaktifkan user]
        A5[Mengelola dokumen pengguna]
    end

    Pengunjung --> U1
    Pengunjung --> U2
    Pengunjung --> U3
    Pengunjung --> U4
    Pengunjung --> U5
    Pengunjung --> U6

    Pengguna --> B1
    Pengguna --> B2
    Pengguna --> B3
    Pengguna --> B4
    Pengguna --> B5
    Pengguna --> B6
    Pengguna --> B7
    Pengguna --> B8
    Pengguna --> B9
    Pengguna --> B10
    Pengguna --> B11
    Pengguna --> B12
    Pengguna --> B13
    Pengguna --> B14
    Pengguna --> B15
    Pengguna --> B16
    Pengguna --> B17
    Pengguna --> B18
    Pengguna --> B19
    Pengguna --> B20
    Pengguna --> B21
    Pengguna --> B22
    Pengguna --> B23

    Premium --> Pengguna
    Premium --> P1
    Premium --> P2
    Premium --> P3

    Admin --> Pengguna
    Admin --> A1
    Admin --> A2
    Admin --> A3
    Admin --> A4
    Admin --> A5

    B2 -. dibaca oleh .-> PembacaFile
    B2 -. diringkas oleh .-> AI
    B6 -. dijawab oleh .-> AI
    B7 -. bisa dibantu oleh .-> AI
    B9 -. bisa dibantu oleh .-> AI
    B6 -. pesan muncul lewat .-> ChatLangsung
    B16 -. pesan muncul lewat .-> ChatLangsung
    B20 -. pesan muncul lewat .-> ChatLangsung
```

## 4. Activity Diagram

Activity diagram menjelaskan urutan kejadian saat pengguna memakai fitur utama.

### 4.1 Mengunggah Materi dan Mendapat Ringkasan

```mermaid
flowchart TD
    A[Pengguna membuka halaman upload materi] --> B[Pengguna mengisi judul]
    B --> C[Pengguna memilih file atau menempel teks]
    C --> D{Data sudah lengkap?}
    D -- belum --> E[Tampilkan pesan bahwa data perlu dilengkapi]
    D -- sudah --> F{Pengguna mengunggah file?}
    F -- ya --> G[Aplikasi mencoba membaca isi file]
    G --> H{Isi file berhasil dibaca?}
    H -- belum --> I[Aplikasi mencoba membaca dengan bantuan pembaca gambar atau PDF]
    H -- berhasil --> J[Aplikasi merapikan teks]
    I --> J
    F -- tidak --> K[Aplikasi memakai teks yang ditempel pengguna]
    J --> L{Teks materi tersedia?}
    K --> L
    L -- tidak --> E
    L -- ya --> M[Aplikasi menyimpan materi]
    M --> N[AI mencoba membuat ringkasan]
    N --> O{Ringkasan AI berhasil dibuat?}
    O -- ya --> P[Aplikasi menyimpan ringkasan dari AI]
    O -- tidak --> Q[Aplikasi membuat ringkasan singkat sederhana]
    P --> R[Pengguna diarahkan ke halaman ringkasan]
    Q --> R
```

### 4.2 Bertanya ke Tutor AI

```mermaid
flowchart TD
    A[Pengguna membuka fitur chat tutor AI] --> B[Pengguna membuat chat baru atau membuka chat lama]
    B --> C[Pengguna menulis pertanyaan]
    C --> D{Pertanyaan boleh dikirim?}
    D -- tidak --> E[Tampilkan pesan batas penggunaan]
    D -- ya --> F[Aplikasi menyimpan pertanyaan pengguna]
    F --> G[Status AI berubah menjadi sedang menunggu]
    G --> H[Halaman chat mendapat kabar status terbaru]
    H --> I[AI mulai menyiapkan jawaban]
    I --> J{AI berhasil menjawab?}
    J -- ya --> K[Aplikasi menyimpan jawaban AI]
    K --> L[Jawaban muncul di chat]
    L --> M[Pengguna mendapat notifikasi]
    J -- tidak --> N[Status berubah menjadi gagal]
    N --> O[Pengguna melihat pesan bahwa AI belum bisa menjawab]
```

### 4.3 Membuat dan Mengulang Flashcard

```mermaid
flowchart TD
    A[Pengguna membuka fitur flashcard] --> B[Pengguna memilih materi]
    B --> C{Sudah ada flashcard?}
    C -- ada --> D[Tampilkan flashcard yang bisa dipelajari]
    C -- belum ada --> E[Pengguna menekan tombol buat flashcard]
    E --> F{Masih boleh membuat flashcard?}
    F -- tidak --> G[Tampilkan pesan batas penggunaan]
    F -- ya --> H[Aplikasi membuat daftar flashcard dari materi]
    H --> I{Flashcard cukup banyak?}
    I -- tidak --> J[Tampilkan pesan bahwa materi kurang lengkap]
    I -- ya --> K[Aplikasi menyimpan flashcard]
    K --> D
    D --> L[Pengguna membuka satu kartu]
    L --> M[Pengguna menilai apakah kartu mudah atau sulit]
    M --> N[Aplikasi mengatur kapan kartu itu muncul lagi]
    N --> D
```

### 4.4 Membuat dan Mengerjakan Kuis

```mermaid
flowchart TD
    A[Pengguna membuka fitur kuis] --> B[Pengguna memilih materi]
    B --> C{Sudah ada kuis?}
    C -- belum ada --> D[Pengguna menekan tombol buat kuis]
    D --> E{Masih boleh membuat kuis?}
    E -- tidak --> F[Tampilkan pesan batas penggunaan]
    E -- ya --> G[Aplikasi membuat soal dari materi]
    G --> H{Soal cukup banyak?}
    H -- tidak --> I[Tampilkan pesan bahwa materi kurang lengkap]
    H -- ya --> J[Aplikasi menyimpan kuis]
    C -- ada --> K[Pengguna mulai mengerjakan kuis]
    J --> K
    K --> L[Aplikasi menampilkan satu soal]
    L --> M[Pengguna memilih jawaban]
    M --> N{Masih ada soal berikutnya?}
    N -- ya --> L
    N -- tidak --> O[Aplikasi menghitung nilai]
    O --> P[Pengguna melihat hasil dan pembahasan]
```

### 4.5 Room Belajar Bersama

```mermaid
flowchart TD
    A[Pengguna membuka daftar room belajar] --> B[Aplikasi menampilkan room yang tersedia]
    B --> C{Pengguna ingin membuat room?}
    C -- ya --> D[Pengguna mengisi nama, topik, deskripsi, dan batas anggota]
    D --> E[Aplikasi membuat room baru]
    E --> F[Pengguna otomatis menjadi pemilik room]
    F --> G[Pengguna masuk ke halaman room]
    C -- tidak --> H{Pengguna ingin masuk room?}
    H -- ya --> I{Room masih aktif dan belum penuh?}
    I -- tidak --> J[Tampilkan pesan room tidak bisa dimasuki]
    I -- ya --> K[Aplikasi memasukkan pengguna sebagai anggota]
    K --> G
    H -- tidak --> B
    G --> L[Pengguna mengirim pesan]
    L --> M[Pesan disimpan]
    M --> N[Pesan langsung muncul untuk anggota lain]
    G --> O[Pengguna sedang mengetik]
    O --> P[Anggota lain melihat tanda sedang mengetik]
```

### 4.6 Mencari Teman Belajar

```mermaid
flowchart TD
    A[Pengguna membuka fitur cari teman belajar] --> B[Pengguna melengkapi profil belajar]
    B --> C[Pengguna memilih topik atau mulai pencarian acak]
    C --> D{Profil pencarian aktif?}
    D -- tidak --> E[Tampilkan pesan agar profil diaktifkan]
    D -- ya --> F{Kuota pencarian masih ada?}
    F -- tidak --> G[Tampilkan pesan kuota habis]
    F -- ya --> H[Aplikasi mencari pengguna lain yang cocok]
    H --> I{Partner ditemukan?}
    I -- ya --> J[Aplikasi membuat sesi belajar berdua]
    J --> K[Pengguna masuk ke halaman chat partner]
    I -- tidak --> L[Pengguna masuk daftar tunggu]
    L --> M[Menunggu sampai ada partner yang cocok]
    K --> N[Pengguna dan partner saling mengirim pesan]
    N --> O{Ada masalah dengan partner?}
    O -- tidak --> P[Sesi belajar berlanjut]
    O -- laporkan --> Q[Aplikasi mengirim laporan ke admin]
    O -- blokir --> R[Aplikasi menutup sesi dan memblokir partner]
    O -- selesai --> S[Aplikasi menutup sesi belajar]
```

### 4.7 Timer Fokus dan Rencana Belajar

```mermaid
flowchart TD
    A[Pengguna membuka fitur fokus] --> B{Memilih fitur}
    B -- Timer fokus --> C[Pengguna memilih durasi belajar atau istirahat]
    C --> D[Pengguna mulai, jeda, lanjut, atau reset timer]
    D --> E[Aplikasi menyimpan progres di browser]
    B -- Rencana belajar --> F[Pengguna menambah tugas dan blok waktu belajar]
    F --> G[Pengguna menandai tugas yang selesai]
    G --> H[Aplikasi menyimpan rencana di browser]
    B -- Insight fokus --> I[Aplikasi membaca data timer dan rencana]
    I --> J[Aplikasi menampilkan progres dan saran belajar]
```

## 5. Class Diagram Data Aplikasi

Diagram ini menunjukkan data apa saja yang disimpan aplikasi dan hubungannya. Nama di dalam diagram dibuat mudah dibaca, bukan mengikuti nama file program secara mentah.

```mermaid
classDiagram
direction LR

class Pengguna {
  nama
  email
  peran
  paket
  status_aktif
  jumlah_room_yang_boleh_dibuat
  sisa_kuota_cari_partner
}

class MateriBelajar {
  judul
  nama_file
  isi_teks
  status_bacaan
  catatan_pembacaan_file
}

class Ringkasan {
  judul
  isi_ringkasan
  dibuat_dengan
}

class RuangChatAI {
  judul_chat
  status_jawaban_AI
  pesan_error_jika_ada
}

class PesanChatAI {
  pengirim
  isi_pesan
}

class KumpulanFlashcard {
  judul
  deskripsi
  jumlah_kartu
}

class KartuFlashcard {
  bagian_depan
  bagian_belakang
  contoh
  tingkat_kesulitan
  jadwal_muncul_lagi
}

class KumpulanKuis {
  judul
  deskripsi
  jumlah_soal
}

class SoalKuis {
  pertanyaan
  pilihan_jawaban
  jawaban_benar
  pembahasan
}

class ProfilBelajar {
  jenjang
  pelajaran_utama
  tujuan_belajar
  gaya_belajar
  bio
  waktu_tersedia
  boleh_dicari_partner
}

class RoomBelajar {
  nama_room
  topik
  deskripsi
  jenis_room
  batas_anggota
  status_aktif
}

class AnggotaRoom {
  peran_di_room
  status_keanggotaan
  waktu_bergabung
}

class PesanRoom {
  isi_pesan
  jenis_pesan
}

class AntreanPartner {
  topik_dicari
  level_yang_diinginkan
  jenis_sesi
  status_antrean
  batas_waktu_tunggu
}

class SesiPartner {
  topik
  status_sesi
  waktu_dipasangkan
}

class PesanPartner {
  isi_pesan
}

class BlokirPengguna {
  pengguna_yang_diblokir
}

class LaporanPengguna {
  alasan_laporan
  status_laporan
}

class PemakaianFitur {
  nama_fitur
  jumlah_diklik
}

Pengguna "1" --> "*" MateriBelajar : punya
Pengguna "1" --> "*" Ringkasan : punya
Pengguna "1" --> "*" RuangChatAI : punya
Pengguna "1" --> "0..1" ProfilBelajar : punya
Pengguna "1" --> "*" RoomBelajar : membuat
Pengguna "1" --> "*" AnggotaRoom : ikut
Pengguna "1" --> "*" AntreanPartner : menunggu
Pengguna "1" --> "*" BlokirPengguna : memblokir
Pengguna "1" --> "*" LaporanPengguna : melaporkan

MateriBelajar "1" --> "*" Ringkasan : menghasilkan
MateriBelajar "1" --> "*" RuangChatAI : dibahas_di
MateriBelajar "1" --> "0..1" KumpulanFlashcard : dibuat_menjadi
MateriBelajar "1" --> "0..1" KumpulanKuis : dibuat_menjadi

RuangChatAI "1" --> "*" PesanChatAI : berisi
KumpulanFlashcard "1" --> "*" KartuFlashcard : berisi
KumpulanKuis "1" --> "*" SoalKuis : berisi

RoomBelajar "1" --> "*" AnggotaRoom : memiliki
RoomBelajar "1" --> "*" PesanRoom : berisi

SesiPartner "1" --> "*" PesanPartner : berisi
PesanPartner "*" --> "1" Pengguna : dikirim_oleh
PesanRoom "*" --> "1" Pengguna : dikirim_oleh
AnggotaRoom "*" --> "1" Pengguna : adalah
```

## 6. Diagram Bagian Dalam Aplikasi

Diagram ini menjelaskan bagian aplikasi yang bekerja di balik layar dengan istilah yang lebih mudah dimengerti.

```mermaid
flowchart TD
    UserAction[Aksi pengguna di halaman] --> PengaturHalaman[Pengatur halaman]
    PengaturHalaman --> DataAplikasi[Data aplikasi]
    PengaturHalaman --> BantuanFile[Bantuan membaca file]
    PengaturHalaman --> BantuanAI[Bantuan AI]
    PengaturHalaman --> BatasPakai[Pengecek batas pemakaian]
    PengaturHalaman --> ChatLangsung[Pengirim kabar chat langsung]
    PengaturHalaman --> Notifikasi[Pengirim notifikasi]

    BantuanFile --> Materi[Materi belajar]
    BantuanAI --> Ringkasan[Ringkasan]
    BantuanAI --> JawabanAI[Jawaban tutor AI]
    BantuanAI --> Flashcard[Flashcard]
    BantuanAI --> Kuis[Kuis]

    DataAplikasi --> Materi
    DataAplikasi --> Ringkasan
    DataAplikasi --> RoomBelajar[Room belajar]
    DataAplikasi --> PartnerBelajar[Partner belajar]

    ChatLangsung --> ChatAI[Chat AI]
    ChatLangsung --> ChatRoom[Chat room]
    ChatLangsung --> ChatPartner[Chat partner]

    Notifikasi --> InfoUser[Info untuk pengguna]
```

## 7. Alur Chat Langsung

| Tempat Chat | Siapa yang boleh melihat | Untuk apa |
| --- | --- | --- |
| Chat AI | Pemilik chat tersebut | Melihat pertanyaan dan jawaban tutor AI |
| Room belajar | Anggota room, atau semua user jika room publik | Chat bersama di room |
| Chat partner | Dua pengguna yang sedang dipasangkan | Belajar berdua |
| Notifikasi pribadi | Pemilik akun | Menerima kabar terbaru |

Kabar yang bisa muncul langsung:

| Kabar | Arti |
| --- | --- |
| Pesan tutor AI masuk | AI sudah menjawab pertanyaan |
| Status AI berubah | AI sedang menunggu, memproses, selesai, atau gagal |
| Pesan room masuk | Ada pesan baru di room belajar |
| Ada yang sedang mengetik di room | Anggota room sedang mengetik |
| Pesan partner masuk | Partner belajar mengirim pesan |
| Partner sedang mengetik | Partner sedang menulis pesan |

## 8. Data yang Disimpan Aplikasi

| Data | Isi Sederhana |
| --- | --- |
| Pengguna | Nama, email, peran, paket, status akun, dan batas penggunaan |
| Materi belajar | Judul, file, isi teks, status hasil baca file |
| Ringkasan | Hasil ringkasan dari materi |
| Chat AI | Ruang percakapan antara pengguna dan tutor AI |
| Pesan chat AI | Pertanyaan pengguna dan jawaban AI |
| Flashcard | Kartu belajar dari materi |
| Kuis | Soal pilihan ganda dari materi |
| Profil belajar | Minat, tujuan, gaya belajar, dan ketersediaan pengguna |
| Room belajar | Ruang belajar bersama berdasarkan topik |
| Anggota room | Daftar user yang ikut room |
| Pesan room | Isi chat di room |
| Antrean partner | Daftar user yang sedang mencari teman belajar |
| Sesi partner | Pasangan belajar yang sudah ditemukan |
| Pesan partner | Isi chat antar partner |
| Blokir pengguna | Daftar user yang diblokir |
| Laporan pengguna | Laporan masalah ke admin |
| Notifikasi | Kabar terbaru untuk user |
| Pemakaian fitur | Jumlah klik fitur tertentu |

## 9. Halaman yang Ada di Aplikasi

| Halaman | Fungsi |
| --- | --- |
| Halaman awal | Mengenalkan Pelajarin.ai |
| Harga paket | Menampilkan pilihan paket |
| Login dan register | Masuk dan membuat akun |
| Dashboard | Ringkasan aktivitas belajar pengguna |
| Upload materi | Menambahkan materi baru |
| Daftar materi | Melihat materi yang pernah diunggah |
| Ringkasan | Membaca ringkasan materi |
| Chat AI | Bertanya ke tutor AI |
| Flashcard | Mengulang materi dengan kartu belajar |
| Kuis | Latihan soal dari materi |
| Pomodoro | Timer fokus belajar |
| Rencana belajar | Menyusun tugas dan blok waktu belajar |
| Insight fokus | Melihat progres fokus |
| Room belajar | Belajar bersama banyak user |
| Cari partner | Mencari teman belajar |
| Notifikasi | Melihat kabar terbaru |
| Profil | Mengubah data akun |
| Admin dashboard | Ringkasan untuk admin |
| Monitoring AI | Melihat penggunaan AI |
| Statistik pembelajaran | Melihat statistik aktivitas belajar |
| Kelola user | Mengaktifkan atau menonaktifkan akun |
| Kelola dokumen | Melihat dan menghapus dokumen pengguna |

## 10. Alamat Fitur Utama

| Alamat | Fungsi |
| --- | --- |
| `/` | Halaman awal |
| `/pricing` | Harga paket |
| `/dashboard` | Dashboard pengguna |
| `/materials` dan `/upload` | Materi belajar |
| `/summary` | Ringkasan |
| `/chat` | Chat tutor AI |
| `/quiz` | Kuis |
| `/flashcards` | Flashcard |
| `/pomodoro` | Timer fokus |
| `/focus-planner` | Rencana belajar |
| `/focus-insights` | Insight fokus |
| `/rooms` | Room belajar |
| `/matchmaking` | Cari partner belajar |
| `/profile` | Profil akun |
| `/notifications` | Notifikasi |
| `/admin/*` | Area admin |

## 11. Catatan Penting

1. Jawaban tutor AI tidak selalu langsung muncul, karena aplikasi menyiapkan jawabannya di belakang layar.
2. Jika AI belum tersedia, beberapa fitur tetap mencoba membuat hasil sederhana dari teks materi.
3. File PDF atau gambar bisa dibaca jika alat pembaca file di server sudah tersedia.
4. Timer fokus dan rencana belajar disimpan di browser pengguna, bukan di database.
5. Chat langsung membutuhkan sistem real-time agar pesan bisa muncul tanpa refresh.
6. Admin punya halaman khusus untuk memantau aplikasi dan mengelola data.

