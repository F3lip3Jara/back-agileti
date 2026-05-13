<div align="center">
  <h1>🚀 Agileti ERP - Backend API</h1>
  <p><strong>A Robust, Secure, and High-Performance Laravel RESTful API</strong></p>
  
  [![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
  [![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
  [![Sanctum](https://img.shields.io/badge/Auth-Sanctum-4CAF50?style=for-the-badge&logo=laravel&logoColor=white)]()
</div>

---

## 📖 Overview

This repository contains the **Backend API** for the **Agileti ERP** system. Built on top of **Laravel 12**, it serves as the core engine that powers the entire Agileti ecosystem, including the Angular frontend, mobile PDA applications, and integrations with external services like WooCommerce.

It is designed following Clean Architecture principles, ensuring scalability, secure data handling, and optimized performance for enterprise-level operations.

## ✨ Key Features

- 🔐 **Authentication & Authorization**: Stateless JWT authentication via Laravel Sanctum, with granular role-based access control (RBAC).
- 🛒 **WooCommerce Integration**: Real-time two-way synchronization of products, orders, and inventory via webhooks and REST API.
- 🏭 **WMS & Production Core**: Complex business logic handling for warehouse management, inventory movements, and production planning.
- 🖨️ **Document Generation**: Automated generation of PDF reports (DomPDF) and barcode labels.
- ⚡ **Asynchronous Processing**: Background job processing via Laravel Queues for heavy tasks like SAP/ERP synchronization.
- 🛠️ **System Monitoring**: Integrated with Laravel Telescope for deep insights into requests, queries, and errors.

## 🛠️ Tech Stack

- **Framework**: [Laravel 12](https://laravel.com/)
- **Language**: PHP 8.2+
- **Database**: MySQL / PostgreSQL / SQLite
- **Security**: Laravel Sanctum, CORS configuration
- **Tools**: DomPDF, Telescope, PHPUnit

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.2
- Composer >= 2.0
- MySQL >= 8.0 (or equivalent)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/agileti-backend.git
   cd agileti-backend
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure to configure your database connection and WooCommerce API credentials in the `.env` file.*

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the Development Server:**
   ```bash
   php artisan serve
   ```

### Running Queues & Workers

For asynchronous tasks (like email sending and synchronization), start the queue worker:
```bash
php artisan queue:work
```

## 🔌 API Architecture

The API follows strict RESTful conventions. Responses are standardized using Laravel API Resources.

- **Authentication**: `POST /api/auth/login`
- **Dashboard Stats**: `GET /api/dashboard/stats`
- **WMS Endpoints**: `GET /api/warehouse/items`, `POST /api/warehouse/move`
- **WooCommerce Webhooks**: `POST /webhooks/woocommerce/order/created`

*A complete Postman / OpenAPI collection is available upon request.*

## 🧪 Testing

We ensure code reliability through comprehensive testing:

```bash
# Run unit and feature tests
php artisan test

# Run tests with coverage
php artisan test --coverage
```

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---
<div align="center">
  <sub>Built with ❤️ by Agileti Team.</sub>
</div>
