const { CheckboxControl, PanelBody, TextControl, Button, ColorPicker } = wp.components;
const { InspectorControls, MediaUpload, } = wp.blockEditor;
const { useSelect } = wp.data;
const { __ } = wp.i18n;
const el = wp.element.createElement;
const { registerBlockType } = wp.blocks;
ctcLiteParams.currency = 'usd';

registerBlockType('ctc-lite/ctc-lite-block', {

    title: __("CTC Lite", 'ctc-lite'),
    icon: 'cart',
    description: __("CTC Lite block to create product", "ctc-lite"),
    category: 'common',
    keywords: [__('eCommerce', 'ctc-lite'), __('Add Product', 'ctc-lite')],
    example: {},
    attributes: {
        productName: { type: 'string', default: 'Product Name' },
        productPrice: { type: 'string', default: '0.00' },
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
                el(TextControl, { name: 'name', className: 'inspect-product-name', type: "text", value: attributes.productName, label: `${__("Name", 'ctc-lite')} : `, onChange: value => setAttributes({ productName: value }), help: __('Enter product name.', 'ctc-lite') }),
                el(TextControl, { name: 'price', className: 'inspect-product-price', type: 'number', value: attributes.productPrice, label: `${__("Price", 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}) :`, onChange: value => setAttributes({ productPrice: parseFloat(value).toFixed(2) }), help: __('Enter product price.', 'ctc-lite') }),
                el(MediaUpload, {
                    title: __('Select Product Image', 'ctc-lite'),
                    onSelect: media => setAttributes({ profilePic: media.url }),
                    render: ({ open }) => el(Button, {
                        className: "ctcl-media-button dashicons-before dashicons-admin-media", onClick: open
                    }, __(" Select  Product Image", "ctc-lite")),
                }),
                el(ColorPicker, { onChangeComplete: colorVal => setAttributes({ buttonColor: colorVal.hex }) },)

            )
        )
    },
    save: ({ attributes }) => {
        return el('div', { className: 'ctcl-product-container' },
            el('div', { className: 'product-price-container' }, el('span', { className: 'price-label' }, `${__('Price', 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}): `), el('span', { className: 'product-price' }, attributes.productPrice)),
            el('div', { className: 'ctcl-quantity' }, el('span', { className: 'ctcl-minus-qty' }, '-'), el('input', { className: 'ctcl-qty', type: 'number', min: '1', value: attributes.dummyQty }), el('span', { className: 'ctcl-plus-qty' }, '+')),
            el(Button, { style: { backgroundColor: attributes.buttonColor }, className: ' dashicons-before dashicons-cart ctcl-add-cart', 'data-price': attributes.productPrice, 'data-qty': 1, 'data-name': attributes.productName, 'data-pic': attributes.profilePic, }, __("Add To Cart", 'ctc-lite')),
        )
    }

})
