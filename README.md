<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<h1 align="center">Learn Docker</h1>

<p align="center">
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel" alt="Laravel"></a>
  <a href="https://www.docker.com/"><img src="https://img.shields.io/badge/Docker-Compose-2496ED?style=flat-square&logo=docker" alt="Docker"></a>
  <a href="https://www.postgresql.org/"><img src="https://img.shields.io/badge/PostgreSQL-16-4169E1?style=flat-square&logo=postgresql" alt="PostgreSQL"></a>
  <a href="https://nginx.org/"><img src="https://img.shields.io/badge/Nginx-Alpine-009639?style=flat-square&logo=nginx" alt="Nginx"></a>
</p>

<p align="center">
  Laravel project containerized with Docker — PHP-FPM, Nginx, and PostgreSQL.
  <br>
  Built as a hands-on learning project for Docker and container orchestration.
</p>

---

## About

This project demonstrates how to containerize a Laravel 13 application using Docker Compose with a production-like stack:

- **PHP 8.4-FPM** — PHP runtime with FastCGI Process Manager
- **Nginx** — Lightweight reverse proxy and static file server
- **PostgreSQL 16** — Relational database with persistent storage
- **Vite 8 + Tailwind CSS 4** — Modern frontend tooling

All three services are orchestrated via `docker-compose.yml` and communicate over a Docker bridge network.

## Tech Stack

| Component       | Technology    | Version   | Description                          |
| --------------- | ------------- | --------- | ------------------------------------ |
| Language        | PHP           | 8.4       | FPM variant for Nginx integration    |
| Framework       | Laravel       | 13.31     | Full-stack PHP framework             |
| Web Server      | Nginx         | Alpine    | Lightweight reverse proxy            |
| Database        | PostgreSQL    | 16        | Primary database (with pgsql driver) |
| CSS Framework   | Tailwind CSS  | 4.0       | Utility-first CSS                    |
| Build Tool      | Vite          | 8.0       | Frontend bundler with HMR            |
| PHP Extensions  | pdo, pdo_pgsql | -       | PostgreSQL connectivity              |
| Composer        | Composer      | 2.x       | PHP dependency manager               |

## Prerequisites

