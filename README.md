# PTE Portal

A full-featured PTE (Pearson Test of English) exam preparation platform for foreign students. Built with Laravel 11 and Vue 3, it delivers all four PTE exam modules in an authentic exam-like interface, with subscription management and multi-tenant support so multiple PTE institutions can run their own branded portals.

---

## Features

### Exam Modules (20 question types)

| Module | Question Types |
|--------|---------------|
| **Speaking** | Read Aloud, Repeat Sentence, Describe Image, Retell Lecture, Answer Short Question |
| **Reading** | Multiple Choice (Single/Multiple), Reorder Paragraphs, Fill in the Blanks, Reading & Writing Fill Blanks |
| **Writing** | Summarize Written Text, Write Essay |
| **Listening** | Summarize Spoken Text, MC Single/Multiple, Fill Blanks, Highlight Correct Summary, Select Missing Word, Highlight Incorrect Words, Write from Dictation |

### Platform Features

- **Browser-based audio recording** — Speaking responses recorded with the MediaRecorder API, no plugins needed
- **Auto-scoring** — Algorithmic scoring for all question types (pluggable with Azure Speech API / OpenAI)
- **Subscription system** — Custom plans with monthly/quarterly/yearly billing, trial periods, and per-tenant pricing
- **Multi-tenancy** — Single database, tenant identified by subdomain or `?tenant=` query param; each institution has its own branding, plans, and student roster
- **Admin panel** — Full CRUD for questions, student management, subscription plans, and reporting
- **Score breakdown** — Detailed feedback with Content, Fluency, Pronunciation, Grammar, Vocabulary, and Spelling sub-scores
- **Mock tests** — Full timed exam simulations across all four modules

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 |
| Frontend | Vue 3 (Composition API, `<script setup>`) |
| State management | Pinia |
| Backend ↔ Frontend | Inertia.js |
| Styling | Tailwind CSS |
| Database | MySQL / SQLite |
| Build | Vite + laravel-vite-plugin |
| Routing (client) | Ziggy |

---

## Requirements

### Docker (recommended)
- [Docker](https://docs.docker.com/get-docker/) 24+
- [Docker Compose](https://docs.docker.com/compose/install/) v2+

### Local development
- PHP 8.2+
- Composer
- Node.js 18+ and npm
- SQLite (default for local dev) or MySQL 8+

---

## Running with Docker

Docker is the recommended way to run the application. It bundles PHP-FPM, Nginx, and MySQL into a single `docker compose up` command — no local PHP or Node installation needed.

### 1. Clone the repository

```bash
git clone https://github.com/salilkoirala46/pte-portal.git
cd pte-portal
```

### 2. Build and start the containers

```bash
docker compose up -d --build
```

This will:
- Build a multi-stage image (Node compiles frontend assets, PHP-FPM serves the app)
- Start a MySQL 8 database container
- Run migrations and seed the database automatically on first boot
- Expose the app on **http://localhost:8080**

The first build takes a few minutes. Watch progress with:

```bash
docker compose logs -f app
```

Wait until you see:

```
INFO  Configuration cached successfully.
INFO  Routes cached successfully.
```

### 3. Open the app

| URL | Description |
|-----|-------------|
| http://localhost:8080 | Landing page |
| http://localhost:8080/login?tenant=demo | Tenant login |

> The `?tenant=demo` query parameter is required for tenant-scoped accounts (admin and student). The super admin can log in without it.

---

## Demo Credentials

| Role | Email | Password | Login URL |
|------|-------|----------|-----------|
| Institution Admin | admin@pteacademy.com | password | http://localhost:8080/login?tenant=demo |
| Student | student@demo.com | password | http://localhost:8080/login?tenant=demo |
| Super Admin | super@pteportal.com | password | http://localhost:8080/login |

---

## Docker Commands

```bash
# Start containers in the background
docker compose up -d

# Stop containers
docker compose down

# Rebuild after code changes
docker compose up -d --build

# View live logs
docker compose logs -f app

# Run Artisan commands
docker compose exec app php artisan <command>

# Re-run database seeders
docker compose exec app php artisan db:seed --force

# Open a shell inside the container
docker compose exec app sh
```

### Data persistence

Docker named volumes keep your data between restarts:

| Volume | Contents |
|--------|----------|
| `mysql_data` | MySQL database files |
| `storage_data` | Uploaded files (`storage/app`) |
| `logs_data` | Application logs (`storage/logs`) |

To wipe everything and start fresh:

```bash
docker compose down -v
docker compose up -d --build
```

---

## Local Installation (without Docker)

```bash
# 1. Clone the repository
git clone https://github.com/salilkoirala46/pte-portal.git
cd pte-portal

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Set up the database (SQLite by default)
touch database/database.sqlite
php artisan migrate --seed

# 6. Create the storage symlink
php artisan storage:link

# 7. Build frontend assets
npm run build

# 8. Start the development server
php artisan serve
```

Visit `http://localhost:8000?tenant=demo` to see the application.

---

## Multi-Tenancy

Tenants are identified by:

1. **Subdomain** in production — `demo.yourdomain.com`
2. **Custom domain** — configured per tenant in the database
3. **Query param** in local dev — `?tenant=demo`

Set your central domain in `.env`:

```env
CENTRAL_DOMAIN=yourdomain.com
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Question, student, plan, settings management
│   │   ├── Student/        # Speaking, reading, writing, listening, results
│   │   ├── Auth/           # Login, register, logout
│   │   └── Api/            # Audio upload/stream, scoring endpoints
│   ├── Middleware/
│   │   ├── IdentifyTenant.php
│   │   ├── CheckSubscription.php
│   │   └── HandleInertiaRequests.php
│   └── Requests/
├── Models/                 # Tenant, User, Question, Attempt, Score, ...
├── Policies/               # AttemptPolicy (role-based result access)
└── Services/
    └── ScoringService.php  # Auto-scoring for all 20 question types

resources/js/
├── pages/
│   ├── Auth/               # Login, Register, Landing
│   ├── Student/            # Dashboard, Speaking, Reading, Writing, Listening, Results
│   └── Admin/              # Dashboard, Questions, Students, Plans, Reports, Settings
├── layouts/                # AppLayout, AuthLayout, ExamLayout
├── components/
│   ├── QuestionTypes/      # All 20 question type components
│   └── UI/                 # AudioRecorder, AudioPlayer, ScoreCard, Timer, FlashMessage
├── stores/                 # Pinia: auth, exam, speaking, subscription
└── composables/            # useTimer, useWordCount

database/
├── migrations/             # 12 application migrations
└── seeders/                # QuestionTypeSeeder, TenantSeeder
```

---

## Scoring

Scoring is handled by `app/Services/ScoringService.php`. By default it uses algorithmic mock scoring. To integrate real AI scoring, replace the relevant methods:

- **Speaking** — integrate Azure Cognitive Speech Services (`scoreSpeaking`)
- **Writing** — integrate OpenAI GPT (`scoreWriting`, `scoreSummarizeWritten`)
- **Listening** — `scoreListening` dispatches to per-type handlers

Set the API keys in `.env`:

```env
AZURE_SPEECH_KEY=your_key
AZURE_SPEECH_REGION=australiaeast
OPENAI_API_KEY=your_key
```

---

## Development

For local development with hot module replacement (HMR):

```bash
# Terminal 1 — Laravel backend
php artisan serve

# Terminal 2 — Vite dev server (HMR)
npm run dev
```

Then visit `http://localhost:8000?tenant=demo`.

To rebuild frontend assets for production:

```bash
npm run build
```

---

## License

MIT
