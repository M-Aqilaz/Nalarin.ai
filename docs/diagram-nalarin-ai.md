# Diagram Sistem Nalarin.ai

Dokumen ini berisi versi lengkap diagram utama untuk project Nalarin.ai. Format diagram memakai Mermaid agar bisa ditempel ke Markdown viewer, GitHub, atau draw.io yang mendukung Mermaid.

## 1. Use Case Diagram

```mermaid
graph LR
    actor_visitor["Visitor"]
    actor_user["User"]
    actor_admin["Admin"]
    actor_pakasir["Pakasir"]
    actor_ai["AI Provider"]
    actor_scheduler["Scheduler"]

    subgraph Public_Area
        UC_Landing["Melihat Landing Page"]
        UC_Pricing["Melihat Pricing"]
        UC_Register["Register"]
        UC_Login["Login"]
        UC_Forgot["Forgot Password"]
    end

    subgraph Learning_Workspace
        UC_Dashboard["Melihat Dashboard"]
        UC_Upload["Upload Materi"]
        UC_Summary["Melihat Ringkasan AI"]
        UC_Chat["Chat dengan Nala atau AI Tutor"]
        UC_ImageChat["Upload Gambar ke Chat AI"]
        UC_Flashcard["Generate dan Review Flashcard"]
        UC_Quiz["Generate dan Mengerjakan Quiz"]
        UC_Pomodoro["Pomodoro"]
        UC_Focus["Focus Planner dan Insights"]
        UC_Profile["Kelola Profil"]
        UC_Notif["Kelola Notifikasi"]
    end

    subgraph Social_Learning
        UC_Room["Membuat atau Join Room Kelas"]
        UC_RoomChat["Chat Room Kelas"]
        UC_ProfileMatch["Menyiapkan Study Profile"]
        UC_Match["Study Matching atau Roulette"]
        UC_MatchChat["Chat Partner Belajar"]
        UC_EndMatch["Stop, End, Block, atau Report Match"]
    end

    subgraph Billing_Area
        UC_Buy["Checkout Premium atau Ultimate"]
        UC_Return["Payment Return"]
        UC_Webhook["Webhook Pembayaran"]
        UC_ResetCredit["Reset Credit Ultimate Bulanan"]
    end

    subgraph Admin_Area
        UC_AdminDashboard["Dashboard Admin"]
        UC_MonitorAI["Monitoring AI"]
        UC_Stats["Statistik Pembelajaran"]
        UC_ManageUser["Kelola User dan Plan"]
        UC_ManageDocs["Kelola Dokumen"]
    end

    actor_visitor --> UC_Landing
    actor_visitor --> UC_Pricing
    actor_visitor --> UC_Register
    actor_visitor --> UC_Login
    actor_visitor --> UC_Forgot

    actor_user --> UC_Dashboard
    actor_user --> UC_Upload
    actor_user --> UC_Summary
    actor_user --> UC_Chat
    actor_user --> UC_ImageChat
    actor_user --> UC_Flashcard
    actor_user --> UC_Quiz
    actor_user --> UC_Pomodoro
    actor_user --> UC_Focus
    actor_user --> UC_Profile
    actor_user --> UC_Notif
    actor_user --> UC_Room
    actor_user --> UC_RoomChat
    actor_user --> UC_ProfileMatch
    actor_user --> UC_Match
    actor_user --> UC_MatchChat
    actor_user --> UC_EndMatch
    actor_user --> UC_Buy
    actor_user --> UC_Return

    actor_admin --> UC_AdminDashboard
    actor_admin --> UC_MonitorAI
    actor_admin --> UC_Stats
    actor_admin --> UC_ManageUser
    actor_admin --> UC_ManageDocs

    actor_pakasir --> UC_Webhook
    actor_ai --> UC_Summary
    actor_ai --> UC_Chat
    actor_ai --> UC_Flashcard
    actor_ai --> UC_Quiz
    actor_scheduler --> UC_ResetCredit
```

## 2. ERD

