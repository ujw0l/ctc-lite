
const { useEffect } = React;
const {  RangeControl,CheckboxControl, PanelBody, TextControl, Button, ColorPicker, SideBar, SelectControl,ToggleControl } = wp.components;
const { InspectorControls, MediaUpload, InnerBlocks, useBlockProps } = wp.blockEditor;
const { PluginSidebar } = wp.editPost;
const { __ } = wp.i18n;
const el = wp.element.createElement;
const { registerBlockType } = wp.blocks;



/**
 *  @since 1.0.0
*
 * Create add product block
 */
registerBlockType('ctc-lite/ctc-lite-product-block', {

    title: __("CTC Lite Product", 'ctc-lite'),
    icon: 'products',
    description: __("Create product", "ctc-lite"),
    category: 'ctc-lite-blocks',
    keywords: [__('eCommerce', 'ctc-lite'), __('Add Product', 'ctc-lite')],
    example: {},
    attributes: {
        productName: { type: 'string', default: 'Product Name' },
        productPrice: { type: 'string', default: '0.00' },
        shippingCost: { type: 'string', default: '0.00' },
        profilePic: { type: 'string', default: ctcLiteParams.defaultPic },
        buttonColor: { type: 'string', default: 'rgba(61,148,218,1)' },
        dummyQty: { type: 'string', default: '1' },
        variation1Lable: { type: 'string', default: __('Variation 1', 'ctc-lite') },
        variation2Lable: { type: 'string', default: __('Variation 2','ctc-lite') },
        variation1: { type: 'Array', default: [] },
        variation2: { type: 'Array', default: [] },
        varDiffPrice:{type:'Boolean', default:false},
        varDiffImage:{type:"Boolean",default:false},
        outOfStock:{ type:"Boolean",default:false },
        disableAddToCartBtn:{type:"Boolean",default:false},
        addToCartMsg:{type:"String",default:__('Add To Cart','ctc-lite')},
        preOrderAvailable:{type:"Boolean",default:false},
        postId :{type:'Number', default:0}
       

    },
    edit: ({ attributes, setAttributes,clientId }) => {

       setAttributes({postId :wp.data.select("core/editor").getCurrentPostId() })

     
    
        setAttributes({clntId:clientId})
        let variationOneItem = attributes.variation1.map(x => x.label);
        let variationTwoItem = attributes.variation2.map(x => x.label);


        return el('div', { className: 'ctcl-product-container' },
            el('div', { className: 'ctcl-gb-ac-container' },

            attributes.preOrderAvailable && el('i',{style:{color:'rgb(255, 0, 0)',fontSize:'10px'}}, __('Out of Stock, Pre order available ','ctc-lite')),
                el('div', { className: 'product-price-container' }, el('span', { className: 'price-label' }, `${__('Price', 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}): `), el('span', { className: 'product-price' }, attributes.productPrice))),
            1 < attributes.variation1.length && el(SelectControl, { 'id': 'ctcl-variation-1', label: `${attributes.variation1Lable} : `, options: attributes.variation1, onChange:val=> document.querySelector('.product-price').innerHTML = parseFloat(val.split('~')[1]).toFixed(2)  }) ,
            1 < attributes.variation2.length && el(SelectControl, { 'id': 'ctcl-variation-2', label: `${attributes.variation2Lable} : `, options: attributes.variation2, onChange:val => {

                let mainImg =  document.querySelector('.ctcl-image-gallery-block .ctclig-main-image');
                if(mainImg != null){
                    mainImg.style.backgroundImage = `url("${val.split('~')[1]}")`;
                }

            }  }) ,
            el('div', { className: 'ctcl-quantity' },el('span',{},__('Qty: ','ctc-lite')), el('span', { onClick: () => setAttributes({ dummyQty: 2 <= parseInt(attributes.dummyQty) ? (parseInt(attributes.dummyQty) - 1) : 1 }), className: 'ctcl-minus-qty' }, '-'), el('input', { onChange: e => setAttributes({ dummyQty: e.target.value }), className: 'ctcl-qty', type: 'number', min: '1', value: attributes.dummyQty }), el('span', { onClick: () => setAttributes({ dummyQty: (parseInt(attributes.dummyQty) + 1) }), className: 'ctcl-plus-qty' }, '+')),
            el(Button, { style: { backgroundColor: attributes.buttonColor },  disabled:attributes.disableAddToCartBtn, className: ' dashicons-before dashicons-cart ctcl-add-cart', 'data-price': attributes.productPrice, 'data-name': attributes.productName, 'data-pic': attributes.profilePic, }, attributes.addToCartMsg),

            el(PluginSidebar, { name: 'ctcl-checkout', icon: 'store', title: __('Product Information', 'ctc-lite') },
            el(PanelBody,null, 
                el(ToggleControl, {

                    label:__("Out of Stock", "ctc-lite"),
			        checked:attributes.outOfStock,
                    onChange: val =>{
                        setAttributes({outOfStock:val})
                        
                        if(val){  
                            setAttributes({addToCartMsg:__('Out of Stock','ctc-lite')}) 
                            setAttributes({disableAddToCartBtn:val})
                            setAttributes({preOrderAvailable:!val})
                        }
                        else{
                            setAttributes({addToCartMsg:__('Add To Cart','ctc-lite')})
                            setAttributes({disableAddToCartBtn:val})
                            setAttributes({preOrderAvailable:val})

                         }
                    }
                },),
                attributes.outOfStock && el(ToggleControl,{
                    label:__( "Pre Order Available",'ctc-lite'),
                    checked:attributes.preOrderAvailable,
                    onChange:val =>{
                        setAttributes({preOrderAvailable:val})
                        if(val){ 
                             setAttributes({addToCartMsg:__('Pre order','ctc-lite')})
                             setAttributes({disableAddToCartBtn:!val})  
                             } else{
                                setAttributes({addToCartMsg:__('Out of Stock','ctc-lite')})
                                setAttributes({disableAddToCartBtn:!val})
                             }
                    }
                })
                ),    
            
            el(PanelBody, null,
                    el(TextControl, { name: 'name', className: 'inspect-product-name', type: "text", value: attributes.productName, label: `${__("Name", 'ctc-lite')} : `, onChange: value => setAttributes({ productName: value }), help: __('Enter product name.', 'ctc-lite') }),
                    el(TextControl, { name: 'price', className: 'inspect-product-price', type: 'number', value: attributes.productPrice, label: `${__("Price", 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}) :`, onChange: value => {
                        
                        if(0 < attributes.variation1.length){
                        let var1 = attributes.variation1.map(x=>{
                            return {value:x.label+'~'+value, label:x.label}
                        })
                       setAttributes({variation1: var1})
                    }
                    setAttributes({ productPrice: parseFloat(value).toFixed(2) }); 
                    
                    }, help: __('Enter product price.', 'ctc-lite') }),
                    el(TextControl, { name: 'shipping', className: 'inspect-shipping-cost', type: 'number', value: attributes.shippingCost, label: `${__("Shipping Cost", 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}) :`, onChange: value => setAttributes({ shippingCost: parseFloat(value).toFixed(2) }), help: __('Enter shipping cost', 'ctc-lite') },),
                    el(PanelBody,{}, 
                    el(TextControl, { name: 'variations1', className: "ctcl-setting-variation1", label: `${__("Variations 1", 'ctc-lite')} : `, value: variationOneItem.join(','), help: __('Comma separated.', 'ctc-lite'), onChange: val => setAttributes({ variation1: val.split(',').map(x => { return { value: x+'~'+attributes.productPrice, label: x } }) }) }),
                    el(TextControl, { name: 'variations1label', className: "ctcl-setting-variation1-label", label: `${__("Variations 1 Label", 'ctc-lite')} : `, value: attributes.variation1Lable, onChange: val => setAttributes({ variation1Lable: val }) }),
                    el(ToggleControl,{ label:__("Different price for variation",'ctc-lite'), help:__("Different price for different variation for Variation 1?","ctc-lite"), checked:attributes.varDiffPrice, onChange:val=>{
                         if(0 < attributes.variation1.length){

                            if( false == attributes.varDiffPrice){

                                let var1 = attributes.variation1.map(x=>{
                                    return {value:x.label+'~'+attributes.productPrice, label:x.label}
                                })
                               setAttributes({variation1: var1})
                        
                    }
                       
                         
                }                   
                     setAttributes({varDiffPrice: val})  
                    }}),
                    
                    attributes.varDiffPrice &&  attributes.variation1.map( (x,i)=>el(TextControl,{className:"ctcl-different-price", label:x.label, className:'ctcl-varition1-input', type: 'number' ,value:parseFloat(x.value.split('~')[1]),  key:i, onChange:val=> {
                       attributes.variation1[i] = {value: x.label+'~'+ parseFloat(val) ,label:x.label }
                       setAttributes({variation1:[...attributes.variation1]})

                    } 
                    } ) )),
                    el(PanelBody,{},
                    el(TextControl, { name: 'variations2', className: "ctcl-setting-variation2", label: `${__("Variations 2", 'ctc-lite')} : `, value: variationTwoItem.join(','), help: __('Comma separated.', 'ctc-lite'), onChange: val => setAttributes({ variation2: val.split(',').map(x => { return { value: x+'~'+attributes.profilePic, label: x } }) }) }),
                    el(TextControl, { name: 'variations2label', className: "ctcl-setting-variation2-label", label: `${__("Variations 2 Label", 'ctc-lite')} : `, value: attributes.variation2Lable, onChange: val => setAttributes({ variation2Lable: val }) }),
                    el(ToggleControl,{ label:__("Different images for variation",'ctc-lite'), help:__("Different images for different variation for Variation 2?","ctc-lite"), checked:attributes. varDiffImage, onChange:val=>setAttributes({ varDiffImage: val})  }),
                    attributes.varDiffImage && attributes.variation2.map((x,i)=>el(MediaUpload, {
                        title: __('Select Product Image for '+x.label, 'ctc-lite'),
                        allowedTypes : 'image',
                        onSelect: media => {
                            attributes.variation2[i] = {value: x.label+'~'+ media.url ,label:x.label }
                            setAttributes({variation2:[...attributes.variation2]}) 
                        },
                        render: ({ open }) => el(Button, {
                            className: "ctcl-media-button dashicons-before dashicons-admin-media", onClick: open
                        }, __("Image for "+x.label, "ctc-lite")),
                    })

                    )

                
                    ),
                    el(PanelBody,{},
                    el(MediaUpload, {
                        title: __('Select Product Image', 'ctc-lite'),
                        onSelect: media => setAttributes({ profilePic: media.url }),
                        render: ({ open }) => el(Button, {
                            className: "ctcl-media-button dashicons-before dashicons-admin-media", onClick: open
                        }, __("Product Cart Image", "ctc-lite")),
                    })),
                  
                    el('h4', { className: "ctcl-colorpicker-label" }, __('Select button color', 'ctc-lite')),
                    el(ColorPicker, { onChangeComplete: colorVal => setAttributes({ buttonColor: colorVal.hex }) },)

                ))
        )
    },
    save: ({ attributes }) => {



        const SelectVariation = ({ selectOptions, variationName, label }) => {

            if (1 < selectOptions.length) {

                return el('div', { className: `ctcl-${variationName.replace(' ', '-')}-cont` },
                    el('label', { htmlFor: `ctcl-${variationName.replace(' ', '-')}`, className: `ctcl-${variationName.replace(' ', '-')}-label` }, `${label} : `),
                    el('select', { id: `ctcl-${variationName.replace(' ', '-')}`, className: `ctcl-${variationName.replace(' ', '-')}`, }, el('option', {selected:true , disabled:true ,value:'N/A'}), selectOptions.map(x => el('option', {  value: `${x.value}` }, x.label),)),
                );

            } else {
                return '';
            }

        };

        return el('div', { className: 'ctcl-product-container' },
        attributes.preOrderAvailable && el('i',{style:{color:'rgb(255, 0, 0)',fontSize:'10px'}}, __('Out of Stock, Pre order available ','ctc-lite')),
            el('div', { className: 'product-price-container' }, el('span', { className: 'price-label' }, `${__('Price', 'ctc-lite')}(${ctcLiteParams.currency.toUpperCase()}): `), el('span', { className: 'product-price' }, attributes.productPrice)),
            el(SelectVariation, { selectOptions: attributes.variation1, variationName: 'Variation 1', label: attributes.variation1Lable }),
            el(SelectVariation, { selectOptions: attributes.variation2, variationName: 'Variation 2', label: attributes.variation2Lable }),
            el('label', { className: 'ctcl-product-qty' }, `${__('Qty ')} : `),
            el('div', { className: 'ctcl-quantity' }, el('span', { className: 'ctcl-minus-qty' }, '-'), el('input', { className: 'ctcl-qty', type: 'number', min: '1', value: attributes.dummyQty }), el('span', { className: 'ctcl-plus-qty' }, '+')),
            el('button', { style: { backgroundColor: attributes.buttonColor },  disabled:attributes.disableAddToCartBtn, className: ' dashicons-before dashicons-cart ctcl-add-cart', 'data-price': attributes.productPrice, 'data-qty': 1, 'data-name': attributes.productName, 'data-shipping-cost': attributes.shippingCost, 'data-pic': attributes.profilePic, 'data-post-id': attributes.postId}, attributes.addToCartMsg),
        )
    }

});





