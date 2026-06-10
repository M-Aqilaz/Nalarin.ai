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

    subgraph Public_Area
        UC_Landing([Melihat Landing Page])
        UC_Pricing([Melihat Pricing])
        UC_Register([Register])
        UC_Login([Login])
        UC_Forgot([Forgot Password])
    end

    subgraph Learning_Workspace
        UC_Dashboard([Melihat Dashboard])
        UC_Upload([Upload Materi])
        UC_Summary([Melihat Ringkasan AI])
        UC_Chat(["Chat dengan Nala / AI Tutor"])
        UC_ImageChat([Upload Gambar ke Chat AI])
        UC_Flashcard([Generate dan Review Flashcard])
        UC_Quiz([Generate dan Mengerjakan Quiz])
        UC_Pomodoro([Pomodoro])
        UC_Focus([Focus Planner dan Insights])
        UC_Profile([Kelola Profil])
        UC_Notif([Kelola Notifikasi])
    end

    subgraph Social_Learning
        UC_Room(["Membuat / Join Room Kelas"])
        UC_RoomChat([Chat Room Kelas])
        UC_ProfileMatch([Menyiapkan Study Profile])
        UC_Match(["Study Matching / Roulette"])
        UC_MatchChat([Chat Partner Belajar])
        UC_EndMatch(["Stop / End / Block / Report Match"])
    end

    subgraph Billing_Area
        UC_Buy(["Checkout Premium / Ultimate"])
        UC_Return([Payment Return])
        UC_Webhook([Webhook Pembayaran])
        UC_ResetCredit([Reset Credit Ultimate Bulanan])
    end

    subgraph Admin_Area
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
flowchart LR
    subgraph User
        direction TB
        A(["Mulai"])
        B["Buka halaman Upload Materi"]
        C["Isi judul dan upload file atau paste teks"]
        Z["Lihat halaman ringkasan"]
    end

    subgraph Frontend
        direction TB
        D["Kirim form upload"]
        Y["Tampilkan error validasi"]
        X["Redirect ke halaman ringkasan"]
    end

    subgraph Backend_Laravel
        direction TB
        E["MaterialController store"]
        F{Validasi input}
        G{Ada file?}
        H["Pilih raw text manual"]
        I["Atur batas OCR sesuai plan user"]
        J["MaterialTextExtractor ekstrak teks atau OCR"]
        K{Teks tersedia?}
        L["AiMaterialCleaner bersihkan materi"]
        M["Buat record Material"]
        N["Minta ringkasan AI"]
        O["Buat record AiSummary"]
    end

    subgraph File_Storage
        direction TB
        P[("Simpan file materi")]
    end

    subgraph AI_Service
        direction TB
        Q["Clean material text"]
        R["Generate summary"]
    end

    subgraph Database_MySQL
        direction TB
        S[("materials")]
        T[("ai_summaries")]
    end

    A --> B --> C --> D --> E --> F
    F -- Tidak valid --> Y --> C
    F -- Valid --> G
    G -- Tidak --> H --> K
    G -- Ya --> I --> J --> K
    J --> P
    K -- Tidak --> Y
    K -- Ya --> L --> Q --> M
    M --> S
    M --> N --> R --> O
    O --> T
    O --> X --> Z
