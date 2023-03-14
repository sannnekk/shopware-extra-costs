<?php declare (strict_types = 1);

namespace HMnetProductExtraCosts\Extension\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;

class ExtraCostsExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('extraCostsExtension', 'id', 'product_id', ExtraCostsExtensionDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