/**
 *  @since 1.0.0
 *
 * checkout page block component
 */

const CheckoutPage = (props) => {

}

registerBlockType('ctc-lite/ctc-lite-checkout-block', {
    title: __("CTC Lite Cart", 'ctc-lite'),
    icon: 'cart',
    description: __("CTC Lite block to create checkout page", "ctc-lite"),
    category: 'ctc-lite-blocks',
    keywords: [__('eCommerce', 'ctc-lite'), __('Checkout', 'ctc-lite')],
    example: {},
    attributes: {
        buttonColor: { type: 'string', default: 'rgba(61,148,218,1)' },
        paymentPage: { type: 'string', default: '' },
        prodListDis :{type:"Boolean",default:true},
        contFormDis:{type:'Boolean',default:false},
        couponAvail:{ type:'Boolean',default:false},
        couponCode:{type:'String',default:'' },
        amount:{type:'Number', default:0}, 
    },

    edit: ({ attributes, setAttributes }) => {
        return el('div', { className: 'ctcl-checkout-container' },
            el('div', { className: 'ctcl-checkout' },
                el('form', { id: 'ctcl-checkout-form', },
                    el('div', { style:{display:attributes.prodListDis? '' :'none'},className: 'ctcl-product-list' },
                        el('h5', { className: 'ctcl-product-list-header' }, __('Product List :', 'ctc-lite')),
                        el('div', { className: 'ctcl-product-list-container' },
                            el('p', { className: 'ctcl-product-list-content' }, __('Contains Product List', 'ctc-lite')),
                        ),

                        el("div",{},

                        el(Button,
                            
                            {   onClick:()=>{
                                setAttributes({prodListDis:false})
                                setAttributes({contFormDis:true})
                             },
                            style: { backgroundColor: attributes.buttonColor,  }, className: 'ctcl-checkout-button' },
                          
                            __("Proceed To Checkout", 'ctc-lite')),
                        )
                        ),
                 el("div", {style:{display:attributes.contFormDis? '' :'none'} ,},
                    el('fieldset', {  className: 'ctcl-contact-form-fieldset' },
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
                        el('div',{},

                        el(Button, { onClick:()=>{
                            setAttributes({prodListDis:true})
                                setAttributes({contFormDis:false})
                        },style: { backgroundColor: attributes.buttonColor, display:"inline-block", float:"left" }, className: 'ctcl-checkout-button' }, __("Back", 'ctc-lite')),
                        el(Button, { style: { backgroundColor: attributes.buttonColor,display:"inline-block",float:"right" }, className: 'ctcl-checkout-button' }, __("Check Out", 'ctc-lite')),
                        ),
                        
                 ),
                    el(PluginSidebar, { name: 'ctcl-checkout', icon: 'store', title: __('Checkout page setting', 'ctc-lite') },
                    el(PanelBody, null,
                            el(TextControl, { value: attributes.paymentPage, onChange: val => setAttributes({ paymentPage: val }), className: 'ctcl-co-payment-page', type: 'text', label: __('URL of page with ctc lite payment processing block :', 'ctc-lite') }),
                            el('i', { className: "ctcl-colorpicker-label" }, __('Select button color', 'ctc-lite')),
                            el(ColorPicker, { onChangeComplete: colorVal => setAttributes({ buttonColor: colorVal.hex }) },))
                    ),
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
                        ),
                        el("div",{},
                        el("button",{
                            style: { backgroundColor: attributes.buttonColor, display:"none" }, className: 'ctcl-checkout-next' },
                          
                            __("Proceed to Checkout", 'ctc-lite')),
                        
                        ),
                        ),
                    el('div',{className:"ctcl-multip-contact", style:{display:"none"}},    
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
                     
                    el('button', {  style: { backgroundColor: attributes.buttonColor, display:"inline-block", float:"left" }, className: 'ctcl-checkout-back', }, __("Back", 'ctc-lite') ),   
                    el('input', { type: 'submit', name: 'ctcl-checkout-button', style: { backgroundColor: attributes.buttonColor, display:"inline-block", float:"right" }, className: 'ctcl-checkout-button', value: __("Check Out", 'ctc-lite') }),
                    
                ),
                el('br',{}),
                ),
            ),
        )

    }
})