```

## 5. Activity Diagram - Chat AI / Nala

```mermaid
flowchart LR
    subgraph User
        direction TB
        A(["Mulai"])
        B["Buka chat thread"]
        C["Kirim pesan, paste gambar, atau upload gambar"]
        Z["Lihat balasan Nala"]
    end

    subgraph Frontend_Chat
        direction TB
        D["Render thread dan sidebar"]
        E["Kirim request AJAX atau form"]
        F["Tampilkan pesan user dan loading AI"]
        Y["Tampilkan error limit atau validasi"]
        X["Tampilkan balasan AI realtime atau polling"]
    end

    subgraph Backend_Laravel
        direction TB
        G["ChatMessageController store"]
        H{Validasi pesan atau gambar}
        I["AiUsageLimiter cek cooldown dan limit"]
        J{Allowed?}
        K["Simpan ChatMessage role user"]
        L{Ada gambar?}
        M["Simpan ChatMessageAttachment"]
        N["Update ChatThread status queued"]
        O["Broadcast ThreadMessageCreated"]
        P["Dispatch GenerateThreadAiReply"]
        Q["Update title thread otomatis"]
    end

    subgraph Queue_Worker
        direction TB
        R["GenerateThreadAiReply job"]
        S["Ambil konteks thread dan attachment"]
        T["Panggil AiThreadResponder"]
    end

    subgraph AI_Provider
        direction TB
        U["Model teks atau vision menghasilkan jawaban"]
    end

    subgraph Database_MySQL
        direction TB
        DB1[("chat_threads")]
        DB2[("chat_messages")]
        DB3[("chat_message_attachments")]
    end

    subgraph Broadcast_Polling
        direction TB
        V["ThreadAiStatusUpdated"]
        W["ThreadMessageCreated"]
    end

    A --> B --> D --> C --> E --> G --> H
    H -- Tidak valid --> Y --> C
    H -- Valid --> I --> J
    J -- Tidak --> Y
    J -- Ya --> K --> DB2
    K --> L
    L -- Ya --> M --> DB3
    L -- Tidak --> N
    M --> N
    N --> DB1
    N --> Q --> DB1
    N --> O --> W --> F
    N --> V --> F
    O --> P --> R --> S --> T --> U
    U --> R
    R --> DB2
    R --> DB1
    R --> W --> X --> Z
```

## 6. Activity Diagram - Study Matching / Roulette

```mermaid
flowchart LR
    subgraph User_A
        direction TB
        A(["Mulai"])
        B["Buka Study Matching"]
        C["Isi Study Profile jika belum ada"]
        D["Klik Start Roulette atau Search"]
        Z["Chat dengan partner"]
    end

    subgraph Frontend_Matchmaking
        direction TB
        E["Tampilkan form profil atau roulette"]
        F["Kirim request start"]
        G["Polling status queue atau match"]
        Y["Redirect ke match aktif"]
        X["Tampilkan kuota habis atau error"]
    end

    subgraph Backend_Laravel
        direction TB
        H["StudyMatchingController"]
        I{StudyProfile aktif?}
        J["Simpan atau update StudyProfile"]
        K{Match credit > 0?}
        L["StudyMatchingService expire queue lama"]
        M["Cek active match"]
        N{Active match ada?}
        O["Cari candidate waiting"]
        P{Candidate ditemukan?}
        Q["Buat MatchQueueEntry waiting"]
        R["Update queue candidate matched"]
        S["Buat StudyMatch active"]
        T["Kurangi credit kedua user"]
        U["End, block, atau report match"]
    end

    subgraph User_B
        direction TB
        AA["Sudah berada di queue waiting"]
        AB["Menerima redirect atau trigger match"]
        AC["Chat dengan User A"]
    end

    subgraph Database_MySQL
        direction TB
        DB1[("study_profiles")]
        DB2[("match_queue_entries")]
        DB3[("study_matches")]
        DB4[("study_match_messages")]
        DB5[("user_blocks dan user_reports")]
        DB6[("users match_credits")]
    end

    subgraph Polling_Broadcast
        direction TB
        RT["Status match tersedia"]
    end

    A --> B --> E
    E --> C --> J --> DB1
    E --> D --> F --> H --> I
    I -- Tidak --> X
    I -- Ya --> K
    K -- Tidak --> X
    K -- Ya --> L --> M --> N
    N -- Ya --> Y
    N -- Tidak --> O
    AA --> DB2
    O --> DB2
    O --> P
    P -- Tidak --> Q --> DB2 --> G
    G --> H
    P -- Ya --> R --> DB2
    R --> S --> DB3
    S --> T --> DB6
    T --> RT --> Y
    RT --> AB
    Y --> Z
    AB --> AC
    Z --> DB4
    AC --> DB4
    Z --> U
    U --> DB3
    U --> DB5
