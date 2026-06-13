# 🏠 RoomHub — Room Rental Marketplace

RoomHub is a full-stack room rental marketplace that connects **Landlords** and **Tenants**. Built with **Laravel 13** REST API backend and **Nuxt 3** frontend.

---

## 📁 Monorepo Structure

```
roomhub/
├── 📁 backend/       # Laravel 13 REST API
└── 📁 frotend/       # Nuxt 3 Web Application
```

---

## 🖥️ Backend — Laravel 13

### Tech Stack
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Sanctum](https://img.shields.io/badge/Sanctum-Token_Auth-FF2D20?style=flat&logo=laravel&logoColor=white)

### Quick Setup

```bash
cd backend

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
# → http://localhost:8000
```

### Folder Highlights

```
backend/app/
├── Http/
│   ├── Controllers/Api/Auth/     # Login, Register, ForgotPassword, Refresh
│   ├── Middleware/               # AdminMiddleware, AuthMiddleware
│   └── Requests/Api/Auth/       # Form validation classes
├── Models/
│   ├── User.php
│   └── RefreshToken.php
└── Service/Api/Auth/
    ├── LoginService.php
    ├── RegisterService.php
    └── RoleService.php
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/auth/register` | Register new user |
| `POST` | `/api/auth/login` | Login |
| `POST` | `/api/auth/refresh` | Refresh access token |
| `POST` | `/api/auth/logout` | Logout |
| `POST` | `/api/auth/forgot-password` | Send OTP |
| `PUT`  | `/api/auth/forgot-password` | Reset password |

---

## 🌐 Frontend — Nuxt 3

### Tech Stack
![Nuxt](https://img.shields.io/badge/Nuxt-3-00DC82?style=flat&logo=nuxt.js&logoColor=white)
![Vue](https://img.shields.io/badge/Vue-3-4FC08D?style=flat&logo=vue.js&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-06B6D4?style=flat&logo=tailwindcss&logoColor=white)

### Quick Setup

```bash
cd frotend

# Install dependencies
npm install

# Environment setup
cp .env.example .env

# Start dev server
npm run dev
# → http://localhost:3000
```

### Folder Highlights

```
frotend/app/
├── components/         # Navbar, Footer, Admin components
├── composables/        # useAuth.js, useDashboard.js
├── layouts/            # admin, auth, dashboard, default
├── middleware/         # auth.global.js, admin.js, role.js
├── pages/
│   ├── auth/           # login, sign-up, select-role, callback
│   ├── dashboard/      # landlord, rental panels
│   └── admin/          # admin panel
└── plugins/            # api.js, auth-init.client.js
```

---

## 👥 User Roles

| Role | Description |
|------|-------------|
| 🏠 **Landlord** | List and manage rental rooms |
| 🔍 **Tenant** | Browse and book rooms |
| 🛡️ **Admin** | Manage platform, users, and listings |

---

## 🔄 Auth Flow

```
Register ──► Select Role ──► Dashboard
Login    ──► (email/password or Google OAuth) ──► Dashboard
Forgot   ──► Send OTP ──► Verify OTP ──► Reset Password ──► Login
```

---

## 🌱 Environment Variables

### Backend (`backend/.env`)
```env
APP_NAME=RoomHub
APP_URL=http://localhost:8000
DB_DATABASE=roomhub
DB_USERNAME=root
DB_PASSWORD=
SANCTUM_STATEFUL_DOMAINS=localhost:3000
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

### Frontend (`frotend/.env`)
```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
NUXT_PUBLIC_GOOGLE_CLIENT_ID=
```

---

## 🚀 Running the Full Stack

```bash
# Terminal 1 — Backend
cd backend && php artisan serve
# → http://localhost:8000

# Terminal 2 — Frontend
cd frotend && npm run dev
# → http://localhost:3000
```

---

## 🗄️ Database

```bash
# Run migrations
php artisan migrate

# Seed admin user
php artisan db:seed --class=AdminUserSeeder

# Fresh migration + seed
php artisan migrate:fresh --seed
```

---

## 🧪 Testing

```bash
# Backend tests
cd backend && php artisan test
```

---

## 📌 Git Workflow

```bash
# Feature branch
git checkout -b feat/your-feature

# Commit with convention
git commit -m "feat: description"     # new feature
git commit -m "fix: description"      # bug fix
git commit -m "docs: description"     # documentation
git commit -m "refactor: description" # code cleanup

# Push
git push origin feat/your-feature
```

---

## 📄 License

MIT © RoomHub 2025
