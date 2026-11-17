# Grain & Feed POS

A Laravel POS for rice, cat and dog food, chicken feed, and pig feed sold by kilo or sack.

## Setup

The project is configured for MySQL. Create a database named `pos_laravel` in MySQL (for example, through phpMyAdmin), then set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` to match your MySQL server. The defaults target a local XAMPP MySQL server (`127.0.0.1:3306`, database `pos_laravel`, user `root`, no password).

From the project directory:

```sh
composer install
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000` and sign in with the seeded administrator:

- Username: `admin`
- Password: `ChangeMe123!`

Set `POS_ADMIN_USERNAME` and `POS_ADMIN_PASSWORD` in `.env` before seeding to choose different initial credentials. Change the initial password before using the system outside a local development environment.

Tests continue to use an isolated in-memory SQLite database.

## Roles

- **Admin:** dashboard, POS, full inventory/product management, sales history, user/role management, sack-size settings, and audit history.
- **Cashier:** dashboard, POS, and inventory/product management.

Passwords are hashed. Do not expose the app publicly without HTTPS and a strong administrator password.

## Inventory and sales

Products are tracked by their selling unit; create separate kilo and sack products with independent prices and stock counts. The dashboard shows both low stock (5 or fewer) and available stock (more than 5). Kilo products can be linked to an admin-managed sack size. Prices are in Philippine pesos. A checkout creates one sales row per product, with a shared receipt number for all rows in that transaction. Stock cannot be sold below zero, and product/user records referenced by sales cannot be deleted.

The admin-only audit history records successful sign-ins/sign-outs, completed sales, product and sack-size changes, account creation/changes/deletion, and password resets. Audit entries include the actor and timestamp and never store password values.

The provided schema did not include inventory quantities or a way to group multiple sale lines. The app adds `products.stock`, a receipt UUID, and a unit-price snapshot to support inventory control, receipts, and accurate sales history. Admins can manage sack-size options; staff assign one to kilo products so inventory and POS screens show which sack the kilo product comes from.

## Tests

```sh
php artisan test --filter=PosWorkflowTest
```
