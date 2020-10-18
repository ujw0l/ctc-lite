const { CheckboxControl, PanelBody, TextControl, Button, ColorPicker } = wp.components;
const { InspectorControls, MediaUpload, } = wp.blockEditor;
const { useSelect } = wp.data;
const { __ } = wp.i18n;
const el = wp.element.createElement;
const { registerBlockType } = wp.blocks;
ctcLiteParams.currency = 'usd';

/**
 * Create add product block
 */
registerBlockType('ctc-lite/ctc-lite-product-block', {

    title: __("CTC Lite Product", 'ctc-lite'),
    icon: 'products',
    description: __("Create product", "ctc-lite"),
    category: 'common',
    keywords: [__('eCommerce', 'ctc-lite'), __('Add Product', 'ctc-lite')],
    example: {},
    attributes: {
        productName: { type: 'string', default: 'Product Name' },
        productPrice: { type: 'string', default: '0.00' },
        shippingCost: { type: 'string', default: '0.00' },
        profilePic: { type: 'string', default: ctcLiteParams.defaultPic },
        buttonColor: { type: 'string', default: 'rgba(61,148,218,1)' },
        dummyQty: { type: 'string', default: '1' }

    },
    edit: ({ attributes, setAttributes }) => {
        return el('div', { className: 'ctcl-product-container' },
            el('div', null,
                el('div', { className: 'product-price-container' }, el('span', { className: 'price-label' }, `${__('Price', 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}): `), el('span', { className: 'product-price' }, attributes.productPrice))),
            el('div', { className: 'ctcl-quantity' }, el('span', { onClick: () => setAttributes({ dummyQty: 2 <= parseInt(attributes.dummyQty) ? (parseInt(attributes.dummyQty) - 1) : 1 }), className: 'ctcl-minus-qty' }, '-'), el('input', { onChange: e => setAttributes({ dummyQty: e.target.value }), className: 'ctcl-qty', type: 'number', min: '1', value: attributes.dummyQty }), el('span', { onClick: () => setAttributes({ dummyQty: (parseInt(attributes.dummyQty) + 1) }), className: 'ctcl-plus-qty' }, '+')),
            el(Button, { style: { backgroundColor: attributes.buttonColor }, className: ' dashicons-before dashicons-cart ctcl-add-cart', 'data-price': attributes.productPrice, 'data-name': attributes.productName, 'data-pic': attributes.profilePic, }, __("Add To Cart", 'ctc-lite')),

            el(InspectorControls, null,
                el(PanelBody, null,
                    el(TextControl, { name: 'name', className: 'inspect-product-name', type: "text", value: attributes.productName, label: `${__("Name", 'ctc-lite')} : `, onChange: value => setAttributes({ productName: value }), help: __('Enter product name.', 'ctc-lite') }),
                    el(TextControl, { name: 'price', className: 'inspect-product-price', type: 'number', value: attributes.productPrice, label: `${__("Price", 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}) :`, onChange: value => setAttributes({ productPrice: parseFloat(value).toFixed(2) }), help: __('Enter product price.', 'ctc-lite') }),
                    el(TextControl, { name: 'price', className: 'inspect-product-price', type: 'number', value: attributes.productPrice, label: `${__("Shipping Cost", 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}) :`, onChange: value => setAttributes({ shippingCost: parseFloat(value).toFixed(2) }), help: __('Enter shipping cost', 'ctc-lite') }),
                    el(MediaUpload, {
                        title: __('Select Product Image', 'ctc-lite'),
                        onSelect: media => setAttributes({ profilePic: media.url }),
                        render: ({ open }) => el(Button, {
                            className: "ctcl-media-button dashicons-before dashicons-admin-media", onClick: open
                        }, __(" Select  Product Image", "ctc-lite")),
                    }),
                    el('i', { className: "ctcl-colorpicker-label" }, __('Select button color', 'ctc-lite')),
                    el(ColorPicker, { onChangeComplete: colorVal => setAttributes({ buttonColor: colorVal.hex }) },)

                ))
        )
    },
    save: ({ attributes }) => {
        return el('div', { className: 'ctcl-product-container' },
            el('div', { className: 'product-price-container' }, el('span', { className: 'price-label' }, `${__('Price', 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}): `), el('span', { className: 'product-price' }, attributes.productPrice)),
            el('div', { className: 'ctcl-quantity' }, el('span', { className: 'ctcl-minus-qty' }, '-'), el('input', { className: 'ctcl-qty', type: 'number', min: '1', value: attributes.dummyQty }), el('span', { className: 'ctcl-plus-qty' }, '+')),
            el(Button, { style: { backgroundColor: attributes.buttonColor }, className: ' dashicons-before dashicons-cart ctcl-add-cart', 'data-price': attributes.productPrice, 'data-qty': 1, 'data-name': attributes.productName, 'data-shipping-cost': attributes.shippingCost, 'data-pic': attributes.profilePic, }, __("Add To Cart", 'ctc-lite')),
        )
    }

});



