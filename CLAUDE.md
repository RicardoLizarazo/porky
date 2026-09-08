# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Porky is a Laravel 12 restaurant/POS management system (Spanish-language domain: mesas, cocina, caja, pedidos). It covers dining-room table management, kitchen order dispatch (with TV "board" screens), cashier/cash-session handling, customer-facing ordering, and administrative reporting. Backend logic lives in Eloquent models and Livewire components; views are Blade + AdminLTE (Bootstrap) + Tailwind, built with Vite.

## Commands

Run these from the project root (PHP via WAMP's PHP, or via `php` on PATH).

```bash
# Install
composer install
npm install

# Local dev (serves PHP, queue listener, log tailer, and Vite together)
composer run dev

# Frontend build
npm run dev      # Vite dev server
npm run build    # production build

# Tests (uses phpunit.xml env: sqlite :memory:, array session/cache)
composer test
php artisan test
php artisan test --filter=TestName
php artisan test tests/Feature/SomeTest.php

# Code style (Laravel Pint)
vendor/bin/pint
vendor/bin/pint --test    # check only, no fixes

# Migrations / seed
php artisan migrate
php artisan migrate:fresh --seed
```

There is no JS/PHP linter configured beyond Pint; no CI config in-repo.

## Architecture

**Auth has two independent guards.** `web` (session, `App\Models\User`, staff/admin) and `customer` (session, `App\Models\Customer`, storefront/self-service ordering). Routes under `/customer/*` and the customer order history use `auth:customer`; most admin/POS routes use `auth`. A few routes (e.g. `/menu`) accept either guard via the custom `auth.any` middleware ([app/Http/Middleware/AuthAny.php](app/Http/Middleware/AuthAny.php)). `/logout-any` inspects which guard is active and logs out accordingly.

**Kitchen board vs. kitchen dispatch are intentionally different auth levels.** `/kitchen-board/{station}` (TV screen, HDMI-connected, no login possible) is deliberately unauthenticated and read-only. `/kitchen-dispatch/{station}` requires `auth` because staff mark items ready there. Don't casually add auth to the board route or remove it from dispatch — see the comments in [routes/web.php](routes/web.php).

**Permissions via spatie/laravel-permission.** Authorization checks use `Auth::user()->can('permission_name')` (dot-scoped names like `kitchen_dispatch.view_all`). Roles/permissions/users are managed under `/roles`, `/permissions`, `/users` (Livewire, in `App\Livewire`). All model changes relevant to auditing use `spatie/laravel-activitylog` (`LogsActivity` trait + `getActivitylogOptions()`), e.g. [app/Models/User.php](app/Models/User.php).

**Kitchen stations can be combined for shared screens.** [config/kitchen.php](config/kitchen.php) defines `combos` — groups of `kitchen_station_id`s shown together on one physical TV/tablet when there's no dedicated screen per station. The `ResolvesKitchenStations` trait (`App\Livewire\Restaurante\Concerns`) resolves a route's `{station}` param (single id or combo) into the underlying station id list; both board and dispatch components use it. Individual per-station routes keep working alongside combo views.

**Order model forces an explicit column list.** [app/Models/Order.php](app/Models/Order.php) overrides `newQuery()` to `select()` a fixed set of `orders.*` columns on every query. When adding a new column to the `orders` table that should be readable through the model, it must also be added to that `select()` list or it will silently come back null.

**Domain PIN gate, not a user password.** Removing a product already sent to the kitchen requires a separate PIN (`config/pos.php` → `removal_pin`, from `PRODUCT_REMOVAL_PIN` in `.env`), independent of any user's login password. It's meant to be changed via env, not code/DB.

**Livewire component organization** (`app/Livewire/`):
- `Restaurante/` — floor/table/kitchen dispatch & board operations
- `Cash/`, `Cashier/` — cash session open/close, cashier order flow
- `Reports/`, `RestaurantReports/` — reporting dashboards (each report area has its own `Concerns/` traits, e.g. `HasReportFilters`)
- `Components/` — shared small pieces (e.g. customer autocomplete/inline create/edit)

Matching Blade views live under `resources/views/livewire/...` mirroring the component namespace.

**Two auth flows, two controller sets.** Staff auth uses Laravel UI's generated `Auth::routes()`. Customer auth is a separate hand-rolled controller, `App\Http\Controllers\Auth\CustomerAuthController`, with its own login/register/logout routes under `/customer/*`.

**Invoicing/printing** goes through `OrderController` (`invoice`, `invoice-pdf` via barryvdh/laravel-dompdf, `ticket`, `tableTicket`) and `CashCloseController` (`print` for cash-session close reports) — these render dedicated print-friendly Blade views rather than the SPA-style Livewire pages.

**Excel exports** use `maatwebsite/excel` under `app/Exports/`.

## Conventions

- Domain code comments and many model/field concepts are in Spanish (matches the business domain — mesas, cocina, caja, pedidos); keep new domain-facing comments/UI copy in Spanish for consistency, code identifiers can stay English.
- Locale is `es` by default (`APP_LOCALE`), with translations under `resources/lang/es`.
- DB is MySQL in the real `.env` (`DB_CONNECTION=mysql`) but SQLite in-memory for tests (`phpunit.xml`).