/**
 *  @since 1.0.0
 *
 * Register order processing block
 */

registerBlockType('ctc-lite/ctc-lite-order-processing', {

    title: __('CTC Lite Order Processing', 'ctc-lite'),
    icon: 'money',
    description: __("CTC Lite block to create Order processing page", "ctc-lite"),
    category: 'ctc-lite-blocks',
    keywords: [__('eCommerce', 'ctc-lite'), __('Order Processing', 'ctc-lite')],
    example: {},
    attributes: {},

    edit: () => {
        return el('div', { className: 'ctcl-order-processing-block' },
            el('h5', { className: 'ctcl-order-processing-header' }, __('Order Processing', 'ctc-lite')),
            el('p', { className: 'ctc-order-message' }, __('Contains Order outcome message.', 'ctc-lite')),
        );

    },
    



});


/**
 *  @since 2.0.0
 *
 * Register Gallery Block
 */

registerBlockType('ctc-lite/ctcl-image-gallery', {



    title: __('CTC Lite Image Gallery', 'ctc-lite'),
    icon: 'cover-image',
    description: __("CTC Lite block to create image gallery", "ctc-lite"),
    category: 'ctc-lite-blocks',
    keywords: [__('image gallery', 'ctc-lite'), __('product album', 'ctc-lite')],
    example: {},
    attributes: {
        galItems: { type: 'Array', default: [] },
        mainImage: { type: 'String', default: '' },
        clntId: { type: 'String', default: '' },
        mainImgWd: { type: 'Number', default: 340 },
        mainImgHt: { type: 'Number', default: 385 },
        mainImgFinalWd: { type: 'Number', default: 450 },
        mainImgFinalHt: { type: 'Number', default: 700 },
    },

    edit: ({ attributes, setAttributes, clientId }) => {



        setAttributes({ clntId: clientId });
        useEffect(() => {
           
            if(attributes.galItems.length > 1){

                let imgList = document.querySelector('.ctclig-image-list');
                if(null != imgList){
                    imgList.remove();   
                } 
              
            new ctclImgGal('.ctcl-image-gallery',{
                
                    mainImgHt:attributes.mainImgHt, 
                    mainImgWd:attributes.mainImgWd,
                    imageEvent:'mouseover' ,
                    callBack:(el)=>{
                        console.log(el)
                    
                    }
            });
        }

        }, [attributes.mainImgHt, attributes.mainImgWd, attributes.galItems])


        return el('div', { className: 'ctcl-image-gallery-block' },

            el('div',{className:'ctcl-image-gallery'},
            attributes.galItems.map((x, i) =>el('img', { className: 'ctclg-gal-img', id: `ctclif-gal-img-${attributes.clntId}-${i}`, 'data-ts': `${attributes.clntId}`, 'data-image-num': `${i}`, style: { border: '1px solid rgba(0,0,0,1)', width: '70px', height: '70px', margin: '2px' }, key: i, title: x.caption, src: x.url })),
            ),
            el('div', { style: { border: '1px solid rgb(61, 148, 218)', backgroundColor: 'rgba(255,255,255,1)', } },

                el(MediaUpload, {
                    title: __('Select  product images for gallery ', 'ctcl-image-gallery'),
                    multiple: true,
                    value: attributes.galItems.map(x => x.id),
                    gallery: true,
                    onSelect: gal => {
                        setAttributes({ galItems: gal });
                        setAttributes({ mainImage: gal[0].url });
                    },
                    render: ({ open }) => el('div', { style: { width: '100%', backgroundColor: 'rgba(255,255,,255,1)', color: 'rgb(61, 148, 218)', padding: '10px' } },
                       
                        el(Button, { style: { marginLeft: 'auto', marginRight: 'auto', display: 'block', color: 'rgb(61, 148, 218)', border: '1px solid rgb(61, 148, 218)' }, className: "ctclig-media-button dashicons-before dashicons-format-gallery", onClick: open }, __(" Select Gallery Images", "ctcl-image-gallery")),
                    ),
                }),

                el(InspectorControls, null,
                    el(PanelBody, null,
                        el(RangeControl, {
                            label: __('Image width in pixel (px)', 'ctc-gal'),
                            min: 148,
                            max: window.innerWidth,
                            onChange: val => setAttributes({ mainImgWd: val }),
                            value: attributes.mainImageWd,
                        }),

                        el(RangeControl, {
                            label: __('Image height in pixel(px)', 'ctc-gal'),
                            min: 148,
                            max: window.innerHeight,
                            onChange: val => setAttributes({ mainImgHt: val }),
                            value: attributes.mainImageHt,
                        })

                    ))

            ),


        )


    },
    save: ({ attributes }) => el('div', { style:{height:attributes.mainImgHt+'px', width:attributes.mainImgWd+'px',opacity:0}, className: 'ctcl-image-gallery', },

        attributes.galItems.map((x, i) => el('img', { className: 'ctclg-gal-img', id: `ctclif-gal-img-${attributes.clntId}-${i}`, 'data-ts': `${attributes.clntId}`, 'data-image-num': `${i}`,  key: i, title: x.caption, src: x.url }))),
      

});

