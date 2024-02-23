<?php

declare(strict_types=1);

namespace HMnetProductExtraCosts\Helper;

class GetCurrentPriceRangeId
{
    public static function getCurrentPriceRangeId($product)
    {
        $prices = $product->getPrices();
        $minPurchase = $product->getMinPurchase();

        foreach ($prices as $price) {
            $start = $price->getQuantityStart();
            $end = $price->getQuantityEnd();

            if ($minPurchase >= $start && $minPurchase <= $end) {
                return $price->getId();
            }
        }

        return null;
    }
}
