# Diagram Sistem Nalarin.ai

Dokumen ini berisi versi lengkap diagram utama untuk project Nalarin.ai. Format diagram memakai Mermaid agar bisa ditempel ke Markdown viewer, GitHub, atau draw.io yang mendukung Mermaid.

## 1. Use Case Diagram

```mermaid
flowchart LR
    Visitor([Visitor])
    User([User])
    Admin([Admin])
    Pakasir([Pakasir])
    AI([AI Provider / OpenRouter])
    Scheduler([Scheduler])

    subgraph Public["Public Area"]
        UC_Landing([Melihat Landing Page])
        UC_Pricing([Melihat Pricing])
        UC_Register([Register])
        UC_Login([Login])
        UC_Forgot([Forgot Password])
    end

    subgraph Learning["Learning Workspace"]
        UC_Dashboard([Melihat Dashboard])
        UC_Upload([Upload Materi])
        UC_Summary([Melihat Ringkasan AI])
        UC_Chat([Chat dengan Nala / AI Tutor])
        UC_ImageChat([Upload Gambar ke Chat AI])
        UC_Flashcard([Generate dan Review Flashcard])
        UC_Quiz([Generate dan Mengerjakan Quiz])
        UC_Pomodoro([Pomodoro])
        UC_Focus([Focus Planner dan Insights])
        UC_Profile([Kelola Profil])
        UC_Notif([Kelola Notifikasi])
    end

    subgraph Social["Social Learning"]
        UC_Room([Membuat / Join Room Kelas])
        UC_RoomChat([Chat Room Kelas])
        UC_ProfileMatch([Menyiapkan Study Profile])
        UC_Match([Study Matching / Roulette])
        UC_MatchChat([Chat Partner Belajar])
        UC_EndMatch([Stop / End / Block / Report Match])
    end

    subgraph Billing["Billing"]
        UC_Buy([Checkout Premium / Ultimate])
        UC_Return([Payment Return])
        UC_Webhook([Webhook Pembayaran])
        UC_ResetCredit([Reset Credit Ultimate Bulanan])
    end

    subgraph AdminArea["Admin Area"]
        UC_AdminDashboard([Dashboard Admin])
        UC_MonitorAI([Monitoring AI])
        UC_Stats([Statistik Pembelajaran])
        UC_ManageUser([Kelola User dan Plan])
        UC_ManageDocs([Kelola Dokumen])
    end

    Visitor --> UC_Landing
    Visitor --> UC_Pricing
    Visitor --> UC_Register
    Visitor --> UC_Login
    Visitor --> UC_Forgot

    User --> UC_Dashboard
    User --> UC_Upload
    User --> UC_Summary
    User --> UC_Chat
    User --> UC_ImageChat
    User --> UC_Flashcard
    User --> UC_Quiz
    User --> UC_Pomodoro
    User --> UC_Focus
    User --> UC_Profile
    User --> UC_Notif
    User --> UC_Room
    User --> UC_RoomChat
    User --> UC_ProfileMatch
    User --> UC_Match
    User --> UC_MatchChat
    User --> UC_EndMatch
    User --> UC_Buy
    User --> UC_Return

    Admin --> UC_AdminDashboard
    Admin --> UC_MonitorAI
    Admin --> UC_Stats
    Admin --> UC_ManageUser
    Admin --> UC_ManageDocs

    Pakasir --> UC_Webhook
    AI --> UC_Summary
    AI --> UC_Chat
    AI --> UC_Flashcard
    AI --> UC_Quiz
    Scheduler --> UC_ResetCredit
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

## 4. Activity Diagram - Upload Materi dan Ringkasan

```mermaid
flowchart TD
    A([Mulai]) --> B[User buka Upload Materi]
    B --> C[Isi judul dan upload file / paste teks]
    C --> D{Validasi input}
    D -- Tidak valid --> E[Tampilkan error]
    E --> C
    D -- Valid --> F{Ada file?}
    F -- Ya --> G[MaterialTextExtractor ekstrak teks / OCR]
    F -- Tidak --> H[Gunakan raw_text]
    G --> I{Teks berhasil didapat?}
    I -- Tidak --> J[Tampilkan error atau minta teks manual]
    J --> C
    I -- Ya --> K[AiMaterialCleaner membersihkan materi]
    H --> L[Simpan Material]
    K --> L
    L --> M[AI membuat ringkasan]
    M --> N[Simpan AiSummary]
    N --> O[Redirect ke halaman ringkasan]
    O --> P([Selesai])
