<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\StockNegatives;

use Ksfraser\CommonDb\Contract\DbConnectionInterface;

/**
 * Data transfer object for negative stock item.
 *
 * @BABOK Related: FR-RE-001-001
 * @since 1.0.0
 */
class NegativeStockItemDTO
{
    /** @var string */
    private $itemCode;

    /** @var string */
    private $description;

    /** @var string */
    private $category;

    /** @var string */
    private $location;

    /** @var float */
    private $quantity;

    /** @var string|null */
    private $lastSaleDate;

    /** @var string|null */
    private $lastAdjustmentDate;

    public function __construct(
        string $itemCode,
        string $description,
        string $category,
        float $quantity,
        ?string $location = null,
        ?string $lastSaleDate = null,
        ?string $lastAdjustmentDate = null
    ) {
        $this->itemCode = $itemCode;
        $this->description = $description;
        $this->category = $category;
        $this->quantity = $quantity;
        $this->location = $location;
        $this->lastSaleDate = $lastSaleDate;
        $this->lastAdjustmentDate = $lastAdjustmentDate;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            $row['item_code'],
            $row['description'],
            $row['category'],
            (float) $row['quantity'],
            $row['location'] ?? null,
            $row['last_sale_date'] ?? null,
            $row['last_adjustment_date'] ?? null
        );
    }

    public function getItemCode(): string
    {
        return $this->itemCode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getLastSaleDate(): ?string
    {
        return $this->lastSaleDate;
    }

    public function getLastAdjustmentDate(): ?string
    {
        return $this->lastAdjustmentDate;
    }

    public function toArray(): array
    {
        return [
            'item_code' => $this->itemCode,
            'description' => $this->description,
            'category' => $this->category,
            'location' => $this->location,
            'quantity' => $this->quantity,
            'last_sale_date' => $this->lastSaleDate,
            'last_adjustment_date' => $this->lastAdjustmentDate,
        ];
    }
}