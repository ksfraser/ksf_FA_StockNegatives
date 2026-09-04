# UAT-RE-001: Stock Negatives Report - User Acceptance Test

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-04

## Status
Approved

## UAT Cases

### UAT-RE-001-01: Generate Report - No Negatives

| Field | Value |
|-------|-------|
| Purpose | Verify report handles empty result gracefully |
| Prereqs | No negative stock items exist |
| Actor | Inventory Manager |
| Steps | 1. Navigate to Reports > Inventory > Stock Negatives<br>2. Click Generate |
| Expected | PDF shows "No negative stock found" message |
| PASS Criteria | Empty state message displayed |

### UAT-RE-001-02: Generate Report - With Negatives

| Field | Value |
|-------|-------|
| Purpose | Verify negative items appear in report |
| Prereqs | At least 3 items with negative stock |
| Actor | Inventory Manager |
| Steps | 1. Navigate to Reports > Inventory > Stock Negatives<br>2. Click Generate |
| Expected | All negative items listed with qty and description |
| PASS Criteria | All negatives appear, quantities match DB |

### UAT-RE-001-03: Filter by Category

| Field | Value |
|-------|-------|
| Purpose | Verify category filter works |
| Prereqs | Multiple categories with negative items |
| Actor | Inventory Manager |
| Steps | 1. Select specific category<br>2. Generate report |
| Expected | Only items in selected category shown |
| PASS Criteria | Correct filtering applied |