Make sure you have the following installed on your machine:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) or [Docker Engine](https://docs.docker.com/engine/install/) (Linux)
- [Docker Compose](https://docs.docker.com/compose/install/) v2+ (included with Docker Desktop)

Verify your installation:

```bash
docker --version
docker compose version
```

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/your-username/learn-docker.git
cd learn-docker

# 2. Copy environment file
cp .env.example .env

# 3. Build and start all containers
docker compose up --build

# 4. In a new terminal, run database migration
docker compose exec app php artisan migrate

# 5. Visit the application
open http://localhost:8000
```

The application is now running at **http://localhost:8000** with a PostgreSQL database connected and ready.

## Project Structure

```
learn-docker/
├── app/                        # Laravel application code
│   ├── Http/Controllers/       # HTTP controllers
│   └── Models/                 # Eloquent models
├── bootstrap/                  # Application bootstrap
├── config/                     # Configuration files
├── database/                   # Migrations, factories, seeders
├── nginx/                      # Nginx configuration
│   └── default.conf            # Server block & PHP-FPM proxy config
├── public/                     # Web root (entry point)
├── resources/                  # Views, CSS, JS
├── routes/                     # Route definitions
├── storage/                    # Logs, cache, compiled views
├── tests/                      # PHPUnit test suites
├── vendor/                     # Composer dependencies (git-ignored)
│
├── docker-compose.yml          # Multi-container orchestration
├── Dockerfile                  # PHP-FPM image definition
├── composer.json               # PHP dependencies
├── package.json                # Node dependencies (Vite, Tailwind)
├── vite.config.js              # Vite build configuration
├── phpunit.xml                 # Test configuration
└── .env                        # Environment variables (git-ignored)
```

## Docker Architecture

The project runs **3 containers** orchestrated by Docker Compose:

```
┌─────────────────────────────────────────────────┐
│                  Docker Network                 │
│                                                 │
│  ┌──────────┐   ┌──────────┐   ┌────────────┐  │
│  │  Nginx   │   │   App    │   │  Postgres  │  │
│  │  :8000   │──▶│  :9000   │   │  :5432     │  │
│  │ (alpine) │   │ (PHP-FPM)│   │  (v16)     │  │
│  └──────────┘   └──────────┘   └────────────┘  │
│       │              │               │          │
│       ▼              ▼               ▼          │
│    Browser      PHP-FPM          Database       │
│   Request     Processes         Persistent      │
│             PHP scripts          Volume         │
└─────────────────────────────────────────────────┘
```

### Services

| Service    | Container Name       | Port Mapping          | Description                      |
| ---------- | -------------------- | --------------------- | -------------------------------- |
| `nginx`    | learn-docker-nginx-1 | `8000` → `80`         | Web server, entry point          |
| `app`      | learn-docker-app-1   | internal only         | PHP-FPM, runs PHP scripts        |
| `postgres` | laravel-postgres     | `5432` → `5432`       | PostgreSQL database              |

### Port Mapping

| Port   | Service    | Purpose                              |
| ------ | ---------- | ------------------------------------ |
| `8000` | Nginx      | Application URL (http://localhost:8000) |
| `5432` | PostgreSQL | Direct DB access from host           |
| `9000` | PHP-FPM    | FastCGI internal (not exposed to host) |

### Volumes

| Volume           | Mount Point              | Purpose                     |
| ---------------- | ------------------------ | --------------------------- |
| Project source   | `.:/var/www`             | Live code sync in containers |
| `postgres_data`  | `/var/lib/postgresql/data` | Persistent database storage |
| Nginx config     | `./nginx/default.conf`   | Custom server configuration  |

## Configuration

### Environment Variables (`.env`)

Key variables configured for Docker:

```env
# Application
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (PostgreSQL via Docker)
DB_CONNECTION=pgsql
DB_HOST=postgres          # Docker service name
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

# Session & Cache (database-backed)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

> **Note:** The `DB_HOST` value `postgres` resolves to the PostgreSQL container via Docker's internal DNS. Do not change this to `localhost` or `127.0.0.1` — that would try to connect to the host machine instead.

### Database Credentials

| Parameter  | Value      |
| ---------- | ---------- |
| Host       | `postgres` |
| Port       | `5432`     |
| Database   | `laravel`  |
| Username   | `laravel`  |
| Password   | `secret`   |

## Common Commands

### Docker

```bash
# Start all containers (with build)
docker compose up --build

# Start in background (detached mode)
docker compose up -d --build

# Stop all containers
docker compose down

# Stop and remove volumes (fresh start)
docker compose down -v

# View running containers
docker compose ps

# View logs (all services)
docker compose logs -f

# View logs (specific service)
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f postgres
```

### Laravel Artisan

```bash
# Run artisan commands inside the app container
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan tinker
docker compose exec app php artisan cache:clear
docker compose exec app php artisan key:generate
docker compose exec app php artisan route:list
docker compose exec app php artisan about
```

### Composer

```bash
# Install PHP dependencies
docker compose exec app composer install

# Add a new package
docker compose exec app composer require package-name

# Add a dev dependency
docker compose exec app composer require package-name --dev
```

### Frontend (Node / Vite)

```bash
# Install Node dependencies (on host)
npm install

# Start Vite dev server with hot reload (on host)
npm run dev

# Build for production (on host)
npm run build
```

### Testing

```bash
# Run all tests (inside container)
docker compose exec app php artisan test

# Run specific test file
docker compose exec app php artisan test tests/Feature/ExampleTest.php

# Run with PHPUnit directly
docker compose exec app ./vendor/bin/phpunit
```

## Database Access

### From Inside Containers

The `app` container connects to PostgreSQL using the Docker service name `postgres`:

```
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
```

### From Host Machine

You can connect to PostgreSQL from your host using any database client:

| Parameter | Value        |
| --------- | ------------ |
| Host      | `localhost`  |
| Port      | `5432`       |
| Database  | `laravel`    |
| Username  | `laravel`    |
| Password  | `secret`     |

Recommended clients:
- [DBeaver](https://dbeaver.io/) — Free, universal database tool
- [TablePlus](https://tableplus.com/) — Modern GUI client
- [pgAdmin](https://www.pgadmin.org/) — PostgreSQL official admin tool
- [psql](https://www.postgresql.org/docs/current/app-psql.html) — CLI tool

```bash
# Quick connection via CLI
psql -h localhost -p 5432 -U laravel -d laravel
```

## Database Management

### Migrations

The project uses Laravel migrations for database schema management. All migrations are located in `database/migrations/`:

| Migration | Description |
| --------- | ----------- |
| `0001_01_01_000000_create_users_table.php` | Users table (id, name, email, email_verified_at, password, remember_token, two_factor_secret, two_factor_recovery_codes, current_team_id, profile_photo_path, created_at, updated_at) |
| `0001_01_01_000001_create_cache_table.php` | Cache table (key, value, expiry) |
| `0001_01_01_000002_create_jobs_table.php` | Jobs table for queue processing |

**Running migrations:**

```bash
# Run all pending migrations
docker compose exec app php artisan migrate

# Reset and re-run with fresh data
docker compose exec app php artisan migrate:fresh --seed

# Force reset all tables and re-migrate
docker compose exec app php artisan migrate:reset
```

### Seeders

Database seeders populate initial data. The main seeder is `DatabaseSeeder.php`:

```bash
# Run the database seeder
docker compose exec app php artisan db:seed

# Run with fresh migration
docker compose exec app php artisan migrate:fresh --seed
```

**Table seeders available:**
- `UsersTableSeeder` - Creates a test user (`test@example.com`)

### Database Structure Overview

The PostgreSQL database contains the following tables:

| Table | Primary Key | Key Columns | Description |
| ----- | ----------- | ----------- | ----------- |
| `users` | `id` (bigint) | `email`, `name`, `created_at` | Application users, authentication |
| `cache` | composite | `key`, `value` | Cache storage (default: database driver) |
| `jobs` | bigint | `queue`, `payload`, `attempts` | Queued jobs for background processing |

**Entity Relationship:**
- `users` - One-to-many relationship with potential models/responses
- `cache` - Key-value pairs for session/cache storage
- `jobs` - Job queue for Laravel's queue system

### Connection Details

**From Host Machine:**

```bash
# Using psql CLI
psql -h localhost -p 5432 -U laravel -d laravel

# Using DBeaver/TablePlus/pgAdmin:
# Host: localhost
# Port: 5432
# Database: laravel
# Username: laravel
# Password: secret
```

**From Inside Docker Containers:**

```bash
# Access via Docker network
docker compose exec app psql -h postgres -U laravel -d laravel

# Or via the app container's internal DB config
# DB_HOST=postgres
# DB_PORT=5432
# DB_DATABASE=laravel
```

### Common Database Operations

```bash
# List all tables
docker compose exec app psql -U laravel -d laravel -c "\dt"

# Describe table structure
docker compose exec app psql -U laravel -d laravel -c "\d users"

# Count rows in users table
docker compose exec app psql -U laravel -d laravel -c "SELECT COUNT(*) FROM users;"

# View all users
docker compose exec app psql -U laravel -d laravel -c "SELECT * FROM users;"

# Create new database (if needed)
docker compose exec app psql -U postgres -c "CREATE DATABASE new_db_name;"

# Drop database
docker compose exec app psql -U postgres -c "DROP DATABASE db_name;"
```

### GUI Clients Recommendation

For visual database management, these clients work well with the project's PostgreSQL setup:

| Client | URL | Notes |
| ------ | -------- | -------- |
| **DBeaver** | <https://dbeaver.io> | Free, universal, supports PostgreSQL |
| **TablePlus** | <https://tableplus.com> | Modern GUI, native performance |
| **pgAdmin** | <https://www.pgadmin.org> | Official PostgreSQL management tool |
| **DataGrip** | <https://www.jetbrains.com/datagrip/> | Professional IDE with DB tools |

### Backup & Restore

```bash
# Backup database
docker compose exec postgres pg_dump -U laravel -d laravel > backup.sql

# Restore database
docker compose exec -i postgres psql -U laravel -d laravel < backup.sql
```

## Frontend Development

The frontend uses **Vite** with **Tailwind CSS v4** for fast development:

- **Hot Module Replacement (HMR)**: Run `npm run dev` on your host, and Vite will watch for CSS/JS changes and inject them instantly
- **Fonts**: Uses [Bunny Fonts](https://bunny.net/fonts/) CDN for Instrument Sans (400, 500, 600)
- **Build**: Run `npm run build` to compile assets into `public/build/`

The Vite configuration is in `vite.config.js`:

```js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

## Troubleshooting

### Port Already in Use

```
Error: Bind for 0.0.0.0:8000 failed: port is already allocated
```

**Solution:** Stop the process using that port, or change the port mapping in `docker-compose.yml`:

```yaml
ports:
  - "8080:80"  # Change 8000 to 8080
```

### Database Connection Refused

```
SQLSTATE[08006] could not connect to server: Connection refused
```

**Solution:** Ensure PostgreSQL container is running:

```bash
docker compose ps
docker compose logs postgres
```

The `app` container may have started before PostgreSQL was ready. Restart:

```bash
docker compose restart app
```

### Permission Denied (Storage/Bootstrap/Cache)

```
failed to open stream: Permission denied
```

**Solution:** Fix permissions inside the container:

```bash
docker compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
```

### Containers Won't Start

```bash
# View detailed logs
docker compose logs

# Rebuild from scratch
docker compose down -v
docker compose up --build
```

### Fresh Start (Reset Everything)

```bash
# Remove all containers, volumes, and rebuild
docker compose down -v
docker compose up --build
docker compose exec app php artisan migrate --seed
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center">
  Built as a learning project for Docker containerization with Laravel.
  <br>
  <a href="https://laravel.com/docs">Laravel Docs</a> ·
  <a href="https://docs.docker.com/compose/">Docker Compose Docs</a> ·
  <a href="https://www.postgresql.org/docs/">PostgreSQL Docs</a>
</p>
