# Leo Naiyem Portfolio API

Laravel 13 backend for the portfolio site: a read-only JSON API, a contact form endpoint, and a Filament admin panel for managing content. The Next.js frontend lives in `leoNaiyem_portfolio_frontend`.

## Requirements

- PHP 8.3+ with `pdo_mysql`, `intl`, `gd`, `zip`
- MySQL 8
- Composer

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# create the "leonaiyem_portfolio" database in MySQL first
php artisan migrate --seed
php artisan storage:link
php artisan make:filament-user   # your admin login

php artisan serve                # http://localhost:8000
php artisan queue:work           # in a second terminal: emails + frontend revalidation
```

Admin panel: <http://localhost:8000/admin>

### Import content from Sanity

```bash
php artisan import:sanity <sanity-project-id> --token=<read-token>
```

This copies all sections, projects, tags and old contact messages, and downloads every image to the media disk. Running it again updates records instead of duplicating them.

## API

All endpoints are under `/api/v1`. GET responses are public, carry `Cache-Control: public, max-age=300` and an `ETag`.

| Method | Path | Notes |
|---|---|---|
| GET | `/settings` | Name, title, bio, avatar, resume URL, socials, SEO defaults |
| GET | `/abouts` | |
| GET | `/projects?tag={slug}` | Published projects with tags (no `body`) |
| GET | `/projects/{slug}` | One published project, including the Markdown `body` |
| GET | `/tags` | Tags used by at least one published project |
| GET | `/skills` | |
| GET | `/experiences` | Grouped: `[{ year, items: [{ role, company, description }] }]`, newest year first |
| GET | `/testimonials` | |
| GET | `/brands` | |
| POST | `/contact` | `name`, `email`, `message`, `turnstile_token`; `website` is a honeypot and must be empty. Rate limit: 3/min and 20/day per IP |

The contact form is posted **from the visitor's browser**, so per-IP rate limiting and Turnstile see the real visitor. CORS only allows `FRONTEND_URL` (or `CORS_ALLOWED_ORIGINS`).

## Frontend revalidation

Saving, deleting or reordering content in the admin queues `App\Jobs\RevalidateFrontend`. The job calls:

```
POST {FRONTEND_URL}/api/revalidate
x-revalidate-secret: {REVALIDATE_SECRET}
{ "tag": "projects" }
```

The tag is the table name (`settings`, `abouts`, `projects`, `tags`, `skills`, `experiences`, `testimonials`, `brands`). Jobs are debounced for 5 seconds per tag. Nothing is sent while `REVALIDATE_SECRET` is empty.

## Environment

| Variable | Purpose |
|---|---|
| `FRONTEND_URL` | Next.js site URL (CORS + revalidation target) |
| `REVALIDATE_SECRET` | Shared secret; set the same value in the Next.js app |
| `ADMIN_EMAIL` | Receives contact form notifications |
| `TURNSTILE_SECRET_KEY` | Cloudflare Turnstile; empty disables the captcha |
| `MEDIA_DISK` | `public` (local) or `s3` (S3 / Cloudflare R2) |

## Production

Run a queue worker (`php artisan queue:work`) and the scheduler (`php artisan schedule:run` every minute), and configure real `MAIL_*` settings. If the app sits behind a load balancer or Cloudflare, configure trusted proxies so the contact rate limit sees visitor IPs.

## Tests

```bash
php artisan test
```
"# LeoNaiyem-portfolio-api" 
