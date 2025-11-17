# BTF Order Management – Laravel API + Blade Demo UI

A scalable REST API for an e-commerce order management system with inventory tracking, built using **Laravel 11**, **JWT authentication**, **Service & Repository pattern**, **Events/Listeners**, **Queue jobs**, and **PDF invoice generation**. A minimal Blade + Tailwind UI is included as a demo client.

---

## 1. Project Overview & Features

### Core Features

#### 1. Product & Inventory Management
- Product CRUD (API-based)
- Product variants (SKU, attributes JSON, variant price)
- Inventory per variant
- Real-time inventory updates on orders
- Low-stock alerts via **event + queued email**
- Bulk product import via CSV (queued job)
- Full-text search on product name & description

#### 2. Order Processing
- Create orders with multiple items (product + variant)
- Status workflow (state machine):
  - `pending → processing → shipped → delivered`
  - `pending/processing/shipped → cancelled`
- Inventory deduction on order creation
- Inventory rollback on cancellation
- PDF invoice generation for each order
- Email notifications for order status updates

#### 3. Authentication & Authorization
- JWT-based API authentication (access token + refresh)
- Roles: **Admin**, **Vendor**, **Customer**
- Admin: full access
- Vendor: manage own products & orders
- Customer: place orders & view own order history

#### 4. Architecture & Technical Design
- Laravel 11 + PHP 8.2+
- Service layer for business logic
- Repository layer for data access
- Events & Listeners for decoupled workflows
- Queue jobs for async operations (emails, CSV import)
- Database transactions for data integrity
- API versioning (`/api/v1/...`)

---

## 2. Local Setup Instructions (Step-by-Step)

### 2.1. Clone the repository

```bash
git clone https://github.com/yourname/btf-order-api.git
cd btf-order-api
```

### 2.2. Install dependencies

```bash
composer install
```

### 2.3. Create and configure environment file

```bash
cp .env.example .env
php artisan key:generate
```

Then open `.env` and set the following variables (see Section 3 for details).

### 2.4. Database setup

Create a new MySQL database (example: `btf_order_api`) and configure `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=btf_order_api
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

### 2.5. JWT setup

Generate the JWT secret key:

```bash
php artisan jwt:secret
```

This will populate `JWT_SECRET` in `.env`.

### 2.6. Queue setup

For local development you can either use `sync` (simpler) or `database` (recommended).

**Option A – Simple (no worker needed):**

```env
QUEUE_CONNECTION=sync
```

**Option B – Recommended (queued jobs):**

```env
QUEUE_CONNECTION=database
```

Then:

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

Keep `php artisan queue:work` running in a separate terminal to process emails and CSV imports.

### 2.7. Mail setup

For local testing you can use the log mailer:

```env
MAIL_MAILER=log
```

All outgoing emails will be written to `storage/logs/laravel.log`.

For real SMTP, configure:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # or other
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="BTF Order API"
```

### 2.8. Run the application

```bash
php artisan serve
```

The API will be available at:

```
http://localhost:8000/api/v1
```

The Blade demo UI will be available at:

```
http://localhost:8000/
```

---

## 3. Environment Variables Documentation

Key environment variables used in this project:

### Application

```env
APP_NAME="BTF Order API"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost
```

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=btf_order_api
DB_USERNAME=root
DB_PASSWORD=
```

### JWT Authentication

```env
JWT_SECRET=your_generated_jwt_secret   # set by `php artisan jwt:secret`
JWT_TTL=60                             # access token lifetime (minutes)
JWT_REFRESH_TTL=20160                  # refresh token lifetime (minutes)
```

### Queue

```env
QUEUE_CONNECTION=database  # or sync for simple local testing
```

### Mail

```env
MAIL_MAILER=log             # or smtp
MAIL_HOST=your_mail_host
MAIL_PORT=your_mail_port
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="BTF Order API"
```

You can add additional environment variables as needed, but these are the core ones.

---

## 4. API Authentication Guide

The API uses **JWT bearer tokens**.

### 4.1. Register

**Endpoint:**

```http
POST /api/v1/auth/register
Content-Type: application/json
Accept: application/json
```

**Body example:**

```json
{
  "name": "Admin User",
  "email": "admin@example.com",
  "password": "password",
  "password_confirmation": "password",
  "role": "Admin"
}
```

**Response:**

```json
{
  "access_token": "JWT_TOKEN_HERE",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "Admin"
  }
}
```

### 4.2. Login

```http
POST /api/v1/auth/login
Content-Type: application/json
Accept: application/json
```

**Body:**

```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

