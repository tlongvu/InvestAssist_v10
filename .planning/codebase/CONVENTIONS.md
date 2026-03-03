# Coding Conventions

**Analysis Date:** 2026-03-03

## Naming Patterns

**Files:**
- Singular PascalCase for Models: `StockTransaction.php`
- Plural kebab-case for Views: `cash-flows/index.blade.php`

**Functions:**
- CamelCase for methods: `getAssetHistory`
- snake_case for private helpers within Blade or JS: `format_money`

## Code Style

**Formatting:**
- PSR-12 for PHP
- Tailwind CSS class order (generally messy/standard)

## Import Organization

**PHP:**
- Laravel/Spatie facades first
- App Models/Services follow

## Error Handling

**Patterns:**
- Try-catch blocks around external API calls (FireAnt)
- Logging errors to `Log::error()`

## Comments

**When to Comment:**
- Complexity: Used in `PortfolioPerformanceService.php` to explain historical reconstruction logic.

---

*Convention analysis: 2026-03-03*
