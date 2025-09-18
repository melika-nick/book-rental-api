# Book Rental API

A library management and book rental system built with **Laravel 12** and **Sanctum**.  
Users can register, log in, browse books, borrow, and return them.  
Admins can manage books and monitor rentals.

---

## Installation & Setup

### Requirements
- PHP ^8.2
- Composer
- MySQL

### Clone the repository
```bash
git clone https://github.com/melika-nick/book-rental-api.git
cd book-rental-api
```

### Install dependencies
```bash
composer install
```

### Create `.env` file
```bash
cp .env.example .env
```

### Configure environment variables
Edit `.env` and set your database and app settings:
```env
APP_NAME=BookRentalAPI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookrental
DB_USERNAME=root
DB_PASSWORD=

DAILY_FINE=20000
```

### Generate application key
```bash
php artisan key:generate
```

### Run migrations and seeders
```bash
php artisan migrate --seed
```

### Start the server
```bash
php artisan serve
```

API will be accessible at:
```
http://127.0.0.1:8000/api
```

---

## API Documentation

### Postman
Import the `openapi.yaml` file from the `docs/` folder into Postman.

---

## Useful Commands

- Run tests:
```bash
php artisan test
```

---

## Features

- Authentication with Sanctum
- Book management (CRUD)
- Rental management
- Late return fine calculation
- Email notifications for overdue books
- Complete API documentation with OpenAPI
