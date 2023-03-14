import template from './sw-product-detail-extracosts.html.twig'
const { Criteria } = Shopware.Data
const { Component, Mixin, Context } = Shopware

const {
    mapState,
    mapGetters
} = Shopware.Component.getComponentHelper()

const COLOR_OPTION_GROUP_ID = '7e0dcacf7f3d4d039de8ec33ede83181'

Shopware.Component.register('sw-product-detail-extracosts', {
    template,

    inject: ['repositoryFactory'],

    metaInfo() {
        return {
            title: 'Extra-Kosten'
        };
    },

    data() {
        return {}
    },

    computed: {
        available() {
            return this.product.configuratorSettings?.length > 0
        },

        columns() {
            const defaults = {
                allowResize: true,
                primary: true,
                visible: true,
                width: '110px',
                rawData: false
            };

            const columns = [{
                property: 'option-name',
                label: 'Ausprägungen',
                ...defaults
            }];

            this.quantities.forEach(quantity => columns.push({
                property: `quantity-${quantity.id}`,
                label: `${quantity.min} - ${quantity.max || '∞'}`,
                ...defaults
            }));

            columns.push({
                property: `setup-cost`,
                label: `Einrichtungskosten`,
                ...defaults
            }, {
                property: `film-cost`,
                label: `Filmkosten`,
                ...defaults
            })


            return columns;
        },

        prices() {
            return this.product.prices || []
        },

        quantities() {
            const _quantities = []

            this.prices.forEach(price => _quantities.push({
                id: price.id,
                min: price.quantityStart,
                max: price.quantityEnd
            }))

            return _quantities
        },

        properties() {
            const _properties = []

            this.product.configuratorSettings?.forEach(async ({ option }, index) => {
                const _prices = {
                    'setup-cost': 0,
                    'film-cost': 0,
                }

                this.quantities.forEach(quantity => 
                    _prices[quantity.id] = 0
                )

                if (option.groupId !== COLOR_OPTION_GROUP_ID) {
                    _properties.push({
                        id: String(index),
                        name: option.groupId,
                        value: option.name,
                        prices: _prices,
                        groupId: option.groupId,
                    })
                }
            })

            return _properties.sort((a, b) => a.name > b.name ? 1 : -1)
        },

        ...mapState('swProductDetail', [
            'product',
            'loading'
        ]),

        ...mapGetters('swProductDetail', [
            'isChild'
        ])
    },

    watch: {
        product: (a, b) => console.log('product changed', a, b),
    },

    mounted() {
        this.mountedComponent();
    },

    methods: {
        mountedComponent() {
            console.log(this.loading)
        },

        async getOptionGroupName(groupId) {
            const criteria = new Criteria(1, 1);
            
            criteria.addFilter(Criteria.equalsAny('id', groupId));
            criteria.addAssociation('group');

            return await this.optionRepository.search(criteria, Context.api).first().group.name || ''
        },

        onPriceChange(...args) {
            console.log('onPriceChange', args);
        },
    }
});