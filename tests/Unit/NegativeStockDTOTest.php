<?php
declare(strict_types=1);

namespace Ksfraser\Tests\FrontAccounting\StockNegatives;

use Ksfraser\FrontAccounting\StockNegatives\NegativeStockItemDTO;
use Ksfraser\FrontAccounting\StockNegatives\ReportOptions;
use Ksfraser\FrontAccounting\StockNegatives\ReportData;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for StockNegatives DTOs.
 *
 * @BABOK Related: FR-RE-001-001
 * @since 1.0.0
 */
class NegativeStockDTOTest extends TestCase
{
    public function testNegativeStockItemDTOFromArray(): void
    {
        $data = [
            'item_code' => 'NEG-001',
            'description' => 'Negative Item',
            'category' => 'Electronics',
            'quantity' => -15.5,
            'location' => 'MAIN',
            'last_sale_date' => '2026-01-10',
            'last_adjustment_date' => '2026-01-15',
        ];

        $dto = NegativeStockItemDTO::fromArray($data);

        $this->assertEquals('NEG-001', $dto->getItemCode());
        $this->assertEquals('Negative Item', $dto->getDescription());
        $this->assertEquals('Electronics', $dto->getCategory());
        $this->assertEquals(-15.5, $dto->getQuantity());
        $this->assertEquals('MAIN', $dto->getLocation());
        $this->assertEquals('2026-01-10', $dto->getLastSaleDate());
        $this->assertEquals('2026-01-15', $dto->getLastAdjustmentDate());
    }

    public function testReportOptionsDefaults(): void
    {
        $options = new ReportOptions();

        $this->assertNull($options->getCategoryId());
        $this->assertNull($options->getLocation());
        $this->assertEquals('quantity', $options->getSortBy());
        $this->assertEquals('ASC', $options->getSortOrder());
    }

    public function testReportOptionsFromArray(): void
    {
        $data = [
            'category_id' => 'CAT1',
            'location' => 'MAIN',
            'sort_by' => 'category',
            'sort_order' => 'DESC',
        ];

        $options = ReportOptions::fromArray($data);

        $this->assertEquals('CAT1', $options->getCategoryId());
        $this->assertEquals('MAIN', $options->getLocation());
        $this->assertEquals('category', $options->getSortBy());
        $this->assertEquals('DESC', $options->getSortOrder());
    }

    public function testReportDataHasItems(): void
    {
        $items = [
            NegativeStockItemDTO::fromArray([
                'item_code' => 'NEG-001',
                'description' => 'Test',
                'category' => 'CAT1',
                'quantity' => -10,
            ]),
        ];

        $data = new ReportData($items, 1, -10.0, null, null, new \DateTimeImmutable());

        $this->assertTrue($data->hasItems());
        $this->assertCount(1, $data->getItems());
        $this->assertEquals(1, $data->getTotalCount());
        $this->assertEquals(-10.0, $data->getTotalNegativeQuantity());
    }

    public function testReportDataEmpty(): void
    {
        $data = new ReportData([], 0, 0.0, null, null, new \DateTimeImmutable());

        $this->assertFalse($data->hasItems());
        $this->assertCount(0, $data->getItems());
    }
}