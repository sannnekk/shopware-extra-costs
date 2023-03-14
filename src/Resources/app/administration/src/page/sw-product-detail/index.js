import template from './sw-product-detail.html.twig';

const { mapGetters } = Shopware.Component.getComponentHelper();

Shopware.Component.override('sw-product-detail', {
	template,

    computed: {
        
		...mapGetters('swProductDetail', [
			'isChild'
	    ])
    }
});