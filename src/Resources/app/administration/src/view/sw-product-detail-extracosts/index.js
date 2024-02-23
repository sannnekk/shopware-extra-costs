import template from './sw-product-detail-extracosts.html.twig'

const { Criteria } = Shopware.Data
const { Context } = Shopware

const {
    mapState,
    mapGetters
} = Shopware.Component.getComponentHelper()

Shopware.Component.register('sw-product-detail-extracosts', {
    template,

    inject: ['repositoryFactory'],

    metaInfo() {
        return {
            title: 'Extra-Kosten'
        };
    },

    data() {
        return {
            properties: []
        }
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

        extraCosts() {
            return JSON.parse(this.product.extensions?.extraCostsExtension?.json || '[]')
        },

        productRepository() {
            return this.repositoryFactory.create('product')
        },

        propertyGroupRepository() {
            return this.repositoryFactory.create('property_group')
        },

        extraCostRepository() {
            return this.repositoryFactory.create('hmnet_extracosts_extension')
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
        product(newProduct, oldProduct) {
            if (newProduct.id === oldProduct.id)
                return
            
            this.setProperties()
        }
    },

    mounted() {
        this.mountedComponent()
    },

    methods: {
        mountedComponent() {
            this.setProperties()
        },

        setProperties() {
            if (this.extraCosts && this.extraCosts.length > 0) {
                this.properties = this.extraCosts
                this.addPropertyGroupNames()
                this.sortProperties()     
                return
            }

            this.product.configuratorSettings?.forEach(({ option }, index) => {
                const _prices = {
                    'setup-cost': 0,
                    'film-cost': 0,
                }

                this.quantities.forEach(quantity => 
                    _prices[quantity.id] = 0
                )
                
                this.properties.push({
                    id: String(index),
                    optionId: option.id,
                    name: option.groupId,
                    value: option.name,
                    prices: _prices,
                    groupId: option.groupId,
                })
            })

            this.addPropertyGroupNames()
            this.sortProperties()            
        },

        onInputChange() {
            this.save()
        },

        async save() {
            const criteria = new Criteria()
            criteria.addFilter(
                Criteria.equals('hmnet_extracosts_extension.product_id', this.product.id)
            )
            
            let ext = await this.extraCostRepository.search(criteria, Context.api)

            if (!ext || ext?.length === 0) {
                ext = this.extraCostRepository.create(Context.api)
                ext.productId = this.product.id
            }

            ext.json = JSON.stringify(this.properties)
            this.extraCostRepository.save(ext, Context.api)

            return ext
        },

        addPropertyGroupNames() {
            const criteria = new Criteria()
            const ids = this.properties.map(property => property.groupId)

            criteria.setIds(ids)

            this.propertyGroupRepository.search(criteria, Context.api).then((groups) => {
                this.properties = this.properties.map(property => {
                    const group = groups.find(group => group.id === property.groupId)
                    property.name = group.name
                    return property
                })
            })
        },

        sortProperties() {
            this.properties = this.properties.sort((a, b) => a.name > b.name ? 1 : -1)
        }
    }
})