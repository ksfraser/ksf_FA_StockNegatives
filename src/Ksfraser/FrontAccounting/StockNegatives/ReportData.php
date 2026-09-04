<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\StockNegatives;

/**
 * Report data container.
 *
 * @since 1.0.0
 */
class ReportData
{
    /** @var NegativeStockItemDTO[] */
    private $items;

    /** @var int */
    private $totalCount;

    /** @var float */
    private $totalNegativeQuantity;

    /** @var string|null */
    private $categoryId;

    /** @var string|null */
    private $location;

    /** @var \DateTimeImmutable */
    private $generatedAt;

    public function __construct(
        array $items,
        int $totalCount,
        float $totalNegativeQuantity,
        ?string $categoryId,
        ?string $location,
        \DateTimeImmutable $generatedAt
    ) {
        $this->items = $items;
        $this->totalCount = $totalCount;
        $this->totalNegativeQuantity = $totalNegativeQuantity;
        $this->categoryId = $categoryId;
        $this->location = $location;
        $this->generatedAt = $generatedAt;
    }

    /**
     * @return NegativeStockItemDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getTotalNegativeQuantity(): float
    {
        return $this->totalNegativeQuantity;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getGeneratedAt(): \DateTimeImmutable
    {
        return $this->generatedAt;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }
}