/**
 *  @since 2.3.0
 *
 * Display Product and gallery in column
 */
registerBlockType('ctc-lite/display-column', {

    title: __('Display in column', 'ctc-lite'),
    icon: 'columns',
    description: __("CTC Lite block columned display", "ctc-lite"),
    category: 'ctc-lite-blocks',
    keywords: [__('Product display', 'ctc-lite'), ],
    example: {},
    edit: () => {


        const ALLOWED_BLOCKS = [
            'core/columns',
			'ctc-lite/ctcl-image-gallery',
			'ctc-lite/ctc-lite-product-block',
            'core/paragraph',
            'core/heading',
		];

        const TEMPLATE = [ [ 'core/columns', {}, [
			[ 'core/column', {
                "width": "61%",
            }, [
				[ 'ctc-lite/ctcl-image-gallery' ],
			] ],
			[ 'core/column', {}, [
                [ 'core/heading',{
					placeholder: __('Name','ctc-lite')}],
                ['core/paragraph',{
					placeholder: __('Short Product Description','ctc-lite')
                }],
				[ 'ctc-lite/ctc-lite-product-block'],
			] ],
		] ] ];

        const blockProps = useBlockProps();

        return (
            el('div', { ...blockProps },
                el(InnerBlocks,{
                    allowedBlocks: ALLOWED_BLOCKS ,
					template:TEMPLATE 
                })
            )
                
            
        );
    },

    save: () => {
        const blockProps = useBlockProps.save();

        return ( 
            el('div', { ...blockProps }, el(InnerBlocks.Content))
             
        );
    },

})