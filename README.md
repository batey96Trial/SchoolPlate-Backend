# SchoolPlate API

A Laravel-based REST API for a food donation and student assistance platform that connects donors with students in need.

## Features

- **Role-Based Authentication**: Support for Students, Donors, Restaurants, and Admins
- **API Token Authentication**: Sanctum-based token authentication with refresh token support
- **User Resource Factory**: Dynamic resource transformation based on user roles
- **Verification System**: Automatic verification workflows for students and restaurants

## Tech Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum
- **Database**: MySQL with Eloquent ORM
- **API**: RESTful JSON API with Sanctum tokens

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+

### Setup

1. **Clone the repository**
   ```bash
   git clone [text](https://github.com/batey96Trial/SchoolPlate-Backend.git)
   cd my-laravel-app

2. **Install dependencies**
    ```bash
   composer install

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database**
   
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=zerofamine
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

   The API will be available at `http://127.0.0.1:8000`

### Contributing
Thank you for considering contributing to this project. For issues and questions, please open an issue or PR on the repository.


This README provides:
- ✅ Project overview and features
- ✅ Installation instructions
- ✅ Configuration guide
- ✅ Security notes
- ✅ Development commands