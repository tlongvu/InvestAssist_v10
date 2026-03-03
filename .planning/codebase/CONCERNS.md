# Codebase Concerns

**Analysis Date:** 2026-03-03

## Tech Debt

**Historical Reconstruction Accuracy:**
- Issue: `getAssetHistory` assumes current stock quantities for all past dates.
- Files: `app/Services/PortfolioPerformanceService.php`
- Impact: Inaccurate historical valuation if stocks were bought/sold within the 30-day window.
- Fix approach: Implement full transaction walk-back.

## Known Bugs

**None reported.**

## Security Considerations

**API Key Exposure:**
- Risk: FireAnt API keys or Telegram IDs might be hardcoded or improperly handled.
- Mitigation: Move all credentials to `.env`.

## Performance Bottlenecks

**Sequential API Calls:**
- Problem: Syncing prices calls FireAnt API sequentially for 50+ stocks.
- Files: `app/Console/Commands/SyncStockPrices.php`
- Cause: Rate limit avoidance (`usleep`).
- Improvement path: Batching or parallel background jobs.

## Fragile Areas

**Frontend Polling:**
- Files: `resources/views/dashboard.blade.php`
- Why fragile: Intervals of 10 minutes might miss session expirations or cause UI flickers if not handled gracefully.

---

*Concerns audit: 2026-03-03*
