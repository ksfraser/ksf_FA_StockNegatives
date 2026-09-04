# FR-RE-001-001: Generate Negative Stock Report

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-04

## Status
Approved

## Functional Requirement

### Related BR
BR-RE-001-stock-negatives-report

### Description

The system SHALL generate a PDF report listing all stock items with negative quantity on hand when requested by a user with appropriate permissions.

### Preconditions

1. User has access to inventory reports
2. Stock master table contains items with negative quantities

### Postconditions

1. PDF report generated and displayed/downloaded

### User Flow

1. User navigates to Reports > Inventory
2. User selects "Stock Negatives Report"
3. User optionally sets filters (category, location)
4. User clicks "Generate Report"
5. PDF is generated and presented

### Acceptance Criteria

| ID | Criteria | Test Scenario |
|----|----------|---------------|
| AC-01 | Report lists all items with qty < 0 | UT-RE-001-001-001 |
| AC-02 | Report excludes items with qty >= 0 | UT-RE-001-001-002 |
| AC-03 | Filter by category works | UT-RE-001-001-003 |
| AC-04 | Filter by location works | UT-RE-001-001-004 |
| AC-05 | PDF output is valid | UT-RE-001-001-005 |
| AC-06 | Empty result shows proper message | UT-RE-001-001-006 |