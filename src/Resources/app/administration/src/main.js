import './page/sw-product-detail';
import './view/sw-product-detail-extracosts';

// Here you create your new route, refer to the mentioned guide for more information
Shopware.Module.register('sw-new-tab-extracosts', {
    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'sw.product.detail.extracosts',
                path: '/sw/product/detail/:id/extracosts',
                component: 'sw-product-detail-extracosts',
                meta: {
                    parentPath: "sw.product.index"
                }
            });
        }
        next(currentRoute);
    }
});