```

## 5. Activity Diagram - Chat AI / Nala

```mermaid
flowchart TD
    A([Mulai]) --> B[User buka Chat Thread]
    B --> C[User kirim pesan / gambar]
    C --> D{Validasi pesan dan gambar}
    D -- Gagal --> E[Tampilkan error]
    E --> C
    D -- Berhasil --> F[AiUsageLimiter cek kuota/cooldown]
    F -- Tidak allowed --> G[Return 429 / pesan limit]
    F -- Allowed --> H[Simpan ChatMessage role user]
    H --> I{Ada gambar?}
    I -- Ya --> J[Simpan ChatMessageAttachment]
    I -- Tidak --> K[Skip attachment]
    J --> L[Update ChatThread ai_status queued]
    K --> L
    L --> M[Broadcast ThreadMessageCreated]
    M --> N[Broadcast ThreadAiStatusUpdated]
    N --> O[Dispatch GenerateThreadAiReply]
    O --> P[OpenAiThreadResponder panggil AI Provider]
    P --> Q{AI sukses?}
    Q -- Ya --> R[Simpan balasan AI ke ChatMessage]
    Q -- Tidak --> S[Update ai_status error dan ai_error]
    R --> T[Broadcast balasan dan status selesai]
    S --> U[User melihat error]
    T --> V([Selesai])
    U --> V
```

## 6. Activity Diagram - Study Matching / Roulette

```mermaid
flowchart TD
    A([Mulai]) --> B[User buka Study Matching]
    B --> C{Study Profile sudah ada?}
    C -- Tidak --> D[User isi profil belajar]
    D --> E[Simpan StudyProfile]
    C -- Ya --> F[Masuk Roulette / Matching]
    E --> F
    F --> G{Match credit > 0?}
    G -- Tidak --> H[Tampilkan kuota habis]
    G -- Ya --> I[Expire queue lama]
    I --> J{Ada active match?}
    J -- Ya --> K[Redirect ke match aktif]
    J -- Tidak --> L[Cari candidate waiting yang cocok]
    L --> M{Candidate ditemukan?}
    M -- Ya --> N[Update queue candidate menjadi matched]
    N --> O[Buat StudyMatch active]
    O --> P[Kurangi match credit kedua user]
    P --> Q[Redirect kedua user ke halaman match]
    M -- Tidak --> R[Buat MatchQueueEntry waiting]
    R --> S[Frontend polling status]
    S --> L
    Q --> T[User chat partner]
    T --> U{End / block / report?}
    U -- End --> V[Update match ended]
    U -- Block --> W[Simpan UserBlock dan end match]
    U -- Report --> X[Simpan UserReport]
    V --> Y([Selesai])
    W --> Y
    X --> Y
```

## 7. Activity Diagram - Billing Premium / Ultimate

```mermaid
flowchart TD
    A([Mulai]) --> B[User buka Pricing]
    B --> C[Pilih Premium bulanan / Ultimate tahunan]
    C --> D[BillingController buat Payment pending]
    D --> E[PakasirClient buat checkout URL]
    E --> F[Redirect user ke Pakasir]
    F --> G[Pakasir proses pembayaran]
    G --> H{Pembayaran sukses?}
    H -- Tidak --> I[Payment tetap pending / failed]
    H -- Ya --> J[Pakasir kirim webhook]
    J --> K[PakasirWebhookController terima webhook]
    K --> L[PakasirPaymentVerifier verifikasi]
    L --> M{Valid?}
    M -- Tidak --> N[Tolak webhook]
    M -- Ya --> O[PaymentFulfillment complete]
    O --> P[Update Payment completed]
    P --> Q[Update User plan, plan_key, expiry, room_limit, match_credit]
    Q --> R{Ultimate yearly?}
    R -- Ya --> S[Set match_credits_reset_at bulan depan]
    R -- Tidak --> T[Reset bulanan tidak aktif]
    S --> U([Selesai])
    T --> U
    I --> U
    N --> U
```

## 8. Activity Diagram - Admin Kelola User

```mermaid
flowchart TD
    A([Mulai]) --> B[Admin login]
    B --> C{Role admin?}
    C -- Tidak --> D[403 Forbidden]
    C -- Ya --> E[Buka Admin Dashboard]
    E --> F[Pilih menu Users]
    F --> G[Lihat daftar user]
    G --> H{Aksi admin}
    H -- Ubah plan --> I[Update plan free / premium]
    H -- Suspend --> J[Set status suspended]
    H -- Activate --> K[Set status active]
    H -- Lihat dokumen --> L[Buka admin documents]
    L --> M{Delete dokumen?}
    M -- Ya --> N[Hapus Material]
    M -- Tidak --> O[Kembali]
    I --> P[Redirect dengan status]
    J --> P
    K --> P
    N --> P
    O --> P
    P --> Q([Selesai])
```

## 9. Catatan Implementasi Diagram

- Untuk laporan akademik, gunakan **Use Case Diagram** untuk menjelaskan fitur dari sisi aktor.
- Gunakan **Activity Diagram** per proses utama, bukan satu diagram besar.
- Gunakan **ERD** untuk menjelaskan struktur database MySQL.
- Gunakan **Class Diagram** untuk menjelaskan arsitektur Laravel: Controller, Model, Service, Job, Event, dan Support class.
- Diagram di atas mengikuti struktur project saat ini: Laravel, MySQL, OpenRouter/AI provider, Pakasir payment, queue job, dan realtime event.
