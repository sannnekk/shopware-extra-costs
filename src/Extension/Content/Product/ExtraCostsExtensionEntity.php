<?php

declare(strict_types=1);

namespace HMnetProductExtraCosts\Extension\Content\Product;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ExtraCostsExtensionEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $json;

    public function getJson(): ?string
    {
        return $this->json;
    }

    public function setJson(?string $json): void
    {
        $this->name = $json;
    }
}