The response contains `access_token` exactly like register.

### 4.3. Using the token

For all protected endpoints, send the token in the `Authorization` header:

```http
Authorization: Bearer JWT_TOKEN_HERE
```

Example with `curl`:

```bash
curl -H "Authorization: Bearer JWT_TOKEN_HERE"      -H "Accept: application/json"      http://localhost:8000/api/v1/products
```

### 4.4. Refresh token

```http
POST /api/v1/auth/refresh
Authorization: Bearer JWT_TOKEN_HERE
Accept: application/json
```

Returns a new `access_token`.

### 4.5. Logout

```http
POST /api/v1/auth/logout
Authorization: Bearer JWT_TOKEN_HERE
Accept: application/json
```

Invalidates the token.

---

## 5. Testing Instructions

### 5.1. Running automated tests

Feature and unit tests are written using Laravel’s testing framework.

Run the full test suite:

```bash
php artisan test
```

Or run a specific test class:

```bash
php artisan test --filter=AuthTest
php artisan test --filter=ProductApiTest
php artisan test --filter=OrderApiTest
```

Make sure your `.env.testing` is configured (or phpunit.xml env overrides) to point to a dedicated test database.

### 5.2. Manual testing with Postman

1. Import the Postman collection from:

   ```text
   /postman/btf-order-api.postman_collection.json
   ```

2. Set collection variable `base_url` to your local URL, e.g. `http://localhost:8000/api/v1`.

3. First call `Auth → POST /auth/register` or `Auth → POST /auth/login`.

4. Copy `access_token` into the `token` collection variable.

5. Now you can call:
   - `Products` (CRUD, search, CSV import)
   - `Product Variants`
   - `Inventory`
   - `Orders` (create, list, update status, invoice PDF)

### 5.3. Verifying background processes

- For **low stock alerts**, update inventory so that `stock <= low_stock_threshold` and check logs or inbox.
- For **order status emails**, change order status via `/orders/{id}/status` and verify email.
- For **CSV import**, hit `/products/import` with a CSV file and monitor created products.

---

## 6. High-Level API Summary

### Authentication

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `GET  /api/v1/auth/me`
- `POST /api/v1/auth/refresh`
- `POST /api/v1/auth/logout`

### Products

- `GET    /api/v1/products?search=...`
- `POST   /api/v1/products`
- `GET    /api/v1/products/{id}`
- `PUT    /api/v1/products/{id}`
- `DELETE /api/v1/products/{id}`
- `POST   /api/v1/products/import`

### Variants

- `GET    /api/v1/products/{product}/variants`
- `POST   /api/v1/products/{product}/variants`
- `GET    /api/v1/products/{product}/variants/{variant}`
- `PUT    /api/v1/products/{product}/variants/{variant}`
- `DELETE /api/v1/products/{product}/variants/{variant}`

### Inventory

- `GET /api/v1/variants/{variant}/inventory`
- `PUT /api/v1/variants/{variant}/inventory`

### Orders

- `GET  /api/v1/orders`                – role-based (Admin/Vendor/Customer)
- `POST /api/v1/orders`                – create order
- `GET  /api/v1/orders/{order}`        – show order details
- `POST /api/v1/orders/{order}/status` – update order status
- `GET  /api/v1/orders/{order}/invoice`– download PDF invoice

---

## 7. About the Author

> ⚠️ **Replace this section with your real information before submitting.**

- **Name:** Your Name  
- **Email:** your.email@example.com  
- **GitHub:** https://github.com/your-github-username  

---

## 8. License

This project is open-sourced under the **MIT License**.
