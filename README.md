# Timely - Service Booking & Job Portal

Timely is a comprehensive Laravel-based platform that connects service providers with customers and facilitates job recruitment. It serves as a dual-purpose ecosystem where users can book various services and also find or post job opportunities.

## 🚀 Key Features

### 🔐 Authentication & User Management

- **Secure Auth**: Powered by Laravel Passport.
- **Social Login**: Integration for easy access.
- **OTP Verification**: Phone number verification for security.
- **User Profiles**: Detailed profiles with education, work experience, and skills.
- **Guardians**: Guardian profile management.

### 🛠 Service Booking System

- **Service Listings**: Providers can list services with categories and prices.
- **Booking Flow**: Users can book services, view details, and track status.
- **Service Requests**: Custom service requests and provider selection.
- **Ratings & Reviews**: Quality control through user feedback.

### 💼 Job Portal

- **Job Posting**: Create and manage job listings with descriptions and requirements.
- **Job Search**: Advanced search for candidates.
- **Application System**: Users can apply for jobs and track application status.
- **Interviews**: Video interview management and slot booking.
- **Hiring Flow**: Complete lifecycle from application to hiring.

### 💳 Payments & Subscription

- **Stripe Integration**: Secure payment processing.
- **Card Management**: Save and manage cards.
- **Bank Accounts**: Manager linked bank accounts.
- **Invoicing**: Payment requests and transaction history.

## 💻 Technology Stack

- **Framework**: Laravel 11.x
- **Language**: PHP 8.2+
- **Database**: MySQL
- **Dependencies**:
  - `laravel/passport`: API Authentication
  - `stripe/stripe-php`: Payment Processing
  - `intervention/image`: Image Handling
  - `spatie/laravel-http-logger`: Logging

## ⚙️ Installation

1.  **Clone the repository**

    ```bash
    git clone <repository_url>
    cd timely
    ```

2.  **Install Dependencies**

    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup**
    Copy the example env file and configure your database and third-party credentials (Stripe, etc.).

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database Migration**

    ```bash
    php artisan migrate
    ```

5.  **Passport Setup**

    ```bash
    php artisan passport:install
    ```

6.  **Run Local Server**
    ```bash
    php artisan serve
    ```

## 📚 API Documentation

The application exposes a robust REST API for all client operations.

- **Routes**: Defined in `routes/api.php`
- **Prefix**: `/api/`

## 📝 Developer Reference

### Card Numbers (Testing)

- `30569309025904`
- `378282246310005`
- `4242424242424242` (Stripe Test)

### Booking Status Codes

- `0`: Booking Declined by Provider
- `1`: Booking Accepted by Provider
- `2`: New Booking
- `3`: Booking Completed by Provider
- `4`: Booking Cancelled by User

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
