# BTF Order Management – Laravel API + Blade Demo UI

A scalable REST API for an e-commerce order management system with inventory tracking, built using Laravel 11, JWT authentication, Service & Repository pattern, Events/Listeners, queue jobs, and PDF invoice generation. A minimal Blade + Tailwind UI is included as a demo client.

---

## 1) Project Overview & Features

### Product & Inventory Management
- Product CRUD with variants (SKU, attributes JSON, price)
- Inventory per variant with real-time updates on orders
- Low-stock alerts via event + queued email
- Bulk product import via CSV (queued job)
- Full-text search on product name & description

### Order Processing
- Create orders with multiple items
- Status workflow: pending → processing → shipped → delivered, or pending/processing/shipped → cancelled
- Inventory deduction on order confirmation
- Inventory rollback on cancellation
- PDF invoice generation
- Email notifications for order status updates

### Authentication & Authorization
- JWT (access + refresh)
- Roles: Admin, Vendor, Customer
- Admin: full access
- Vendor: manage own products and related orders
- Customer: place orders and view their history

### Architecture & Technical Design
- Laravel 11 + PHP 8.2
- Service + Repository layers
- Events & Listeners for decoupled workflows
- Queue jobs for async work (emails, CSV import)
- Transactions for data integrity
- API versioning (`/api/v1/...`)

---

## 2) Local Setup (step-by-step)

1. **Clone**
   ```bash
   git clone https://github.com/yourname/btf-order-api.git
   cd btf-order-api
   ```
2. **Install dependencies**
   ```bash
   composer install
   ```
3. **Env + keys**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```
4. **Database**
   - Create MySQL DB `btf_order_api`
   - Set DB creds in `.env` (see section 3)
   - Run migrations + seeders (roles/users + sample data):
     ```bash
     php artisan migrate --seed
     ```
5. **Queue**
   - Local simple: `QUEUE_CONNECTION=sync`
   - Recommended: `QUEUE_CONNECTION=database`
     ```bash
     php artisan queue:table
     php artisan migrate
     php artisan queue:work
     ```
     Keep `queue:work` running to process emails/CSV imports.
6. **Mail**
   - Dev: `MAIL_MAILER=log` (emails go to `storage/logs/laravel.log`)
   - SMTP: configure host/port/user/pass and `MAIL_FROM_*`
7. **Run app**
   ```bash
   php artisan serve
   ```
   - API: `http://localhost:8000/api/v1`
   - Demo UI: `http://localhost:8000/`

---

## 3) Environment Variables

```env
APP_NAME="BTF Order API"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=btf_order_api
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=your_generated_jwt_secret   # set by `php artisan jwt:secret`
JWT_TTL=60                             # access token lifetime (minutes)
JWT_REFRESH_TTL=20160                  # refresh token lifetime (minutes)

QUEUE_CONNECTION=database              # or sync for quick local use

MAIL_MAILER=log                        # or smtp
MAIL_HOST=your_mail_host
MAIL_PORT=your_mail_port
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="BTF Order API"
```

---

## 4) API Authentication Guide (JWT)

**Register**
```http
POST /api/v1/auth/register
```
Body:
```json
{
  "name": "Admin User",
  "email": "admin@example.com",
  "password": "password",
  "password_confirmation": "password",
  "role": "Admin"
}
```

**Login**
```http
POST /api/v1/auth/login
```
Body:
```json
{ "email": "admin@example.com", "password": "password" }
```

**Use token**
```
Authorization: Bearer <access_token>
```

**Refresh**
```http
POST /api/v1/auth/refresh
```

**Logout**
```http
POST /api/v1/auth/logout
```

---

## 5) Testing Instructions

### Automated tests
```bash
php artisan test
```
Or target a class:
```bash
php artisan test --filter=AuthTest
php artisan test --filter=ProductApiTest
php artisan test --filter=OrderApiTest
```
Ensure `.env.testing` points to a test DB.

### Manual (Postman)
1. Import `postman/collection.json`
2. Set `base_url` variable (e.g., `http://localhost:8000/api/v1`)
3. Call auth (register/login), copy `access_token` to `token`
4. Exercise Products, Variants, Inventory, Orders (status updates, invoice PDF)

### Background checks
- Low stock: set `stock <= low_stock_threshold` and check log/inbox
- Order status emails: `POST /orders/{id}/status`
- CSV import: `POST /products/import` with CSV, monitor created products

---

## 6) API Documentation

- OpenAPI/Swagger spec: `docs/openapi.yaml`
- Postman collection: `postman/collection.json`
- Auth: `Authorization: Bearer <token>`

---

## 7) Frontend (Blade + Tailwind)

- **Storefront**: `GET /store` — customer-facing experience. Login (JWT), browse products/variants (live API), add to cart, checkout (POST `/api/v1/orders`), and track your recent orders (GET `/api/v1/orders`).
- **Admin UI**: `GET /products`, `GET /orders` — basic CRUD/overview.
- **Auth for UI**: use seeded users (e.g., `customer@example.com` / `password`) or register via `/store` quick register.

---

## 8) High-Level API Summary

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

## 9) Architecture Notes (Performance & Ops)

- **Queues**: `QUEUE_CONNECTION=database` locally; run `php artisan queue:table && php artisan migrate` and keep `php artisan queue:work` running. For production, move to Redis/SQS and supervise workers.
- **Rate limiting**: default API throttle (60 req/min) via `throttle:api`; adjust in `RouteServiceProvider` or add a custom limiter env if stricter limits are needed.
- **Caching**: database cache driver by default; switch to Redis with `CACHE_STORE=redis` for higher throughput. Cache prefix derives from `APP_NAME` to avoid collisions.
- **Indexes**: products indexed on name/slug plus full-text on name/description; orders indexed on order_number/status.
- **Database sharding strategy**: start single-node MySQL. For scale, shard by a consistent hash of `customer_id` to keep each customer’s history co-located. Maintain a shard-map table/service and have repositories resolve the shard before queries. Background jobs (emails, imports) should also resolve shards before accessing data.
- **PDF/Email**: invoices via DomPDF; emails are queued. Configure `MAIL_MAILER` appropriately.

---

## 10) About the Author

- **Name:** Your Name  
- **Email:** your.email@example.com  
- **GitHub:** https://github.com/your-github-username  

---

## 11) License

MIT License.
