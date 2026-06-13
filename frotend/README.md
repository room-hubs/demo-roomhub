# Frontend — Rental Platform (Nuxt 3)

A Nuxt 3 SPA frontend for the rental platform API (Laravel + Sanctum backend).

## Tech Stack

- **Framework**: Nuxt 3 (`srcDir: app/`)
- **UI**: `@nuxt/ui` + Tailwind CSS
- **Auth**: Laravel Sanctum (Bearer tokens + refresh token rotation)
- **Social Login**: Google, Facebook (via Laravel Socialite)

## Project Structure

```
app/
├── assets/             # Static assets, CSS
├── components/         # Reusable Vue components (admin/, dashboard/, etc.)
├── composables/
│   ├── useAuth.js       # Auth state, login/register/logout, token refresh
│   ├── useDashboard.js  # Shared dashboard UI state (slideovers, etc.)
│   └── useRandom.js
├── layouts/
│   ├── admin.vue        # Layout for admin pages
│   ├── auth.vue         # Layout for login/register pages
│   ├── dashboard.vue     # Layout for landlord/rental dashboards
│   └── default.vue
├── middleware/
│   ├── auth.global.js   # Runs on every route — token/session checks
│   ├── auth.js          # Per-page auth guard
│   ├── admin.js         # Restricts routes to role === 'admin'
│   └── role.js          # Restricts dashboard routes by role (landlord/rental)
├── pages/
│   ├── admin/           # Admin-only pages
│   ├── auth/            # Login, register, select-role, callback
│   ├── dashboard/
│   │   ├── landlord/    # Landlord-specific dashboard pages
│   │   └── rental/      # Rental-specific dashboard pages
│   └── user/
├── plugins/
│   ├── api.js            # Centralized $fetch client — attaches auth token,
│   │                      # handles 401 → silent refresh → retry
│   └── auth-init.client.js  # Initializes auth state on app load
└── app.vue
```

## Environment Setup

```bash
npm install
cp .env.example .env
```

Set the API base URL in `.env`:
```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

Run the dev server:
```bash
npm run dev
```

Default dev port: **3001**.

## Authentication Architecture

### Token Model

The app uses **two tokens**, both stored as cookies via `useCookie()`:

| Token | Lifetime | Purpose |
|---|---|---|
| `auth_token` | Cookie: 30 days / Server: 1 hour | Sent as `Authorization: Bearer <token>` on every API request |
| `refresh_token` | Cookie: 30 days / Server: 30 days | Sent only to `POST /auth/refresh` to obtain a new access token |

Because the access token expires server-side after 1 hour but the cookie persists for 30 days, expired-token requests are expected — `api.js` automatically detects `401` responses and silently refreshes before retrying.

### Auth Flow

1. **Login/Register** (`useAuth().login()` / `register()`) — returns `{ user, token, refresh_token }`. Both tokens are stored.
2. **Social login** (`loginWithGoogle()` / `loginWithFacebook()`) — redirects to the backend OAuth flow. On return, the backend redirects to `/auth/callback?code=...` with a short-lived one-time code (never the raw token, to avoid leaking tokens via browser history/logs). The callback page calls `exchangeOAuthCode(code)` to retrieve the real tokens.
3. **Role assignment** (`updateRole()`) — for new/social-signup users with no role yet (`needs_role: true`), redirects to `/auth/select-role`. Assigning a role activates the account and issues fresh tokens.
4. **Silent refresh** — handled centrally in `plugins/api.js`. Any `401` triggers `tryRefreshToken()`, which exchanges `refresh_token` for a new token pair. If refresh also fails, the user is logged out and redirected to `/auth/login`.
5. **Logout** (`logout()`) — calls `POST /auth/logout` (revokes the current access token + refresh token server-side), clears both cookies, redirects to `/auth/login`.

### Route Protection

- **`auth.global.js`** — runs on every navigation:
  - No token + non-public route → redirect to `/auth/login`
  - Has token + on a public route (login/register) → redirect to `/dashboard`
  - Initializes user state via `initAuth()` if a token exists but user isn't loaded

- **`admin.js`** — applied to admin pages. Redirects non-admins to their role-based dashboard (`/dashboard/landlord` or `/dashboard/rental`), never back to a route that would re-trigger the same check (avoids redirect loops).

- **`role.js`** — applied to `/dashboard/*` pages. Ensures `landlord` users can't access `/dashboard/rental/*` and vice versa; admins are redirected to `/admin`.

> **Note**: when combining `admin.js` and `role.js` across layouts, make sure redirect targets never loop back into a route protected by the same middleware that triggered the redirect.

## Security Notes

- Tokens are stored in `useCookie()` (JS-readable), **not `httpOnly`**. This is the standard SPA Bearer-token tradeoff — it allows the frontend to attach `Authorization` headers manually and run the refresh-token flow, but means an XSS vulnerability could expose tokens. Mitigations:
  - Strict Content-Security-Policy headers (recommended, not yet configured)
  - Sanitize all user-generated content before rendering
  - Keep dependencies updated
- `Secure` flag is enabled in production (`secure: process.env.NODE_ENV === 'production'`)
- `SameSite: 'lax'` on all auth cookies
- Refresh tokens are rotated on every use (single-use, deleted and reissued)

## Known Issues / TODO

- [ ] Email verification not yet implemented — accounts activate immediately on role assignment
- [ ] CSP headers not yet configured
- [ ] Confirm `pages/user/index.vue` vs `pages/dashboard/*` — possible duplicate, needs cleanup
- [ ] 2FA planned for future release