/**
 * Create checkout block
 */

registerBlockType('ctc-lite/ctc-lite-checkout-block', {
    title: __("CTC Lite Cart", 'ctc-lite'),
    icon: 'cart',
    description: __("CTC Lite block to create checkout page", "ctc-lite"),
    category: 'common',
    keywords: [__('eCommerce', 'ctc-lite'), __('Checkout', 'ctc-lite')],
    example: {},
    attributes: {
        buttonColor: { type: 'string', default: 'rgba(61,148,218,1)' },
    },

    edit: ({ attributes, setAttributes }) => {
        return el('div', { className: 'ctcl-checkout' },
            el('form', { id: 'ctcl-checkout-from', },
                el('div', { className: 'ctcl-product-list' },
                    el('h5', { className: 'ctcl-product-list-header' }, __('Product List', 'ctc-lite')),
                    el('div', { className: 'ctcl-product-list-container' },
                        el('p', { className: 'ctcl-product-list-content' }, __('Contains Product List', 'ctc-lite')),
                    )),


                el(TextControl, { className: 'ctcl-co-name', type: 'text', label: __('Full Name', 'ctc-lite'), },),
                el('div', { className: 'ctcl-address-row' },
                    el(TextControl, { className: 'ctcl-co-add-1', type: 'text', label: __('Street Address 1', 'ctc-lite') }),
                    el(TextControl, { className: 'ctcl-co-add-2', type: 'text', label: __('Street Address 2', 'ctc-lite') }),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el(TextControl, { className: 'ctcl-co-city', type: 'text', label: __('City', 'ctc-lite') }),
                    el(TextControl, { className: 'ctcl-co-state', type: 'text', label: __('State/Province', 'ctc-lite') }),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el(TextControl, { className: 'ctccl-co-zip', type: 'text', label: __('Zip Code', 'ctc-lite') }),
                    el(TextControl, { className: 'ctcl-co-country', type: 'text', label: __('Country', 'ctc-lite') }),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el(TextControl, { className: 'ctcl-co-email', type: 'email', label: __('Email Address', 'ctc-lite') }),
                    el(TextControl, { className: 'ctcl-co-phone-number', type: 'tel', label: __('Phone Number', 'ctc-lite') }),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el('textarea', { className: 'ctcl-co-instruction', cols: '50', rows: '10', label: __('Special Instructions', 'ctc-lite') }),
                ),
                el('div', { className: 'ctcl-shipping-options' },
                    el('h5', { className: 'ctcl-shipping-options-header' }, __('Shipping Options', 'ctc-lite')),
                    el('div', { className: 'ctcl-shipping-options-container' },
                        el('p', { className: 'ctcl-shipping-options-content' }, __('Contains Shipping Options', 'ctc-lite')),
                    )),
                el('div', { className: 'ctcl-payment-options' },
                    el('h5', { className: 'ctcl-payment-options-header' }, __('Payment Options', 'ctc-lite')),
                    el('div', { className: 'ctcl-payment-options-container' },
                        el('p', { className: 'ctcl-payment-options-content' }, __('Contains Payment Options.', 'ctc-lite')),
                    )),

                el(Button, { style: { backgroundColor: attributes.buttonColor }, className: 'ctcl-checkout-button' }, __("Check Out", 'ctc-lite')),

                el(InspectorControls, null,
                    el('i', { className: "ctcl-colorpicker-label" }, __('Select button color', 'ctc-lite')),
                    el(ColorPicker, { onChangeComplete: colorVal => setAttributes({ buttonColor: colorVal.hex }) },))

            ))

    },
    save: () => { }
})