```mermaid
erDiagram
    USERS ||--o{ MATERIALS : uploads
    USERS ||--o{ AI_SUMMARIES : owns
    USERS ||--o{ CHAT_THREADS : owns
    USERS ||--o{ PAYMENTS : pays
    USERS ||--|| STUDY_PROFILES : has
    USERS ||--o{ STUDY_ROOMS : owns
    USERS ||--o{ STUDY_ROOM_MEMBERS : joins
    USERS ||--o{ STUDY_ROOM_MESSAGES : sends
    USERS ||--o{ MATCH_QUEUE_ENTRIES : queues
    USERS ||--o{ STUDY_MATCH_MESSAGES : sends
    USERS ||--o{ USER_BLOCKS : blocks
    USERS ||--o{ USER_REPORTS : reports

    MATERIALS ||--o{ AI_SUMMARIES : generates
    MATERIALS ||--o{ CHAT_THREADS : contextualizes
    MATERIALS ||--|| FLASHCARD_DECKS : has
    MATERIALS ||--|| QUIZ_SETS : has

    CHAT_THREADS ||--o{ CHAT_MESSAGES : contains
    CHAT_MESSAGES ||--o{ CHAT_MESSAGE_ATTACHMENTS : has

    FLASHCARD_DECKS ||--o{ FLASHCARDS : contains
    QUIZ_SETS ||--o{ QUIZ_QUESTIONS : contains

    STUDY_ROOMS ||--o{ STUDY_ROOM_MEMBERS : has
    STUDY_ROOMS ||--o{ STUDY_ROOM_MESSAGES : contains
    STUDY_ROOM_MESSAGES ||--o{ STUDY_ROOM_MESSAGES : replies_to

    USERS ||--o{ STUDY_MATCHES : user_one
    USERS ||--o{ STUDY_MATCHES : user_two
    STUDY_MATCHES ||--o{ STUDY_MATCH_MESSAGES : contains

    USERS {
        bigint id PK
        string name
        string email
        string role
        string plan
        string plan_key
        timestamp plan_expires_at
        int room_limit
        int match_credits
        int match_credits_monthly_allowance
        timestamp match_credits_reset_at
    }

    MATERIALS {
        bigint id PK
        bigint user_id FK
        string title
        string file_path
        longtext raw_text
        string status
        string ocr_status
    }

    AI_SUMMARIES {
        bigint id PK
        bigint material_id FK
        bigint user_id FK
        string title
        longtext summary_text
        string model
    }

    CHAT_THREADS {
        bigint id PK
        bigint user_id FK
        bigint material_id FK
        string title
        string ai_status
        text ai_error
    }

    CHAT_MESSAGES {
        bigint id PK
        bigint thread_id FK
        string role
        longtext content
    }

    CHAT_MESSAGE_ATTACHMENTS {
        bigint id PK
        bigint chat_message_id FK
        string kind
        string disk
        string path
        string mime_type
        bigint size
    }

    FLASHCARD_DECKS {
        bigint id PK
        bigint material_id FK
        string title
    }

    FLASHCARDS {
        bigint id PK
        bigint flashcard_deck_id FK
        text front
        text back
        int interval
        timestamp due_at
    }

    QUIZ_SETS {
        bigint id PK
        bigint material_id FK
        string title
        string status
    }

    QUIZ_QUESTIONS {
        bigint id PK
        bigint quiz_set_id FK
        text question
        json options
        string correct_answer
    }

    STUDY_PROFILES {
        bigint id PK
        bigint user_id FK
        string learning_goal
        json topics
        boolean is_matchmaking_enabled
    }

    MATCH_QUEUE_ENTRIES {
        bigint id PK
        bigint user_id FK
        string selected_topic
        string status
        timestamp expires_at
    }

    STUDY_MATCHES {
        bigint id PK
        bigint user_one_id FK
        bigint user_two_id FK
        string topic
        string status
        timestamp matched_at
        timestamp ended_at
    }

    STUDY_MATCH_MESSAGES {
        bigint id PK
        bigint study_match_id FK
        bigint user_id FK
        text content
    }

    STUDY_ROOMS {
        bigint id PK
        bigint owner_id FK
        string name
        string topic
        string status
        int max_members
    }

    STUDY_ROOM_MEMBERS {
        bigint id PK
        bigint study_room_id FK
        bigint user_id FK
        string role
        string status
    }

    STUDY_ROOM_MESSAGES {
        bigint id PK
        bigint study_room_id FK
        bigint user_id FK
        bigint reply_to_message_id FK
        text content
    }

    PAYMENTS {
        bigint id PK
        bigint user_id FK
        string order_id
        string plan
        string plan_key
        string status
        int amount
        int duration_days
        timestamp paid_at
    }
```

