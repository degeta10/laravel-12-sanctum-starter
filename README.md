# Laravel 12 Sanctum Starter

A production-ready Laravel 12 starter project with Laravel Sanctum authentication, API versioning, and Docker support using Laravel Sail.

## Features

- ✅ Laravel 12 Framework
- ✅ Laravel Sanctum API Authentication
- ✅ API Versioning (v1)
- ✅ User Authentication (Login, Register, Logout)
- ✅ User Profile Management
- ✅ Docker Support with Laravel Sail
- ✅ MySQL Database
- ✅ Redis Cache
- ✅ Meilisearch Integration
- ✅ Mailpit for Email Testing
- ✅ PHPUnit Testing Setup
- ✅ Laravel Pint Code Styling
- ✅ Commitlint & Husky for Git Hooks

## Requirements

### Without Docker

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Redis (optional)
- Node.js >= 18 & NPM

### With Docker (Recommended)

- Docker Desktop
- Docker Compose

## Installation

### Option 1: Using Laravel Sail (Docker) - Recommended

1. **Clone the repository**

    ```bash
    git clone <your-repository-url>
    cd laravel-12-sanctum-starter
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Copy environment file**

    ```bash
    cp .env.example .env
    ```

4. **Generate application key**

    ```bash
    php artisan key:generate
    ```

5. **Start Docker containers**

    ```bash
    ./vendor/bin/sail up -d
    ```

6. **Run migrations**

    ```bash
    ./vendor/bin/sail artisan migrate
    ```

7. **Install Node dependencies and build assets**

    ```bash
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run build
    ```

8. **Your application is ready!**
    - API: http://localhost
    - Mailpit: http://localhost:8025
    - Meilisearch: http://localhost:7700

### Option 2: Local Installation (Without Docker)

1. **Clone the repository**

    ```bash
    git clone <your-repository-url>
    cd laravel-12-sanctum-starter
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configure your database**

    Update the following in your `.env` file:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

5. **Run migrations**

    ```bash
    php artisan migrate
    ```

6. **Build frontend assets**

    ```bash
    npm run build
    ```

7. **Start the development server**

    ```bash
    php artisan serve
    ```

8. **Your application is ready!**
    - API: http://localhost:8000

## Configuration

### Environment Variables

Key environment variables to configure:

```env
# Application
APP_NAME=Laravel
APP_ENV=local
APP_URL=http://localhost

# Database (for Sail/Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# Sanctum & CORS
SANCTUM_STATEFUL_DOMAINS=localhost:5173
CORS_ALLOWED_ORIGINS="http://localhost:5173"
```

### Sail Alias (Optional)

Add to your shell profile (`.bashrc`, `.zshrc`, etc.):

```bash
alias sail='./vendor/bin/sail'
```

Then you can use `sail` instead of `./vendor/bin/sail`.

## API Documentation

### Base URL

- Local: `http://localhost/api/v1`
- With Sail: `http://localhost/api/v1`

### Authentication Endpoints

#### Register

```http
POST /api/v1/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Get Authenticated User

```http
GET /api/v1/me
Authorization: Bearer {token}
```

#### Update Authenticated User

```http
PATCH /api/v1/me
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Updated",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

#### Logout

```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

### User Endpoints

#### Get User Profile

```http
GET /api/v1/me
Authorization: Bearer {token}
```

#### Update User Profile

```http
PATCH /api/v1/me
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Updated"
}
```

## Development

### Composer Scripts

#### Setup Project

```bash
composer setup
```

Runs: install dependencies, copy .env, generate key, run migrations, install npm packages, build assets

#### Run in Development Mode

```bash
composer dev
```

Runs: server, queue worker, logs (Pail), and Vite concurrently

#### Run Tests

```bash
composer test
```

### Sail Commands

```bash
# Start containers
sail up -d

# Stop containers
sail down

# Run artisan commands
sail artisan migrate
sail artisan tinker

# Run Composer commands
sail composer install

# Run NPM commands
sail npm install
sail npm run dev

# Run tests
sail test
sail artisan test

# Access MySQL
sail mysql

# View logs
sail artisan pail
```

### Code Style

Format code using Laravel Pint:

```bash
./vendor/bin/pint
# or with Sail
sail pint
```

## Testing

Run PHPUnit tests:

```bash
# Local
php artisan test

# With Sail
sail artisan test
# or
sail test
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/
│   │       ├── AuthController.php
│   │       └── UserController.php
│   ├── Requests/
│   └── Resources/
├── Actions/
│   └── Auth/
│       ├── ApiLoginUserAction.php
│       ├── ApiLogoutUserAction.php
│       ├── WebLoginUserAction.php
│       └── WebLogoutUserAction.php
├── Models/
│   └── User.php
└── Providers/
    ├── AppServiceProvider.php
    └── ResponseMacroServiceProvider.php

routes/
├── api.php
├── api_v1.php
└── web.php
```

## Git Hooks

This project uses Husky and Commitlint to enforce conventional commits:

```bash
# Will be validated on commit
git commit -m "feat: add user registration"
git commit -m "fix: resolve login issue"
git commit -m "docs: update README"
```

Commit types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

## Troubleshooting

### Permission Issues (Docker/Sail)

```bash
sudo chown -R $USER:$USER .
```

### Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Reset Database

```bash
php artisan migrate:fresh --seed
# or with Sail
sail artisan migrate:fresh --seed
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
