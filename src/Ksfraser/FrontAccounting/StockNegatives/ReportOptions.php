<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\StockNegatives;

/**
 * Options for negative stock report.
 *
 * @since 1.0.0
 */
class ReportOptions
{
    /** @var string|null */
    private $categoryId;

    /** @var string|null */
    private $location;

    /** @var string */
    private $sortBy;

    /** @var string */
    private $sortOrder;

    public function __construct(
        ?string $categoryId = null,
        ?string $location = null,
        string $sortBy = 'quantity',
        string $sortOrder = 'ASC'
    ) {
        $this->categoryId = $categoryId;
        $this->location = $location;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['category_id'] ?? null,
            $data['location'] ?? null,
            $data['sort_by'] ?? 'quantity',
            $data['sort_order'] ?? 'ASC'
        );
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'location' => $this->location,
            'sort_by' => $this->sortBy,
            'sort_order' => $this->sortOrder,
        ];
    }
}