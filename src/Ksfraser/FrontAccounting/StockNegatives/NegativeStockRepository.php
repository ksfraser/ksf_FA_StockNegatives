<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\StockNegatives;

use Ksfraser\CommonDb\Contract\DbConnectionInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Repository for querying negative stock items.
 *
 * @BABOK Related: FR-RE-001-001
 * @since 1.0.0
 */
class NegativeStockRepository
{
    /** @var DbConnectionInterface */
    private $db;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $stockTable;

    /** @var string */
    private $categoryTable;

    public function __construct(
        DbConnectionInterface $db,
        ?LoggerInterface $logger = null,
        string $stockTable = '0_stock_master',
        string $categoryTable = '0_stock_category'
    ) {
        $this->db = $db;
        $this->logger = $logger ?? new NullLogger();
        $this->stockTable = $stockTable;
        $this->categoryTable = $categoryTable;
    }

    /**
     * Find all items with negative stock.
     *
     * @param string|null $categoryId Filter by category
     * @param string|null $location Filter by stock location
     * @param string $sortBy Sort field (quantity, category, item_code)
     * @param string $sortOrder ASC or DESC
     * @return NegativeStockItemDTO[]
     *
     * @since 1.0.0
     */
    public function findNegativeStock(
        ?string $categoryId = null,
        ?string $location = null,
        string $sortBy = 'quantity',
        string $sortOrder = 'ASC'
    ): array {
        $validSorts = ['quantity', 'category', 'item_code', 'description'];
        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'quantity';
        }
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT
                    s.stock_id AS item_code,
                    s.description,
                    c.category_description AS category,
                    s.quantity,
                    s.loc_code AS location,
                    s.last_sale_date,
                    s.last_adjustment_date
                FROM {$this->stockTable} s
                LEFT JOIN {$this->categoryTable} c ON s.category_id = c.category_id
                WHERE s.quantity < 0";

        $params = [];

        if ($categoryId !== null) {
            $sql .= " AND s.category_id = ?";
            $params[] = $categoryId;
        }

        if ($location !== null) {
            $sql .= " AND s.loc_code = ?";
            $params[] = $location;
        }

        $sql .= " ORDER BY {$sortBy} {$sortOrder}";

        $this->logger->debug('Finding negative stock', [
            'category' => $categoryId,
            'location' => $location,
            'sort' => "{$sortBy} {$sortOrder}",
        ]);

        $rows = $this->db->fetchAll($sql, $params);

        return array_map(fn(array $row) => NegativeStockItemDTO::fromArray($row), $rows);
    }

    /**
     * Get count of negative stock items.
     *
     * @param string|null $categoryId
     * @return int
     *
     * @since 1.0.0
     */
    public function countNegativeStock(?string $categoryId = null): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM {$this->stockTable} WHERE quantity < 0";
        $params = [];

        if ($categoryId !== null) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
        }

        $result = $this->db->fetchAssoc($sql, $params);
        return (int) ($result['cnt'] ?? 0);
    }

    /**
     * Get total negative quantity across all items.
     *
     * @param string|null $categoryId
     * @return float
     *
     * @since 1.0.0
     */
    public function getTotalNegativeQuantity(?string $categoryId = null): float
    {
        $sql = "SELECT COALESCE(SUM(quantity), 0) as total FROM {$this->stockTable} WHERE quantity < 0";
        $params = [];

        if ($categoryId !== null) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
        }

        $result = $this->db->fetchAssoc($sql, $params);
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Get all categories for filter dropdown.
     *
     * @return array<string, string>
     *
     * @since 1.0.0
     */
    public function getCategories(): array
    {
        $sql = "SELECT category_id, category_description
                FROM {$this->categoryTable}
                ORDER BY category_description";

        $rows = $this->db->fetchAll($sql, []);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['category_id']] = $row['category_description'];
        }
        return $result;
    }

    /**
     * Get all locations for filter dropdown.
     *
     * @return array<string, string>
     *
     * @since 1.0.0
     */
    public function getLocations(): array
    {
        $sql = "SELECT loc_code, location_name FROM 0_locations ORDER BY location_name";
        $rows = $this->db->fetchAll($sql, []);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['loc_code']] = $row['location_name'];
        }
        return $result;
    }
}