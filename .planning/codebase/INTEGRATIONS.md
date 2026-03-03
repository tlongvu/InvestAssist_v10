# External Integrations

**Analysis Date:** 2026-03-03

## APIs & External Services

**Market Data:**
- FireAnt API - Historical and real-time stock quotes
  - Endpoint: `https://www.fireant.vn/api/Data/Markets/HistoricalQuotes`
  - Implementation: `app/Services/PortfolioPerformanceService.php` and `app/Console/Commands/SyncStockPrices.php`

**Alerts:**
- Telegram Bot - Daily reports and price alerts
  - Implementation: `app/Console/Commands/TelegramDailyReport.php`
  - Auth: `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`

## Data Storage

**Databases:**
- MySQL - Relational storage for user data, stocks, and transactions
  - Client: Eloquent ORM

**File Storage:**
- Local filesystem only

**Caching:**
- Laravel Cache (File/Database based on `.env`)
  - Used for historical stock prices (`hist_prices_{symbol}`)

## Authentication & Identity

**Auth Provider:**
- Laravel Breeze/Fortify (Standard custom auth)
  - Sessions handled by database (seen in `.env`)

## Monitoring & Observability

**Error Tracking:**
- Laravel Log (Storage at `storage/logs/laravel.log`)

## CI/CD & Deployment

**Hosting:**
- VPS (Ubuntu/Linux based on context)

---

*Integration audit: 2026-03-03*
