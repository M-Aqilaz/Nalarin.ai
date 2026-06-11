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

## 4. Activity Diagram Sederhana - 3 Pool Utama

Diagram ini menyederhanakan activity diagram utama menjadi tiga pool vertikal yang sejajar. Detail teknis seperti controller, tabel, queue, dan service tetap direpresentasikan di dalam pool **Sistem Nalarin.ai** agar alurnya tidak berubah makna.

```mermaid
flowchart TB
    subgraph USER_POOL["Pool 1 - Pengguna dan Admin"]
        direction TB
        U0[" "]
        U1(["Mulai"])
        U2["Buka landing page atau pricing"]
        U3["Register atau login"]
        U4["Masuk dashboard"]
        U5{"Pilih kebutuhan"}
        U6["Upload materi"]
        U7["Chat dengan Nala"]
        U8["Buat flashcard atau quiz"]
        U9["Study matching atau room kelas"]
        U10["Checkout plan premium atau ultimate"]
        U11["Admin kelola data"]
        U12["Lihat hasil, status, dan notifikasi"]
        U13(["Selesai"])
    end

    subgraph SYSTEM_POOL["Pool 2 - Sistem Nalarin.ai"]
        direction TB
        S0[" "]
        S1["Tampilkan halaman publik"]
        S2{"Validasi akun dan role"}
        S3["Tampilkan dashboard dan menu fitur"]
        S4{"Validasi request"}
        S5["Simpan file dan ekstrak teks"]
        S6{"Teks berhasil dibaca?"}
        S7["Simpan material"]
        S8["Generate dan simpan ringkasan"]
        S9["Simpan pesan chat dan attachment"]
        S10["Jalankan job balasan AI"]
        S11["Simpan balasan Nala"]
        S12["Generate flashcard atau quiz"]
        S13["Cari partner, room, atau queue match"]
        S14["Buat invoice pembayaran"]
        S15["Verifikasi pembayaran dan update plan"]
        S16["Load analytics dan proses aksi admin"]
        S17["Kirim notifikasi atau realtime update"]
        S18["Tampilkan error validasi atau proses"]
    end

    subgraph EXTERNAL_POOL["Pool 3 - Layanan Eksternal"]
        direction TB
        X0[" "]
        X1["AI Provider OpenRouter"]
        X2["Payment Gateway Pakasir"]
        X3["SMTP Email"]
        X4["Realtime Server Reverb"]
        X5["Scheduler Laravel"]
    end

    U0 ~~~ S0 ~~~ X0

    U1 --> U2 --> S1 --> U3 --> S2
    S2 -- "Tidak valid" --> S18 --> U3
    S2 -- "Valid" --> U4 --> S3 --> U5

    U5 -- "Materi dan ringkasan" --> U6 --> S4
    S4 -- "Tidak valid" --> S18 --> U12
    S4 -- "Valid" --> S5 --> S6
    S6 -- "Tidak" --> S18 --> U12
    S6 -- "Ya" --> S7 --> S8 --> X1 --> S17 --> U12

    U5 -- "Chat Nala" --> U7 --> S4
    S4 -- "Chat valid" --> S9 --> S10 --> X1 --> S11 --> S17 --> X4 --> U12

    U5 -- "Flashcard atau quiz" --> U8 --> S12 --> X1 --> S17 --> U12

    U5 -- "Study matching atau room" --> U9 --> S13 --> S17 --> X4 --> U12

    U5 -- "Billing" --> U10 --> S14 --> X2 --> S15 --> S17 --> X3 --> U12
    X5 --> S15

    U5 -- "Admin" --> U11 --> S2
    S2 -- "Admin valid" --> S16 --> U12

    U12 --> U13

    style U0 fill:transparent,stroke:transparent,color:transparent
    style S0 fill:transparent,stroke:transparent,color:transparent
    style X0 fill:transparent,stroke:transparent,color:transparent
```

## 5. Catatan Implementasi Diagram

- Untuk laporan akademik, gunakan **Use Case Diagram** untuk menjelaskan fitur dari sisi aktor.
- Gunakan **Activity Diagram Sederhana - 3 Pool Utama** jika laporan membutuhkan gambaran alur end-to-end yang ringkas.
- Jika butuh detail teknis lebih dalam, pecah lagi activity diagram berdasarkan fitur: upload materi, chat AI, study matching, billing, dan admin.
- Gunakan **ERD** untuk menjelaskan struktur database MySQL.
- Gunakan **Class Diagram** untuk menjelaskan arsitektur Laravel: Controller, Model, Service, Job, Event, dan Support class.
- Diagram di atas mengikuti struktur project saat ini: Laravel, MySQL, OpenRouter/AI provider, Pakasir payment, queue job, dan realtime event.
