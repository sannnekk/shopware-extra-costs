<?php

declare(strict_types=1);

namespace HMnetProductExtraCosts\Helper;

use Shopware\Core\Content\Product\ProductEntity;

class GetAdditionalPositions
{
    public static function getAdditionalPositions(ProductEntity $product, array $extraCosts): array
    {
        $options = $product->getOptions();
        $additionalPositions = [
            "setup" => [],
            "film" => []
        ];

        foreach ($options as $optionId => $option) {
            $extraCost = array_filter($extraCosts, function ($extraCost) use ($optionId) {
                return $extraCost->optionId === $optionId;
            });

            if (empty($extraCost)) {
                continue;
            }

            $extraCost = reset($extraCost);
            $optionName = $option->getName();

            $prices = get_object_vars($extraCost->prices);

            $setupPrice = $prices["setup-cost"];
            $filmPrice = $prices["film-cost"];

            if ($setupPrice > 0) {
                $additionalPositions["setup"][] = [
                    "optionName" => $optionName,
                    "price" => $setupPrice
                ];
            }

            if ($filmPrice > 0) {
                $additionalPositions["film"][] = [
                    "optionName" => $optionName,
                    "price" => $filmPrice
                ];
            }
        }

        return $additionalPositions;
    }
}
