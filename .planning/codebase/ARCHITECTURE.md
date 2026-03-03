# Architecture

**Analysis Date:** 2026-03-03

## Pattern Overview

**Overall:** Model-View-Controller (MVC) + Service Layer

**Key Characteristics:**
- Laravel 11 structure
- Business logic isolated in `app/Services`
- Data persistence via Eloquent Models

## Layers

**Controllers:**
- Purpose: Handle HTTP requests and orchestrate services
- Location: `app/Http/Controllers/`
- Depends on: Services, Models
- Used by: Routing system

**Services:**
- Purpose: Core business logic (portfolio valuation, history reconstruction)
- Location: `app/Services/`
- Contains: `PortfolioPerformanceService.php`
- Depends on: Models, External APIs (via Guzzle)

**Models:**
- Purpose: Database representation and relationships
- Location: `app/Models/`
- Includes: `User.php`, `Stock.php`, `CashFlow.php`, `StockTransaction.php`, `Exchange.php`, `Industry.php`

**Views:**
- Purpose: UI Rendering
- Location: `resources/views/`
- Pattern: Blade templates + AlpineJS/Chart.js scripts

## Data Flow

**Portfolio Dashboard:**

1. Route `/dashboard` calls `DashboardController@index`.
2. Controller calls `PortfolioPerformanceService` methods to calculate metrics.
3. Service queries database models and potentially FireAnt API.
4. Results are passed to `dashboard.blade.php`.
5. Frontend script (`Chart.js`) renders the history and allocation charts.

## Key Abstractions

**PortfolioPerformanceService:**
- Purpose: Central hub for all financial calculations.
- File: `app/Services/PortfolioPerformanceService.php`

## Entry Points

**Web:**
- URI: `/dashboard`
- Location: `routes/web.php` -> `app/Http/Controllers/DashboardController.php`

**CLI:**
- Commands: `app:sync-stock-prices`, `app:send-telegram-report`
- Location: `app/Console/Commands/`

---

*Architecture analysis: 2026-03-03*
