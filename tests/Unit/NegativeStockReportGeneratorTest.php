<?php
declare(strict_types=1);

namespace Ksfraser\Tests\FrontAccounting\StockNegatives;

use Ksfraser\FrontAccounting\StockNegatives\NegativeStockReportGenerator;
use Ksfraser\FrontAccounting\StockNegatives\NegativeStockRepository;
use Ksfraser\FrontAccounting\StockNegatives\NegativeStockItemDTO;
use Ksfraser\FrontAccounting\StockNegatives\ReportOptions;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for NegativeStockReportGenerator.
 *
 * @BABOK Related: FR-RE-001-001
 * @since 1.0.0
 */
class NegativeStockReportGeneratorTest extends TestCase
{
    private NegativeStockReportGenerator $generator;
    private NegativeStockRepository $mockRepository;

    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(NegativeStockRepository::class);
        $this->generator = new NegativeStockReportGenerator($this->mockRepository);
    }

    public function testGenerateWithNoNegatives(): void
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findNegativeStock')
            ->willReturn([]);

        $options = new ReportOptions();
        $data = $this->generator->generate($options);

        $this->assertFalse($data->hasItems());
        $this->assertEquals(0, $data->getTotalCount());
        $this->assertEquals(0.0, $data->getTotalNegativeQuantity());
    }

    public function testGenerateWithNegatives(): void
    {
        $items = [
            NegativeStockItemDTO::fromArray([
                'item_code' => 'NEG-001',
                'description' => 'Item 1',
                'category' => 'CAT1',
                'quantity' => -10.0,
            ]),
            NegativeStockItemDTO::fromArray([
                'item_code' => 'NEG-002',
                'description' => 'Item 2',
                'category' => 'CAT2',
                'quantity' => -25.5,
            ]),
        ];

        $this->mockRepository
            ->expects($this->once())
            ->method('findNegativeStock')
            ->willReturn($items);

        $options = new ReportOptions();
        $data = $this->generator->generate($options);

        $this->assertTrue($data->hasItems());
        $this->assertEquals(2, $data->getTotalCount());
        $this->assertEquals(-35.5, $data->getTotalNegativeQuantity());
    }

    public function testGenerateCsvOutput(): void
    {
        $items = [
            NegativeStockItemDTO::fromArray([
                'item_code' => 'NEG-001',
                'description' => 'Item 1',
                'category' => 'CAT1',
                'quantity' => -10.0,
                'location' => 'MAIN',
            ]),
        ];

        $data = new ReportData($items, 1, -10.0, null, null, new \DateTimeImmutable());
        $csv = $this->generator->generateCsv($data);

        $this->assertStringContainsString('NEG-001', $csv);
        $this->assertStringContainsString('Item 1', $csv);
        $this->assertStringContainsString('-10.00', $csv);
    }

    public function testGenerateCsvWithCommasInDescription(): void
    {
        $items = [
            NegativeStockItemDTO::fromArray([
                'item_code' => 'NEG-001',
                'description' => 'Item, with comma',
                'category' => 'CAT1',
                'quantity' => -10.0,
                'location' => null,
            ]),
        ];

        $data = new ReportData($items, 1, -10.0, null, null, new \DateTimeImmutable());
        $csv = $this->generator->generateCsv($data);

        $this->assertStringContainsString('"Item, with comma"', $csv);
    }
}