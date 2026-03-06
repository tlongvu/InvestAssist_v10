# GSD - InvestAssist v10 Project State

> **Get Shit Done** — Tài liệu này dùng để tóm tắt trạng thái project, giúp AI (và dev) không bị loạn khi context window lớn.

---

## 🗂️ Tech Stack
- **Framework:** Laravel (PHP)
- **Frontend:** Blade Templates + Vanilla JS + Chart.js + Flatpickr
- **Database:** MySQL/SQLite
- **External API:** FireAnt (Historical Stock Prices), DNSE (Realtime Prices)

---

## 📐 Kiến trúc dữ liệu

### Models & Bảng chính
| Model | Bảng | Mô tả |
|-------|------|-------|
| `User` | `users` | Người dùng, có `bank_interest_rate` |
| `Stock` | `stocks` | Danh mục cổ phiếu. `quantity` & `avg_price` nhập **thủ công** |
| `StockTransaction` | `stock_transactions` | **Chỉ là sổ ghi chép** lịch sử mua/bán, KHÔNG ảnh hưởng tính toán |
| `CashFlow` | `cash_flows` | Dòng tiền nạp/rút. `type`: `deposit` hoặc `withdrawal` |
| `Exchange` | `exchanges` | Sàn giao dịch (VPS, VCSC, ...) |
| `UserExchangeBalance` | `user_exchange_balances` | Số dư tiền mặt thực tế tại mỗi sàn |
| `Industry` | `industries` | Ngành nghề cổ phiếu |

---

## 💡 Logic nghiệp vụ quan trọng

### Dashboard - Thẻ tóm tắt
- **Tổng vốn đầu tư** = Tổng `CashFlow` (deposits - withdrawals)
- **Giá trị hiện tại** = `Stocks.quantity * Stocks.current_price` + `UserExchangeBalance.balance`
- **Lãi/Lỗ** = Giá trị hiện tại - Tổng vốn

### Biểu đồ Tăng trưởng tài sản (NAV)
- **Nguồn dữ liệu:** `Stocks` model + `CashFlow` model + API giá lịch sử
- **Công thức:** `PortfolioValue = TotalNetDeposited + UnrealizedPnL (Giá TT - Giá vốn của CP đã có)`
- **Bộ lọc chu kỳ:** 
  - *Lùi 60 ngày từ ngày chọn*: Lùi 60 ngày giao dịch từ ngày được pick.
  - *Hôm nay*: Lùi 60 ngày giao dịch tính từ hôm nay.
  - *Xem tất cả*: Xem theo từng tuần từ khi có dòng tiền đầu tiên.

### StockTransaction — Chỉ là sổ ghi chép
- Ghi nhận lịch sử: mua/bán mã nào, ngày nào, giá bao nhiêu
- **KHÔNG** tự động cập nhật `Stocks.quantity` hay `Stocks.avg_price`
- **KHÔNG** ảnh hưởng đến Dashboard hay biểu đồ
- Tính năng đồng bộ tự động (auto-sync) **CÒN TRONG KẾ HOẠCH, CHƯA LÀM**

---

## 🔑 Service chính: `PortfolioPerformanceService.php`
| Method | Mô tả |
|--------|-------|
| `calculateTotalInvested()` | Tổng vốn (CashFlows) |
| `calculateTotalCurrentValue()` | Tổng tài sản hiện tại |
| `getWealthBreakdown()` | Bóc tách: Cổ phiếu vs Tiền mặt |
| `calculateProfitLoss()` | Lãi/Lỗ tuyệt đối + % |
| `getStockPerformance()` | P&L từng mã |
| `getBankComparison()` | So sánh với gửi ngân hàng |
| `getAssetHistory(period, endDate)` | Dữ liệu cho biểu đồ NAV |
| `getLiquidCashByExchange()` | Tiền mặt tại các sàn |
| `getAllocationByIndustry/Exchange/Stock()` | Dữ liệu biểu đồ phân bổ |



## 🎨 UI / Branding
- **App Name:** InvestAssist (`.env APP_NAME`)
- **Logo:** `public/logo.svg` (SVG thanh bar chart tăng trưởng)
- **Favicon:** `logo.svg` (trong `layouts/app.blade.php`)
- **Sidebar:** Dùng `logo.svg` + tên InvestAssist
- **Đơn vị tiền:** `đ` (VND), hậu tố sau số
- **Privacy mode:** JS toggle ẩn/hiện số, giữ ký tự `đ` (dùng `data-currency="đ"`)

---

## ✅ Đã hoàn thành (tính từ session này)
- [x] Thêm đơn vị `đ` toàn bộ Dashboard
- [x] Fix Privacy mode để giữ `đ` khi ẩn số
- [x] Biểu đồ: 4 chu kỳ (Ngày/Tuần/Tháng/Quý)
- [x] Biểu đồ: Date picker lọc theo ngày
- [x] Biểu đồ: NAV normalization (loại bỏ bậc thang nạp tiền)
- [x] Biểu đồ: Inception Date (không hiện data fake trước ngày nạp tiền đầu tiên)
- [x] Biểu đồ: Tách biệt hoàn toàn khỏi StockTransaction
- [x] Dashboard: Bóc tách Cổ phiếu & Tiền mặt trong thẻ "Giá trị hiện tại"
- [x] Branding: Đổi tên + Logo InvestAssist
- [x] Biểu đồ: 3 chu kỳ (60 ngày từ ngày chọn / Hôm nay / Xem tất cả)
- [x] Biểu đồ: Dùng logic Tổng nạp ròng + Unrealized PnL theo `created_at`
- [x] **XÓA BỎ** tính năng Tin tức Chứng khoán (News) hoàn toàn khỏi dự án.
