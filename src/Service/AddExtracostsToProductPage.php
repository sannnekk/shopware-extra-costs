<?php

declare(strict_types=1);

namespace HMnetProductExtraCosts\Service;

use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Shopware\Storefront\Page\Product\ProductPage;
use Shopware\Core\Content\Product\ProductEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Framework\Context;
use HMnetProductExtraCosts\Helper\GetAdditionalPositions;
use HMnetProductExtraCosts\Helper\GetCurrentPriceRangeId;

class AddExtracostsToProductPage implements EventSubscriberInterface
{
    protected EntityRepository $productRepository;

    private ProductPage $page;
    private Context $context;
    private array $extraCosts;
    private ProductEntity $product;
    private array $additionalPrices;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded'
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $this->page = $event->getPage();
        $this->context = $event->getContext();
        $this->product = $this->page->getProduct();

        // check if its a variant
        if (is_null($this->product->getParentId())) {
            return;
        }

        $this->addExtraCosts();

        $this->additionalPrices = GetAdditionalPositions::getAdditionalPositions($this->product, $this->extraCosts);

        $this->page->addExtension('extraCosts', new ArrayStruct($this->extraCosts));
        $this->page->addExtension('additionalPositions', new ArrayStruct($this->additionalPrices));
        //$this->page->addExtension('currentPriceRangeId', GetCurrentPriceRangeId::getCurrentPriceRangeId($this->product));
    }

    private function addExtraCosts(): void
    {
        $parentProduct = $this->getParentProduct($this->product->getParentId(), $this->context);

        if (is_null($parentProduct)) {
            return;
        }

        $extension = $parentProduct->getExtensions()['extraCostsExtension'];

        if (is_null($extension)) {
            return;
        }

        $this->extraCosts = json_decode($extension->getJson()) ?? [];
    }

    private function getParentProduct($parentId)
    {
        $criteria = new Criteria([$parentId]);
        $parentProduct = $this->productRepository->search($criteria, $this->context)->first();

        return $parentProduct;
    }
}
