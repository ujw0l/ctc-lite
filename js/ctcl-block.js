const { CheckboxControl, PanelBody, TextControl, Button, ColorPicker, SideBar, SelectControl } = wp.components;
const { InspectorControls, MediaUpload, } = wp.blockEditor;
const { PluginSidebar } = wp.editPost;
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
        dummyQty: { type: 'string', default: '1' },
        variation1Lable: { type: 'string', default: 'Variation 1' },
        variation2Lable: { type: 'string', default: 'Variation 2' },
        variation1: { type: 'Array', default: [] },
        variation2: { type: 'Array', default: [] },


    },
    edit: ({ attributes, setAttributes }) => {

        let variationOneItem = attributes.variation1.map(x => x.value);
        let variationTwoItem = attributes.variation2.map(x => x.value);

        return el('div', { className: 'ctcl-product-container' },
            el('div', { className: 'ctcl-gb-ac-container' },

                el('div', { className: 'product-price-container' }, el('span', { className: 'price-label' }, `${__('Price', 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}): `), el('span', { className: 'product-price' }, attributes.productPrice))),
            el(SelectControl, { className: 'ctcl-variation-select', label: `${attributes.variation1Lable} : `, className: 'ctcl-variation-1', options: attributes.variation1, }),
            el(SelectControl, { className: 'ctcl-variation-select', label: `${attributes.variation2Lable} : `, className: 'ctcl-variation-2', options: attributes.variation2, }),
            el('div', { className: 'ctcl-quantity' }, el('span', { onClick: () => setAttributes({ dummyQty: 2 <= parseInt(attributes.dummyQty) ? (parseInt(attributes.dummyQty) - 1) : 1 }), className: 'ctcl-minus-qty' }, '-'), el('input', { onChange: e => setAttributes({ dummyQty: e.target.value }), className: 'ctcl-qty', type: 'number', min: '1', value: attributes.dummyQty }), el('span', { onClick: () => setAttributes({ dummyQty: (parseInt(attributes.dummyQty) + 1) }), className: 'ctcl-plus-qty' }, '+')),
            el(Button, { style: { backgroundColor: attributes.buttonColor }, className: ' dashicons-before dashicons-cart ctcl-add-cart', 'data-price': attributes.productPrice, 'data-name': attributes.productName, 'data-pic': attributes.profilePic, }, __("Add To Cart", 'ctc-lite')),

            el(PluginSidebar, { name: 'ctcl-checkout', icon: 'store', title: __('Product Information', 'ctc-lite') },
                el(PanelBody, null,
                    el(TextControl, { name: 'name', className: 'inspect-product-name', type: "text", value: attributes.productName, label: `${__("Name", 'ctc-lite')} : `, onChange: value => setAttributes({ productName: value }), help: __('Enter product name.', 'ctc-lite') }),
                    el(TextControl, { name: 'price', className: 'inspect-product-price', type: 'number', value: attributes.productPrice, label: `${__("Price", 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}) :`, onChange: value => setAttributes({ productPrice: parseFloat(value).toFixed(2) }), help: __('Enter product price.', 'ctc-lite') }),
                    el(TextControl, { name: 'shipping', className: 'inspect-shipping-cost', type: 'number', value: attributes.shippingCost, label: `${__("Shipping Cost", 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}) :`, onChange: value => setAttributes({ shippingCost: parseFloat(value).toFixed(2) }), help: __('Enter shipping cost', 'ctc-lite') },),
                    el(TextControl, { name: 'variations1', className: "ctcl-setting-variation1", label: `${__("Variations 1", 'ctc-lite')} : `, value: variationOneItem.join(','), help: __('Comma separated.', 'ctc-lite'), onChange: val => setAttributes({ variation1: val.split(',').map(x => { return { value: x, label: x } }) }) }),
                    el(TextControl, { name: 'variations2', className: "ctcl-setting-variation2", label: `${__("Variations 2", 'ctc-lite')} : `, value: variationTwoItem.join(','), help: __('Comma separated.', 'ctc-lite'), onChange: val => setAttributes({ variation2: val.split(',').map(x => { return { value: x, label: x } }) }) }),
                    el(TextControl, { name: 'variations1label', className: "ctcl-setting-variation1-label", label: `${__("Variations 1 Label", 'ctc-lite')} : `, value: attributes.variation1Lable, onChange: val => setAttributes({ variation1Lable: val }) }),
                    el(TextControl, { name: 'variations2label', className: "ctcl-setting-variation2-label", label: `${__("Variations 2 Label", 'ctc-lite')} : `, value: attributes.variation2Lable, onChange: val => setAttributes({ variation2Lable: val }) }),
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


        const SelectVariation = ({ selectOptions, variationName, label }) => {

            if (0 < selectOptions.length) {

                return el('div', { className: `ctcl-${variationName.replace(' ', '-')}-cont` },
                    el('label', { htmlFor: `ctcl-${variationName.replace(' ', '-')}`, className: `ctcl-${variationName.replace(' ', '-')}-label` }, `${label} : `),
                    el('select', { id: `ctcl-${variationName.replace(' ', '-')}`, className: `ctcl-${variationName.replace(' ', '-')}`, }, selectOptions.map(x => el('option', { value: x.value }, x.value),)),
                );

            } else {
                return '';
            }

        };

        return el('div', { className: 'ctcl-product-container' },
            el('div', { className: 'product-price-container' }, el('span', { className: 'price-label' }, `${__('Price', 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}): `), el('span', { className: 'product-price' }, attributes.productPrice)),

            el(SelectVariation, { selectOptions: attributes.variation1, variationName: 'Variation 1', label: attributes.variation1Lable }),
            el(SelectVariation, { selectOptions: attributes.variation2, variationName: 'Variation 2', label: attributes.variation2Lable }),
            el('label', { className: 'ctcl-product-qty' }, `${__('Qty ')} : `),
            el('div', { className: 'ctcl-quantity' }, el('span', { className: 'ctcl-minus-qty' }, '-'), el('input', { className: 'ctcl-qty', type: 'number', min: '1', value: attributes.dummyQty }), el('span', { className: 'ctcl-plus-qty' }, '+')),
            el(Button, { style: { backgroundColor: attributes.buttonColor }, className: ' dashicons-before dashicons-cart ctcl-add-cart', 'data-price': attributes.productPrice, 'data-qty': 1, 'data-name': attributes.productName, 'data-shipping-cost': attributes.shippingCost, 'data-pic': attributes.profilePic, }, __("Add To Cart", 'ctc-lite')),
        )
    }

});



/**
 * Create checkout block
 */

/**
 * checkout page block componenet
 */

const CheckoutPage = (props) => {

}

registerBlockType('ctc-lite/ctc-lite-checkout-block', {
    title: __("CTC Lite Cart", 'ctc-lite'),
    icon: 'cart',
    description: __("CTC Lite block to create checkout page", "ctc-lite"),
    category: 'common',
    keywords: [__('eCommerce', 'ctc-lite'), __('Checkout', 'ctc-lite')],
    example: {},
    attributes: {
        buttonColor: { type: 'string', default: 'rgba(61,148,218,1)' },
        paymentPage: { type: 'string', default: '' }
    },

    edit: ({ attributes, setAttributes }) => {
        return el('div', { className: 'ctcl-checkout-container' },
            el('div', { className: 'ctcl-checkout' },
                el('form', { id: 'ctcl-checkout-form', },
                    el('div', { className: 'ctcl-product-list' },
                        el('h5', { className: 'ctcl-product-list-header' }, __('Product List :', 'ctc-lite')),
                        el('div', { className: 'ctcl-product-list-container' },
                            el('p', { className: 'ctcl-product-list-content' }, __('Contains Product List', 'ctc-lite')),
                        )),
                    el('fieldset', { className: 'ctcl-contact-form-fieldset' },
                        el('legend', { className: 'ctcl-contact-form-legend' }, __('Shipping/Contact Info', 'ctc-lite')),
                        el('div', { className: 'ctcl-input-container' },
                            el('div', { className: 'ctcl-address-row' },
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-first-name', type: 'text', label: __('*First Name :', 'ctc-lite'), },),
                                ),
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-last-name', type: 'text', label: __('*Last Name :', 'ctc-lite'), },),
                                ),
                            ),
                            el('div', { className: 'ctcl-address-row' },
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-add-1', type: 'text', label: __('*Street Address 1 :', 'ctc-lite') }),
                                ),
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-add-2', type: 'text', label: __('Street Address 2 :', 'ctc-lite') }),
                                ),
                            ),

                            el('div', { className: 'ctcl-address-row' },
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-city', type: 'text', label: __('*City :', 'ctc-lite') }),
                                ),
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-state', type: 'text', label: __('*State/Province :', 'ctc-lite') }),
                                ),
                            ),
                            el('div', { className: 'ctcl-address-row' },
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctccl-co-zip', type: 'text', label: __('*Zip Code :', 'ctc-lite') }),
                                ),
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-country', type: 'text', label: __('*Country :', 'ctc-lite') }),
                                ),
                            ),
                            el('div', { className: 'ctcl-address-row' },
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-email', type: 'email', label: __('*Email Address :', 'ctc-lite') }),
                                ),
                                el('div', { className: 'ctcl-address-col' },
                                    el(TextControl, { className: 'ctcl-co-phone-number', type: 'tel', label: __('Phone Number :', 'ctc-lite') }),
                                ),
                            )),
                        el('div', { className: 'ctcl-address-row' },
                            el('div', { className: 'ctcl-co-instruction-label-container' },
                                el('label', { htmlFor: "ctcl-co-instruction", className: "ctcl-co-instruction-label" }, __("Special Instruction : ", "ctc-lite")),
                            ),

                            el('textarea', { id: "ctcl-co-instruction", className: 'ctcl-co-instruction', cols: '52', rows: '5', label: __('Special Instructions :', 'ctc-lite') }),
                        ),
                    ),
                    el('div', { className: 'ctcl-required-message' }, el('i', null, __('* Are Required Fields.', 'ctc-lite'))),
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

                    el(PluginSidebar, { name: 'ctcl-checkout', icon: 'store', title: __('Checkout page setting', 'ctc-lite') },
                        el(PanelBody, null,
                            el(TextControl, { value: attributes.paymentPage, onChange: val => setAttributes({ paymentPage: val }), className: 'ctcl-co-payment-page', type: 'text', label: __('URL of page with ctc lite payment processing block :', 'ctc-lite') }),
                            el('i', { className: "ctcl-colorpicker-label" }, __('Select button color', 'ctc-lite')),
                            el(ColorPicker, { onChangeComplete: colorVal => setAttributes({ buttonColor: colorVal.hex }) },))
                    )

                )))

    },
    save: ({ attributes }) => {
        return el('div', null,
            el('div', { className: 'ctcl-checkout' },
                el('form', { id: 'ctcl-checkout-from', method: 'post', action: attributes.paymentPage },
                    el('div', { className: 'ctcl-product-list' },
                        el('h5', { className: 'ctcl-product-list-header' }, __('Product List', 'ctc-lite')),
                        el('div', { id: 'ctcl-checkout-product-list', className: 'ctcl-product-list-container' },
                            el('p', { className: 'ctcl-product-loading dashicons-before dashicons-cart' }, __('Loading ...', 'ctc-lite')),
                            el('p', { className: 'ctcl-product-list-content  dashicons-before dashicons-cart', style: { display: 'none' } }, __('Empty Cart', 'ctc-lite')),
                        )),
                    el('fieldset', { className: 'ctcl-contact-form-fieldset' },
                        el('legend', { className: 'ctcl-contact-form-legend' }, __('Contact/Shipping Info', 'ctc-lite')),
                        el('div', { className: "ctcl-address-row" },
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-first-name", className: "ctcl-co-fn-label" }, __("*First  Name :", 'ctc-lite')),
                                el('input', { className: 'ctcl-co-first-name', type: 'text', required: true, name: 'ctcl-co-first-name' }),
                            ),
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-last-name", className: "ctcl-co-ln-label" }, __("*Last Name :", 'ctc-lite')),
                                el('input', { className: 'ctcl-co-last-name', type: 'text', required: true, name: 'ctcl-co-last-name' }),
                            ),
                        ),
                        el('div', { className: 'ctcl-address-row' },
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-add-1", className: "ctcl-co-add-1-label" }, __("*Street Address 1 :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-add-1', className: 'ctcl-co-add-1', required: true, name: "checkout-street-address-1", type: 'text', }),
                            ),
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-add-2", className: "ctcl-co-add-2-label" }, __("Street Address 2 :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-add-2', className: 'ctcl-co-add-2', name: "checkout-street-address-2", type: 'text', }),
                            ),
                        ),
                        el('div', { className: 'ctcl-address-row' },
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-city", className: "ctcl-co-city-label" }, __("*City :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-city', className: 'ctcl-co-city', required: true, name: "checkout-city", type: 'text', }),
                            ),
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-state", className: "ctcl-co-state-label" }, __("*State/Province :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-state', className: 'ctcl-co-state', required: true, name: "checkout-state", type: 'text', }),
                            ),
                        ),
                        el('div', { className: 'ctcl-address-row' },
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-zip", className: "ctcl-co-zip-label" }, __("*Zip Code :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-zip', className: 'ctccl-co-zip', required: true, name: "checkout-zip-code", type: 'text', }),
                            ),
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-country", className: "ctcl-co-country-label" }, __("*Country :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-country', className: 'ctcl-co-country', required: true, name: "checkout-country", type: 'text', }),
                            ),
                        ),
                        el('div', { className: 'ctcl-address-row' },
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-email", className: "ctcl-co-email-label" }, __("*Email Address :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-email', className: 'ctcl-co-email', required: true, name: "checkout-email-address", type: 'email', }),
                            ),
                            el('div', { className: 'ctcl-address-col' },
                                el('label', { htmlFor: "ctcl-co-phone-number", className: "ctcl-co-phone-number-label" }, __("Phone Number :", 'ctc-lite')),
                                el('input', { id: 'ctcl-co-phone-number', className: 'ctcl-co-phone-number', name: "checkout-phone-number", type: 'tel', }),
                            ),
                        ),
                        el('div', { className: 'ctcl-address-row' },
                            el('div', { className: 'ctcl-co-instruction-label' },
                                el('label', { htmlFor: "ctcl-co-instruction", className: "ctcl-co-instruction-label" }, __("Special Instruction :", 'ctc-lite')),
                            ),
                            el('textarea', { id: 'ctcl-co-instruction', className: 'ctcl-co-instruction', name: "checkout-special-instruction", cols: '68', rows: '10', }),
                        )),
                    el('div', { className: 'ctcl-required-message' }, el('i', null, __('* Are Required Fields.', 'ctc-lite'))),
                    el('div', { className: 'ctcl-shipping-options' },
                        el('strong', { className: 'ctcl-shipping-options-header' }, __('Shipping Options', 'ctc-lite')),
                        el('div', { id: 'ctcl-checkout-shipping-options', className: 'ctcl-shipping-options-container' }, '[ctcl_shipping_options]'
                        )),
                    el('div', { className: 'ctcl-payment-options' },
                        el('strong', { className: 'ctcl-payment-options-header' }, __('Payment Options', 'ctc-lite')),
                        el('div', { id: 'ctcl-checkout-payment-option', className: 'ctcl-payment-options-container' }, '[ctcl_payment_options]'
                        )),
                    el('input', { type: 'submit', name: 'ctcl-checkout-button', style: { backgroundColor: attributes.buttonColor }, className: 'ctcl-checkout-button', value: __("Check Out", 'ctc-lite') }),
                ),
            ),
        )

    }
})

/**
 * Register order prosseing block
 */

registerBlockType('ctc-lite/ctc-lite-order-processing', {

    title: __('CTC Lite Order Processing', 'ctc-lite'),
    icon: 'money',
    description: __("CTC Lite block to create Order processing page", "ctc-lite"),
    category: 'common',
    keywords: [__('eCommerce', 'ctc-lite'), __('Order Processing', 'ctc-lite')],
    example: {},
    attributes: {},

    edit: () => {
        return el('div', { className: 'ctcl-order-processing-block' },
            el('h5', { className: 'ctcl-order-processing-header' }, __('Order Processing', 'ctc-lite')),
            el('p', { className: 'ctc-order-message' }, __('Contains Order outcome message.', 'ctc-lite')),
        );

    },
    save: () => el('div', { className: 'ctcl-order-message' }, '[ctcl_order_page]'),



});