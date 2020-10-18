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
                    el('h5', { className: 'ctcl-product-list-header' }, __('Product List :', 'ctc-lite')),
                    el('div', { className: 'ctcl-product-list-container' },
                        el('p', { className: 'ctcl-product-list-content' }, __('Contains Product List', 'ctc-lite')),
                    )),

                el('div', { className: 'ctcl-input-container' },
                    el(TextControl, { className: 'ctcl-co-name', type: 'text', label: __('Full Name :', 'ctc-lite'), },),
                    el('div', { className: 'ctcl-address-row' },
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctcl-co-add-1', type: 'text', label: __('Street Address 1 :', 'ctc-lite') }),
                        ),
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctcl-co-add-2', type: 'text', label: __('Street Address 2 :', 'ctc-lite') }),
                        ),
                    ),
                    el('div', { className: 'ctcl-address-row' },
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctcl-co-city', type: 'text', label: __('City :', 'ctc-lite') }),
                        ),
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctcl-co-state', type: 'text', label: __('State/Province :', 'ctc-lite') }),
                        ),
                    ),
                    el('div', { className: 'ctcl-address-row' },
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctccl-co-zip', type: 'text', label: __('Zip Code :', 'ctc-lite') }),
                        ),
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctcl-co-country', type: 'text', label: __('Country :', 'ctc-lite') }),
                        ),
                    ),
                    el('div', { className: 'ctcl-address-row' },
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctcl-co-email', type: 'email', label: __('Email Address :', 'ctc-lite') }),
                        ),
                        el('div', { className: 'ctcl-address-col' },
                            el(TextControl, { className: 'ctcl-co-phone-number', type: 'tel', label: __('Phone Number :', 'ctc-lite') }),
                        ),
                    ),
                    el('div', { className: 'ctcl-address-row' },
                        el('label', { for: "ctcl-co-instruction", className: "ctcl-co-instruction-label" }, __("Special Instruction : ", "ctc-lite")),
                        el('textarea', { id: "ctcl-co-instruction", className: 'ctcl-co-instruction', cols: '50', rows: '10', label: __('Special Instructions :', 'ctc-lite') }),
                    ),
                ),
                el('div', { className: 'ctcl-shipping-options' },
                    el('h5', { className: 'ctcl-shipping-options-header' }, __('Shipping Options :', 'ctc-lite')),
                    el('div', { className: 'ctcl-shipping-options-container' },
                        el('p', { className: 'ctcl-shipping-options-content' }, __('Contains Shipping Options', 'ctc-lite')),
                    )),
                el('div', { className: 'ctcl-payment-options' },
                    el('h5', { className: 'ctcl-payment-options-header' }, __('Payment Options :', 'ctc-lite')),
                    el('div', { className: 'ctcl-payment-options-container' },
                        el('p', { className: 'ctcl-payment-options-content' }, __('Contains Payment Options.', 'ctc-lite')),
                    )),

                el(Button, { style: { backgroundColor: attributes.buttonColor }, className: 'ctcl-checkout-button' }, __("Check Out", 'ctc-lite')),

                el(InspectorControls, null,
                    el('i', { className: "ctcl-colorpicker-label" }, __('Select button color', 'ctc-lite')),
                    el(ColorPicker, { onChangeComplete: colorVal => setAttributes({ buttonColor: colorVal.hex }) },))

            ))

    },
    save: ({ attributes }) => {
        return el('div', { className: 'ctcl-checkout' },
            el('form', { id: 'ctcl-checkout-from', },
                el('div', { className: 'ctcl-product-list' },
                    el('h5', { className: 'ctcl-product-list-header' }, __('Product List', 'ctc-lite')),
                    el('div', { id: 'ctcl-checkout-product-list', className: 'ctcl-product-list-container' },
                        el('p', { className: 'ctcl-product-list-content' }, __('Loading ... ', 'ctc-lite')),
                    )),
                el('div', { className: "ctcl-co-name-container" },
                    el('label', { for: "ctcl-co-name", className: "ctcl-co-add-1-label" }, __("Full Name :", 'ctc-lite')),
                    el('input', { className: 'ctcl-co-name', type: 'text', required: "reuired" }),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-add-1", className: "ctcl-co-add-1-label" }, __("Street Address 1 :", 'ctc-lite')),
                        el('input', { id: 'ctcl-co-add-1', className: 'ctcl-co-add-1', required: "reuired", name: "checkout-street-address-1", type: 'text', }),
                    ),
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-add-1", className: "ctcl-co-add-2-label" }, __("Street Address 2 :", 'ctc-lite')),
                        el('input', { id: 'ctcl-co-add-2', className: 'ctcl-co-add-2', name: "checkout-street-address-2", type: 'text', }),
                    ),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-city", className: "ctcl-co-city-label" }, __("City :", 'ctc-lite')),
                        el('input', { id: 'ctcl-co-city', className: 'ctcl-co-city', required: "reuired", name: "checkout-city", type: 'text', }),
                    ),
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-state", className: "ctcl-co-state-label" }, __("State/Province :", 'ctc-lite')),
                        el('input', { id: 'ctcl-co-state', className: 'ctcl-co-state', required: "reuired", name: "checkout-state", type: 'text', }),
                    ),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-zip", className: "ctcl-co-zip-label" }, __("Zip Code :", 'ctc-lite')),
                        el('input', { id: 'ctccl-co-zip', className: 'ctccl-co-zip', required: "reuired", name: "checkout-zip-code", type: 'text', }),
                    ),
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-country", className: "ctcl-co-country-label" }, __("Country :", 'ctc-lite')),
                        el('input', { id: 'ctcl-co-country', className: 'ctcl-co-country', required: "reuired", name: "checkout-country", type: 'text', }),
                    ),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-email", className: "ctcl-co-email-label" }, __("Email Address :", 'ctc-lite')),
                        el('input', { id: 'ctcl-co-email', className: 'ctcl-co-email', required: "reuired", name: "checkout-email-address", type: 'email', }),
                    ),
                    el('div', { className: 'ctcl-address-col' },
                        el('label', { for: "ctcl-co-phone-number", className: "ctcl-co-phone-number-label" }, __("Phone Number :", 'ctc-lite')),
                        el('input', { id: 'ctcl-co-phone-number', className: 'ctcl-co-phone-number', name: "checkout-phone-number", type: 'tel', }),
                    ),
                ),
                el('div', { className: 'ctcl-address-row' },
                    el('label', { for: "ctcl-co-instruction", className: "ctcl-co-instruction-label" }, __("Special Instruction :", 'ctc-lite')),
                    el('textarea', { id: 'ctcl-co-instruction', className: 'ctcl-co-instruction', required: "reuired", name: "checkout-special-instruction", cols: '50', rows: '10', }),
                ),
                el('div', { className: 'ctcl-shipping-options' },
                    el('h5', { className: 'ctcl-shipping-options-header' }, __('Shipping Options', 'ctc-lite')),
                    el('div', { id: 'ctcl-checkout-shipping-options', className: 'ctcl-shipping-options-container' },
                        el('p', { className: 'ctcl-shipping-options-content' }, __('Loading ...', 'ctc-lite')),
                    )),
                el('div', { className: 'ctcl-payment-options' },
                    el('h5', { className: 'ctcl-payment-options-header' }, __('Payment Options', 'ctc-lite')),
                    el('div', { id: 'ctcl-checkout-payment-option', className: 'ctcl-payment-options-container' },
                        el('p', { className: 'ctcl-payment-options-content' }, __('Loading ...', 'ctc-lite')),
                    )),

                el(Button, { style: { backgroundColor: attributes.buttonColor }, className: 'ctcl-checkout-button' }, __("Check Out", 'ctc-lite')),
            ))

    }
})