## 3. Class Diagram

```mermaid
classDiagram
    class User {
        +materials()
        +summaries()
        +chatThreads()
        +payments()
        +studyProfile()
        +ownedRooms()
        +roomMemberships()
        +matchQueueEntries()
        +isPremium()
    }

    class Material {
        +user()
        +summaries()
        +chatThreads()
        +flashcardDeck()
        +quizSet()
    }

    class AiSummary {
        +material()
        +user()
    }

    class ChatThread {
        +user()
        +material()
        +messages()
    }

    class ChatMessage {
        +thread()
        +attachments()
    }

    class ChatMessageAttachment {
        +message()
        +url()
    }

    class FlashcardDeck {
        +material()
        +cards()
    }

    class Flashcard {
        +deck()
    }

    class QuizSet {
        +material()
        +questions()
    }

    class QuizQuestion {
        +quizSet()
    }

    class StudyProfile {
        +user()
    }

    class MatchQueueEntry {
        +user()
    }

    class StudyMatch {
        +userOne()
        +userTwo()
        +messages()
        +involves(user)
        +partnerFor(user)
    }

    class StudyRoom {
        +owner()
        +members()
        +messages()
    }

    class Payment {
        +user()
        +isCompleted()
        +makeOrderId()
    }

    class MaterialController
    class ChatMessageController
    class StudyMatchingController
    class BillingController
    class AdminUserController

    class MaterialTextExtractor
    class AiMaterialCleaner
    class StudyContentGenerator
    class StudyMatchingService
    class PaymentFulfillment
    class PakasirClient
    class PakasirPaymentVerifier
    class AiThreadResponder
    class OpenAiThreadResponder
    class GenerateThreadAiReply
    class AiUsageLimiter
    class RealtimePayloads

    User "1" --> "*" Material
    Material "1" --> "*" AiSummary
    Material "1" --> "*" ChatThread
    ChatThread "1" --> "*" ChatMessage
    ChatMessage "1" --> "*" ChatMessageAttachment
    Material "1" --> "1" FlashcardDeck
    FlashcardDeck "1" --> "*" Flashcard
    Material "1" --> "1" QuizSet
    QuizSet "1" --> "*" QuizQuestion
    User "1" --> "1" StudyProfile
    User "1" --> "*" MatchQueueEntry
    StudyMatch "1" --> "*" StudyMatchMessage
    StudyRoom "1" --> "*" StudyRoomMember
    StudyRoom "1" --> "*" StudyRoomMessage
    User "1" --> "*" Payment

    MaterialController --> MaterialTextExtractor
    MaterialController --> AiMaterialCleaner
    MaterialController --> Material
    MaterialController --> AiSummary

    ChatMessageController --> ChatThread
    ChatMessageController --> AiUsageLimiter
    ChatMessageController --> GenerateThreadAiReply
    ChatMessageController --> RealtimePayloads

    GenerateThreadAiReply --> AiThreadResponder
    OpenAiThreadResponder ..|> AiThreadResponder

    StudyMatchingController --> StudyMatchingService
    BillingController --> PakasirClient
    BillingController --> Payment
    BillingController --> PaymentFulfillment
    PakasirPaymentVerifier --> PaymentFulfillment
```

