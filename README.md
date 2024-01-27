
# Restaurant Reservation System API

Welcome to the Restaurant Reservation System API! This API is designed to handle restaurant reservations, allowing users to book tables either online or offline. Online reservations can be made by customers, while offline bookings are restricted to admin users. The system is built using Laravel 10, PHP 8.1, MySQL database, and utilizes Laravel Passport for authentication.

## Getting Started

### Prerequisites
- Laravel 10
- PHP 8.1
- MySQL 8.0


### Installation

1. Clone the repository:

```bash
git clone https://github.com/yrehan32/restaurant-reservation-system.git

cd restaurant-reservation-system

```


2. Install dependencies:

```bash
composer install

```


3. Create a copy of the `.env.example` file and rename it to `.env` and `.env.testing`. Update the database configuration and other relevant settings:

```bash
cp .env.example .env

cp .env.example .env.testing

```

4. Generate application key:

```bash
php artisan key:generate

```


5. Run migrations of the database:

```bash
php artisan migrate

```


6. Install Laravel Passport:

```bash
php artisan passport:install

```


7. Start the development server:
```bash
php artisan serve

```

Your API is now accessible at http://localhost:8000.
## Testing
```bash
php artisan test

```

## Database Mapping
![Database Mapping.](https://raw.githubusercontent.com/yrehan32/restaurant-reservation-system/main/db-map.png)

## API Documentation

[Documentation](https://www.postman.com/yr-team/workspace/yr-workspace/collection/14575963-4efcc41a-f836-482d-96af-b24243e71bb1?action=share&creator=14575963)

