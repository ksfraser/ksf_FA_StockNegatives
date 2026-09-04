<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\StockNegatives;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Report generator for negative stock.
 *
 * @BABOK Related: FR-RE-001-001
 * @since 1.0.0
 */
class NegativeStockReportGenerator
{
    /** @var NegativeStockRepository */
    private $repository;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        NegativeStockRepository $repository,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Generate report data.
     *
     * @param ReportOptions $options
     * @return ReportData
     *
     * @since 1.0.0
     */
    public function generate(ReportOptions $options): ReportData
    {
        $this->logger->info('Generating negative stock report', [
            'category' => $options->getCategoryId(),
            'location' => $options->getLocation(),
        ]);

        $items = $this->repository->findNegativeStock(
            $options->getCategoryId(),
            $options->getLocation(),
            $options->getSortBy(),
            $options->getSortOrder()
        );

        $totalCount = count($items);
        $totalNegativeQty = array_sum(array_map(fn($i) => $i->getQuantity(), $items));

        return new ReportData(
            $items,
            $totalCount,
            $totalNegativeQty,
            $options->getCategoryId(),
            $options->getLocation(),
            new \DateTimeImmutable()
        );
    }

    /**
     * Generate CSV output.
     *
     * @param ReportData $data
     * @return string
     *
     * @since 1.0.0
     */
    public function generateCsv(ReportData $data): string
    {
        $output = "Item Code,Description,Category,Location,Quantity\n";

        foreach ($data->getItems() as $item) {
            $output .= sprintf(
                "%s,%s,%s,%s,%.2f\n",
                $this->escapeCsv($item->getItemCode()),
                $this->escapeCsv($item->getDescription()),
                $this->escapeCsv($item->getCategory()),
                $this->escapeCsv($item->getLocation() ?? 'N/A'),
                $item->getQuantity()
            );
        }

        return $output;
    }

    private function escapeCsv(string $value): string
    {
        if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }
}