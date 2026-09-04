# BR-RE-001: Stock Negatives Report

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-04

## Status
Approved

## Business Requirement

Port the webERP PDFStockNegatives report to FrontAccounting. This report lists all stock items with negative quantities on hand, which indicates inventory discrepancies requiring attention.

### Problem Statement

Negative stock indicates:
1. Data entry errors in adjustments
2. Unrecorded returns
3. Theft or shrinkage
4. System errors in order fulfillment

Without visibility into negative stock, these issues go undetected.

### Solution

Create a PDF report that:
- Lists all items with quantity < 0
- Shows current negative quantity
- Includes item details (description, category, location)
- Sorts by category or quantity severity
- Configurable filters (category, location, date range)

### Scope

**In Scope:**
- Generate negative stock report
- PDF output with proper formatting
- Filter by category
- Filter by location/stock location
- Sort options

**Out of Scope:**
- Auto-correction of negative stock
- Email notifications (future feature)
- Scheduled reports (use FA report scheduler)

### Dependencies

- `stock_master` table
- `stock_category` table
- `loc_stock` table (for location quantities)
- TCPDF or FA's PDF library

### Success Metrics

- Report generates in < 2 seconds for 10,000 items
- Zero false positives (items shown are truly negative)