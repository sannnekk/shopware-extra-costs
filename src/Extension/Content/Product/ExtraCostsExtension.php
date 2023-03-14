<?php declare (strict_types = 1);

namespace Swag\BasicExample\Extension\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ExtraCostsExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            ""
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