## 4. Activity Diagram Per Fitur - 3 Pool

Activity diagram dibuat terpisah per fitur agar alurnya lebih mudah dibaca. Setiap diagram tetap menggunakan tiga pool utama: **Pengguna atau Admin**, **Sistem Nalarin.ai**, dan **Layanan Eksternal**.

### 4.1 Activity Diagram - Register, Login, dan Dashboard

```mermaid
flowchart LR
    subgraph USER_POOL[Pool 1 - Pengguna]
        direction TB
        U1[Mulai]
        U2[Buka landing page atau pricing]
        U3[Pilih register atau login]
        U4[Isi data akun]
        U5[Masuk dashboard]
        U6[Lihat pesan error]
        U7[Selesai]
    end

    subgraph SYSTEM_POOL[Pool 2 - Sistem Nalarin.ai]
        direction TB
        S1[Tampilkan halaman publik]
        S2[Validasi data akun]
        S3{Data valid?}
        S4[Buat session user]
        S5[Ambil data profil, plan, dan fitur]
        S6[Tampilkan dashboard]
        S7[Tampilkan error autentikasi]
    end

    subgraph EXTERNAL_POOL[Pool 3 - Layanan Eksternal]
        direction TB
        X1[SMTP Email]
    end

    U1 --> U2 --> S1 --> U3 --> U4 --> S2 --> S3
    S3 -->|Tidak| S7 --> U6 --> U7
    S3 -->|Ya| S4 --> S5 --> S6 --> U5 --> U7
    S4 -. kirim verifikasi atau reset password .-> X1
```

### 4.2 Activity Diagram - Upload Materi dan Ringkasan AI

```mermaid
flowchart LR
    subgraph USER_POOL[Pool 1 - Pengguna]
        direction TB
        U1[Mulai dari dashboard]
        U2[Pilih upload materi]
        U3[Pilih file materi]
        U4[Kirim upload]
        U5[Lihat ringkasan AI]
        U6[Lihat pesan error]
        U7[Selesai]
    end

    subgraph SYSTEM_POOL[Pool 2 - Sistem Nalarin.ai]
        direction TB
        S1[Tampilkan form upload]
        S2[Validasi file dan akses user]
        S3{Upload valid?}
        S4[Simpan file materi]
        S5[Ekstrak teks dari file]
        S6{Teks terbaca?}
        S7[Kirim teks ke generator ringkasan]
        S8[Simpan material dan ringkasan]
        S9[Kirim status selesai]
        S10[Tampilkan error upload atau ekstraksi]
    end

    subgraph EXTERNAL_POOL[Pool 3 - Layanan Eksternal]
        direction TB
        X1[AI Provider OpenRouter]
        X2[Realtime Server Reverb]
    end

    U1 --> U2 --> S1 --> U3 --> U4 --> S2 --> S3
    S3 -->|Tidak| S10 --> U6 --> U7
    S3 -->|Ya| S4 --> S5 --> S6
    S6 -->|Tidak| S10 --> U6
    S6 -->|Ya| S7 --> X1 --> S8 --> S9 --> X2 --> U5 --> U7
```

### 4.3 Activity Diagram - Chat dengan Nala

```mermaid
flowchart LR
    subgraph USER_POOL[Pool 1 - Pengguna]
        direction TB
        U1[Mulai dari dashboard]
        U2[Buka chat Nala]
        U3[Pilih thread atau materi]
        U4[Tulis pesan atau upload gambar]
        U5[Kirim pesan]
        U6[Lihat balasan Nala]
        U7[Lihat pesan error]
        U8[Selesai]
    end

    subgraph SYSTEM_POOL[Pool 2 - Sistem Nalarin.ai]
        direction TB
        S1[Tampilkan halaman chat]
        S2[Validasi thread, attachment, dan limit AI]
        S3{Chat valid?}
        S4[Simpan pesan dan attachment]
        S5[Jalankan job balasan AI]
        S6[Bangun konteks chat dan materi]
        S7[Kirim prompt ke AI]
        S8[Simpan balasan AI]
        S9[Broadcast balasan realtime]
        S10[Tampilkan error chat]
    end

    subgraph EXTERNAL_POOL[Pool 3 - Layanan Eksternal]
        direction TB
        X1[AI Provider OpenRouter]
        X2[Realtime Server Reverb]
    end

    U1 --> U2 --> S1 --> U3 --> U4 --> U5 --> S2 --> S3
    S3 -->|Tidak| S10 --> U7 --> U8
    S3 -->|Ya| S4 --> S5 --> S6 --> S7 --> X1 --> S8 --> S9 --> X2 --> U6 --> U8
```

