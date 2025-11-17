# BTF Order Management – Laravel API + Blade Demo UI
A production-grade e-commerce order & inventory management system built with **Laravel 11**, **JWT Authentication**, **Service + Repository Pattern**, **Queue Jobs**, **Events/Listeners**, **PDF Invoice**, and **Role-based access control**.

## 🚀 Features Overview

### ✅ Authentication & Authorization
- JWT Auth (Register, Login, Refresh, Logout)
- Role-based access: **Admin**, **Vendor**, **Customer**
- Admin → full access  
- Vendor → manage own products & orders  
- Customer → can place orders & view own order history  

### 🛒 Product & Variant Management
- Full Product CRUD (API based)
- Variant support:
  - Name, SKU, attributes (JSON)
  - Variant price
  - Auto-created inventory per variant
- Full-text search (MySQL FULLTEXT Index)
  - `GET /products?search=iphone`  
- Product import via **CSV + Queue Job**

### 📦 Inventory Management
- Inventory per variant
- Real-time quantity update
- Low-stock threshold
- Automatic **Low Stock Email Alert** (Event + Listener + Queue Job)

### 🧾 Order Management
- Create order with multiple items  
- Status workflow (State Machine):
  ```
  pending → processing → shipped → delivered  
  (or cancel any step until shipped)
  ```
- Inventory deduction on order creation
- Inventory rollback on cancellation
- Order invoice PDF generation (Dompdf)
- Customer order e-mail notifications

### 📡 API Versioning
All endpoints are prefixed:

```
/api/v1/...
```

### 🖥️ Demo Blade Web UI
- Dashboard
- Product list + create form  
- Order list + create form  
- Login page  
- TailwindCSS (CDN) based modern UI

## 🛠️ Installation Guide

### 1. Clone project
```bash
git clone https://github.com/yourname/btf-order-api.git
cd btf-order-api
```

### 2. Install dependencies
```bash
composer install
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database, JWT, queue
```bash
php artisan jwt:secret
php artisan migrate --seed
php artisan queue:table
php artisan migrate
php artisan queue:work
```

### 5. Run application
```bash
php artisan serve
```

## 📦 API Summary

### Authentication
- POST `/auth/register`
- POST `/auth/login`
- GET `/auth/me`
- POST `/auth/refresh`
- POST `/auth/logout`

### Products
- GET `/products`
- POST `/products`
- GET `/products/{id}`
- PUT `/products/{id}`
- DELETE `/products/{id}`
- POST `/products/import`

### Variants
- Full CRUD under `/products/{product}/variants`

### Inventory
- GET `/variants/{id}/inventory`
- PUT `/variants/{id}/inventory`

### Orders
- Full order lifecycle
- PDF invoice: `/orders/{id}/invoice`

## 📜 License
MIT License.