```

## 7. Activity Diagram - Billing Premium / Ultimate

```mermaid
flowchart LR
    subgraph User
        direction TB
        A(["Mulai"])
        B["Buka Pricing"]
        C["Pilih Premium bulanan atau Ultimate tahunan"]
        Z["Plan aktif di dashboard atau profile"]
    end

    subgraph Frontend
        direction TB
        D["Kirim checkout request"]
        E["Redirect ke URL pembayaran"]
        Y["Tampilkan status pembayaran"]
    end

    subgraph Backend_Laravel
        direction TB
        F["BillingController checkout"]
        G["Buat Payment pending"]
        H["PakasirClient buat payment URL"]
        I["BillingController return"]
        J["PakasirWebhookController store"]
        K["PakasirPaymentVerifier verifikasi"]
        L{Webhook valid?}
        M["PaymentFulfillment complete"]
        N["Hitung masa aktif plan"]
        O["Update user plan dan limit"]
        P{Plan Ultimate yearly?}
        Q["Set reset credit bulanan"]
        R["Premium monthly tanpa reset bulanan"]
    end

    subgraph Pakasir
        direction TB
        S["Halaman pembayaran"]
        T{Pembayaran sukses?}
        U["Kirim webhook"]
        V["Redirect return"]
    end

    subgraph Database_MySQL
        direction TB
        DB1[("payments")]
        DB2[("users")]
    end

    subgraph Laravel_Scheduler
        direction TB
        W["billing reset annual credits"]
        X["Reset match credit Ultimate saat jatuh tempo"]
    end

    A --> B --> C --> D --> F --> G --> DB1
    G --> H --> E --> S --> T
    T -- Tidak --> V --> I --> Y
    T -- Ya --> U --> J --> K --> L
    L -- Tidak --> Y
    L -- Ya --> M --> N
    M --> DB1
    N --> O --> DB2
    O --> P
    P -- Ya --> Q --> DB2
    P -- Tidak --> R --> DB2
    Q --> Y --> Z
    R --> Y --> Z
    V --> I --> Y
    W --> X --> DB2
```

## 8. Activity Diagram - Admin Kelola User

```mermaid
flowchart LR
    subgraph Admin
        direction TB
        A(["Mulai"])
        B["Login"]
        C["Buka Admin Dashboard"]
        D["Pilih menu Users atau Documents"]
        E["Pilih aksi update plan, suspend, activate, atau delete dokumen"]
        Z["Lihat hasil aksi"]
    end

    subgraph Admin_UI
        direction TB
        F["Kirim request admin"]
        G["Tampilkan daftar user atau dokumen"]
        Y["Tampilkan status sukses atau error"]
    end

    subgraph Middleware
        direction TB
        H["Authenticate"]
        I["AdminMiddleware cek role"]
        J{Role admin?}
    end

    subgraph Backend_Laravel
        direction TB
        K["AdminController, AdminUserController, AdminDocumentController"]
        L{Jenis aksi}
        M["Update plan user"]
        N["Suspend user"]
        O["Activate user"]
        P["Delete material atau dokumen"]
        Q["Load statistik atau monitoring AI"]
    end

    subgraph Database_MySQL
        direction TB
        DB1[("users")]
        DB2[("materials")]
        DB3[("ai_summaries")]
        DB4[("feature_usages")]
        DB5[("payments")]
    end

    A --> B --> H --> I --> J
    J -- Tidak --> Y
    J -- Ya --> C --> D --> F --> K
    K --> Q
    Q --> DB1
    Q --> DB2
    Q --> DB3
    Q --> DB4
    Q --> DB5
    Q --> G
    G --> E --> L
    L -- Update plan --> M --> DB1
    L -- Suspend --> N --> DB1
    L -- Activate --> O --> DB1
    L -- Delete dokumen --> P --> DB2
    M --> Y --> Z
    N --> Y --> Z
    O --> Y --> Z
    P --> Y --> Z
```

## 9. Catatan Implementasi Diagram

- Untuk laporan akademik, gunakan **Use Case Diagram** untuk menjelaskan fitur dari sisi aktor.
- Gunakan **Activity Diagram** per proses utama, bukan satu diagram besar.
- Gunakan **ERD** untuk menjelaskan struktur database MySQL.
- Gunakan **Class Diagram** untuk menjelaskan arsitektur Laravel: Controller, Model, Service, Job, Event, dan Support class.
- Diagram di atas mengikuti struktur project saat ini: Laravel, MySQL, OpenRouter/AI provider, Pakasir payment, queue job, dan realtime event.
