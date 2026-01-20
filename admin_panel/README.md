# Admin Panel - Game Bundle

This is the backend administration panel for the Game Bundle application. It is built with Laravel.

## Requirements

- PHP >= 8.2
- Composer
- MySQL Database
- BCMath PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Installation

1.  **Clone or Download** the repository.
2.  **Navigate** to the `admin_panel` directory.
3.  **Install Dependencies**:
    ```bash
    composer install
    npm install
    ```
4.  **Environment Setup**:
    - Copy `.env.example` to `.env`:
      ```bash
      cp .env.example .env
      ```
    - Open `.env` and configure your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    - Set `APP_URL` to your domain or local IP (e.g., `http://your-domain.com`).

5.  **Generate Key**:
    ```bash
    php artisan key:generate
    ```

6.  **Run Migrations & Seeders**:
    ```bash
    php artisan migrate --seed
    ```
    *Note: This will install the default admin user and settings.*

7.  **Build Assets**:
    ```bash
    npm run build
    ```

## Usage

-   **Admin Login**: Go to `/admin/login`.
-   **Default Credentials**:
    -   Email: `admin@admin.com`
    -   Password: `password`

## Cron Jobs

For scheduled tasks (if any), add the following cron entry:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Security

-   Ensure `APP_DEBUG` is set to `false` in production.
-   Change the default admin password immediately after logging in.
