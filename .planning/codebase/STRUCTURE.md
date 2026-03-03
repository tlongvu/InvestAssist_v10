# Codebase Structure

**Analysis Date:** 2026-03-03

## Directory Layout

```
InvestAssist_v10/
├── app/
│   ├── Http/Controllers/    # Request handling
│   ├── Models/              # Data models
│   ├── Services/            # Business logic
│   └── Console/Commands/    # CLI specialized scripts
├── config/                  # Configuration files
├── database/
│   ├── migrations/          # Schema definitions
│   └── seeders/             # Initial data
├── resources/
│   ├── views/               # UI templates (Blade)
│   └── css/                 # Styling
├── routes/                  # Uri routing
├── public/                  # Static assets
└── tests/                   # Test suite
```

## Naming Conventions

**Files:**
- Controllers: `*Controller.php` (PascalCase)
- Models: `*.php` (Singular PascalCase)
- Views: `*.blade.php` (kebab-case)

## Where to Add New Code

**New Dashboard Feature:**
- Logic: `app/Services/PortfolioPerformanceService.php`
- Display: `resources/views/dashboard.blade.php`

**New Entity (e.g., Cryptos):**
- Model: `app/Models/Crypto.php`
- Migration: `database/migrations/`
- Controller: `app/Http/Controllers/CryptoController.php`

---

*Structure analysis: 2026-03-03*
