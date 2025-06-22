# Job Order Statement (JOS) Management System
A Laravel-based mini application to manage Job Orders (JO) and generate Job Order Statements (JOS) by grouping similar job orders by **Contractor** and **Conductor**.

## Features
- Type of Work, Contractor, Conductor management (CRUD)
- Job Orders management with auto-generated reference numbers
- Job Order Statement (JOS) creation:
  - Grouped by Contractor, Conductor, and JOS Date
  - Calculates total amount using: `Actual Work × Rate`
  - Allows setting paid & balance amount
- JOS PDF export
- Month-based filtering
- Role-based access with Sanctum
- Soft deletes (optional)
- Paid/balance amount entry
-Token-based secure API with AJAX Blade UI
-Role-based separation
-Hashid obfuscation

---

## Requirements

- PHP >= 8.2
- Composer
- MySQL
- Laravel 12 & npm (for frontend assets if needed)
- Sanctum ^4.0
- Spatie Roles ^6.20
- DOMPDF *
- Hashids ^13.0

---

## Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/Cemp412/job_order_system.git
   cd jos-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run dev
   ```
3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Edit `.env` and update your database credentials:
   ```
   DB_DATABASE=your_db
   DB_USERNAME=your_user
   DB_PASSWORD=your_password
   ```

4. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

5. **Serve the application**
   ```bash
   php artisan serve
   ```

6. **Access App**
   - Visit: `http://localhost:8000`
   - Default login credentials (if seeded):
     - **Email**: `admin@example.com`
     - **Password**: `admin@123`

---

##  API Testing

Use Postman or similar tool with your **Bearer Token** (generated via login or `auth()->user()->createToken()`).

---