### 4.4 Activity Diagram - Generate Flashcard dan Quiz

```mermaid
flowchart LR
    subgraph USER_POOL[Pool 1 - Pengguna]
        direction TB
        U1[Mulai dari dashboard]
        U2[Buka materi]
        U3[Pilih generate flashcard atau quiz]
        U4[Review flashcard atau kerjakan quiz]
        U5[Lihat pesan error]
        U6[Selesai]
    end

    subgraph SYSTEM_POOL[Pool 2 - Sistem Nalarin.ai]
        direction TB
        S1[Ambil data materi]
        S2[Validasi kepemilikan, teks materi, dan limit]
        S3{Bisa generate?}
        S4[Siapkan prompt belajar]
        S5[Kirim permintaan generate]
        S6[Parsing hasil AI]
        S7[Simpan deck flashcard atau quiz set]
        S8[Tampilkan flashcard atau quiz]
        S9[Tampilkan error generate]
    end

    subgraph EXTERNAL_POOL[Pool 3 - Layanan Eksternal]
        direction TB
        X1[AI Provider OpenRouter]
    end

    U1 --> U2 --> S1 --> U3 --> S2 --> S3
    S3 -->|Tidak| S9 --> U5 --> U6
    S3 -->|Ya| S4 --> S5 --> X1 --> S6 --> S7 --> S8 --> U4 --> U6
```

### 4.5 Activity Diagram - Study Matching dan Room Kelas

```mermaid
flowchart LR
    subgraph USER_POOL[Pool 1 - Pengguna]
        direction TB
        U1[Mulai dari dashboard]
        U2[Buka fitur belajar sosial]
        U3[Pilih matching atau room kelas]
        U4[Isi topik dan preferensi]
        U5[Mulai chat partner atau room]
        U6[Lihat pesan error]
        U7[Selesai]
    end

    subgraph SYSTEM_POOL[Pool 2 - Sistem Nalarin.ai]
        direction TB
        S1[Tampilkan halaman belajar sosial]
        S2[Validasi study profile, topik, dan limit]
        S3{Permintaan valid?}
        S4[Proses antrian matching atau pembuatan room]
        S5{Partner atau room tersedia?}
        S6[Buat study match atau membership room]
        S7[Simpan pesan room atau match]
        S8[Broadcast update realtime]
        S9[Tampilkan status menunggu]
        S10[Tampilkan error sosial]
    end

    subgraph EXTERNAL_POOL[Pool 3 - Layanan Eksternal]
        direction TB
        X1[Realtime Server Reverb]
        X2[Scheduler Laravel]
    end

    U1 --> U2 --> S1 --> U3 --> U4 --> S2 --> S3
    S3 -->|Tidak| S10 --> U6 --> U7
    S3 -->|Ya| S4 --> S5
    S5 -->|Tidak| S9 --> U7
    S5 -->|Ya| S6 --> S8 --> X1 --> U5
    U5 --> S7 --> S8 --> X1 --> U5
    X2 -. expire antrian matching .-> S9
```

### 4.6 Activity Diagram - Checkout Plan Premium atau Ultimate

```mermaid
flowchart LR
    subgraph USER_POOL[Pool 1 - Pengguna]
        direction TB
        U1[Mulai dari pricing]
        U2[Pilih plan premium atau ultimate]
        U3[Klik checkout]
        U4[Lakukan pembayaran]
        U5[Lihat plan aktif]
        U6[Lihat status gagal atau pending]
        U7[Selesai]
    end

    subgraph SYSTEM_POOL[Pool 2 - Sistem Nalarin.ai]
        direction TB
        S1[Validasi user dan plan]
        S2{Checkout valid?}
        S3[Buat order dan invoice]
        S4[Arahkan user ke pembayaran]
        S5[Terima return atau webhook pembayaran]
        S6[Verifikasi status pembayaran]
        S7{Pembayaran berhasil?}
        S8[Update plan, expired date, dan credit]
        S9[Kirim notifikasi pembayaran]
        S10[Tampilkan status gagal atau pending]
    end

    subgraph EXTERNAL_POOL[Pool 3 - Layanan Eksternal]
        direction TB
        X1[Payment Gateway Pakasir]
        X2[SMTP Email]
        X3[Scheduler Laravel]
    end

    U1 --> U2 --> U3 --> S1 --> S2
    S2 -->|Tidak| S10 --> U6 --> U7
    S2 -->|Ya| S3 --> S4 --> X1 --> U4
    U4 --> X1 --> S5 --> S6 --> S7
    S7 -->|Tidak| S10 --> U6 --> U7
    S7 -->|Ya| S8 --> S9 --> X2 --> U5 --> U7
    X3 -. reset credit bulanan ultimate .-> S8
```

### 4.7 Activity Diagram - Admin Kelola Data

```mermaid
flowchart LR
    subgraph ADMIN_POOL[Pool 1 - Admin]
        direction TB
        A1[Mulai]
        A2[Login sebagai admin]
        A3[Buka dashboard admin]
        A4[Pilih kelola user, dokumen, atau monitoring AI]
        A5[Jalankan aksi admin]
        A6[Lihat hasil perubahan]
        A7[Lihat pesan error]
        A8[Selesai]
    end

    subgraph SYSTEM_POOL[Pool 2 - Sistem Nalarin.ai]
        direction TB
        S1[Validasi akun dan role admin]
        S2{Admin valid?}
        S3[Ambil data statistik dan daftar resource]
        S4[Validasi aksi admin]
        S5{Aksi valid?}
        S6[Update user, plan, dokumen, atau status]
        S7[Catat hasil aksi]
        S8[Tampilkan data terbaru]
        S9[Tampilkan error admin]
    end

    subgraph EXTERNAL_POOL[Pool 3 - Layanan Eksternal]
        direction TB
        X1[AI Provider OpenRouter]
        X2[SMTP Email]
    end

    A1 --> A2 --> S1 --> S2
    S2 -->|Tidak| S9 --> A7 --> A8
    S2 -->|Ya| S3 --> A3 --> A4 --> A5 --> S4 --> S5
    S5 -->|Tidak| S9 --> A7 --> A8
    S5 -->|Ya| S6 --> S7 --> S8 --> A6 --> A8
    S3 -. ambil statistik penggunaan AI .-> X1
    S7 -. kirim notifikasi perubahan penting .-> X2
```

## 5. Catatan Implementasi Diagram

- Untuk laporan akademik, gunakan **Use Case Diagram** untuk menjelaskan fitur dari sisi aktor.
- Gunakan **Activity Diagram Per Fitur - 3 Pool** agar laporan lebih mudah dibaca dan setiap fitur memiliki alur yang fokus.
- Jika butuh gambaran end-to-end, gabungkan ringkasan dari activity diagram register/login, upload materi, chat AI, study matching, billing, dan admin.
- Gunakan **ERD** untuk menjelaskan struktur database MySQL.
- Gunakan **Class Diagram** untuk menjelaskan arsitektur Laravel: Controller, Model, Service, Job, Event, dan Support class.
- Diagram di atas mengikuti struktur project saat ini: Laravel, MySQL, OpenRouter/AI provider, Pakasir payment, queue job, dan realtime event.
