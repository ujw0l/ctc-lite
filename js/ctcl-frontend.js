
class ctclMain {

    constructor() {
        if (undefined != document.querySelector('#ctcl-order-sucesfully-placed')) {
            this.removeLocalstorageItem();
        }

        if (1 <= document.querySelectorAll('.ctcl-add-cart').length) {

            this.onVariationOneSelect();
            this.onVariationTwoSelect();
            this.reduceItemQyty();
            this.increaseItemQty();
            this.addReduceItemQty();
            this.addItemToCart();
        }

        if (null !== document.querySelector('#ctcl-checkout-from')) {
            this.loadCartItems();
            this.hideShowPaymentContainer();
            this.onShippingRadioButtonCheck();
            this.showHideMultipartForm();

        }

        if(null !== document.querySelector('.ctcl-image-gallery')){

            this.applyGallery();

        }

    }

    /**
     *  @since 1.0.0
     *
     * user clicks minus quantity
     * 
     */
    reduceItemQyty() {
        Array.from(document.querySelectorAll('.ctcl-minus-qty')).map(x => x.addEventListener('click', e => {

            let superParent = e.target.parentElement.parentElement;
            let qtyInput = superParent.querySelector('.ctcl-quantity').querySelector('.ctcl-qty');
            let addCartBtn = superParent.querySelector('.ctcl-add-cart');
            let prodQty = parseInt(addCartBtn.getAttribute('data-qty'));
            let updateQty = 2 <= prodQty ? prodQty - 1 : 1;
            qtyInput.value = updateQty;
            addCartBtn.setAttribute('data-qty', updateQty);
        }))
    }

    /**
     * @since 1.0.0
     *
     * user clicks plus quantity
     * 
     */
    increaseItemQty() {
        Array.from(document.querySelectorAll('.ctcl-plus-qty')).map(x => x.addEventListener('click', e => {
            let superParent = e.target.parentElement.parentElement;
            let qtyInput = superParent.querySelector('.ctcl-quantity').querySelector('.ctcl-qty');
            let addCartBtn = superParent.querySelector('.ctcl-add-cart');
            let prodQty = parseInt(addCartBtn.getAttribute('data-qty'));
            let updateQty = prodQty + 1;
            qtyInput.value = updateQty;
            addCartBtn.setAttribute('data-qty', updateQty);
        }))
    }

    /**
     * @since 1.0.0
     *
     * changes quantity with html5 number
     * 
     */
    addReduceItemQty() {
        Array.from(document.querySelectorAll('.ctcl-qty')).map(x => x.addEventListener('change', e => {

            let superParent = e.target.parentElement.parentElement;
            let addCartBtn = superParent.querySelector('.ctcl-add-cart');
            addCartBtn.setAttribute('data-qty', e.target.value);
        }))
    }

    /**
     * @since 2.0.0
     * 
     * On Variation one select
     */
    onVariationOneSelect(){
      if(undefined != document.querySelector('#ctcl-Variation-1')) { 
        document.querySelector('#ctcl-Variation-1').addEventListener('change',e=>{
            let val =  e.target.value ;
            if(val.includes('~')){
                let parentContainer = e.target.parentElement.parentElement;
                let vals = val.split('~');
                parentContainer.querySelector('.product-price').innerHTML =  parseFloat(vals[1]).toFixed(2);
                parentContainer.querySelector('.ctcl-add-cart').setAttribute('data-price',parseFloat(vals[1]).toFixed(2));  
            }
        })
    }
    }

/**
     * @since 2.0.0
     * 
     * On Variation two select
     */
    onVariationTwoSelect(){

        if(null != document.querySelector('#ctcl-Variation-2')) { 
            document.querySelector('#ctcl-Variation-2').addEventListener('change',e=>{
                let val =  e.target.value ;
                if(val.includes('~')){
                    let parentContainer = e.target.parentElement.parentElement;
                    let vals = val.split('~');
                    if(null !=  document.querySelector('.ctcl-image-gallery')){
                        document.querySelector('.ctcl-image-gallery').querySelector('.ctclig-main-image').style.backgroundImage = `url("${vals[1]}")`;
                    }
                    parentContainer.querySelector('.ctcl-add-cart').setAttribute('data-pic',vals[1]);  
                }
            })
        }

    }

    /**
     * @since 1.0.0
     *
     * on add to cart button click
     */

    addItemToCart() {
        Array.from(document.querySelectorAll('.ctcl-add-cart')).map((x, i, btnArr) => btnArr[i].addEventListener('click', e => {
            let newItem = e.target.getAttribute('data-name');

            let parentContainer = e.target.parentElement;
            let variation1 = undefined != parentContainer.querySelector('#ctcl-Variation-1') ? parentContainer.querySelector('#ctcl-Variation-1').value.split('~')[0] : 'N/A';
            let variation2 = undefined != parentContainer.querySelector('#ctcl-Variation-2') ? parentContainer.querySelector('#ctcl-Variation-2').value.split('~')[0] : 'N/A';

            if (null === localStorage.getItem('ctclHiddenCart')) {
                localStorage.setItem('ctclHiddenCart', JSON.stringify([{
                    name: e.target.getAttribute('data-name'),
                    price: e.target.getAttribute('data-price'),
                    qty: e.target.getAttribute('data-qty'),
                    pic: e.target.getAttribute('data-pic'),
                    postId: e.target.getAttribute('data-post-id'),
                    shippingCost: e.target.getAttribute('data-shipping-cost'),
                    varOne: variation1,
                    varTwo: variation2,


                }]))
            } else {
                let setCartItems = JSON.parse(localStorage.getItem('ctclHiddenCart'));
                let prodNameCart = setCartItems.map(x => x.name);
                for (let i in setCartItems) {
                    if (setCartItems[i].name === newItem && 0 <= prodNameCart.indexOf(newItem)) {
                        setCartItems[i] = {
                            name: e.target.getAttribute('data-name'),
                            price: e.target.getAttribute('data-price'),
                            qty: e.target.getAttribute('data-qty'),
                            pic: e.target.getAttribute('data-pic'),
                            postId: e.target.getAttribute('data-post-id'),
                            shippingCost: e.target.getAttribute('data-shipping-cost'),
                            varOne: variation1,
                            varTwo: variation2,
                        };
                    } else if (setCartItems[i].name != newItem && -1 === prodNameCart.indexOf(newItem)) {
                        setCartItems.push({
                            name: e.target.getAttribute('data-name'),
                            price: e.target.getAttribute('data-price'),
                            qty: e.target.getAttribute('data-qty'),
                            pic: e.target.getAttribute('data-pic'),
                            postId: e.target.getAttribute('data-post-id'),
                            shippingCost: e.target.getAttribute('data-shipping-cost'),
                            varOne: variation1,
                            varTwo: variation2,
                        })
                        prodNameCart.push(newItem);
                    }
                }
                localStorage.setItem('ctclHiddenCart', JSON.stringify(setCartItems));

                
            }

            /**
                 * Set custom event on add remove product
                 */
            document.dispatchEvent(new CustomEvent("addRemoveProduct", { detail:JSON.parse(localStorage.getItem('ctclHiddenCart')).length }));

        }))

    }

    /**
     * @since 1.0.0
     *
     * Load cart item to main checkout cart
     * 
     */

    loadCartItems(storePickUp) {
        let prodListCont = document.querySelector('#ctcl-checkout-product-list');
        let loadingP = document.querySelector('.ctcl-product-loading');
        Array.from(prodListCont.querySelectorAll('.ctcl-checkout-item,input,#ctcl-totalshipping-cost,#ctcl-subtotal-container,.ctcl-checkout-item-header,#ctcl-discount-cont,#ctcl-items-total-cont,#ctcl-tax-total-cont')).map(x => {
            x.parentElement.removeChild(x)
        })

        if (null !== localStorage.getItem('ctclHiddenCart') && 0 !== localStorage.getItem('ctclHiddenCart').length) {
            prodListCont.parentElement.querySelector('.ctcl-checkout-next').style.display =''; 
            prodListCont.querySelector('p').style.display = 'none';


            let cartItems = JSON.parse(localStorage.getItem('ctclHiddenCart'));
            let shippingCost = 0;
            let totalTax = 0;
            let subTotal = 0;


            let totalCont = document.createElement('div');
                    totalCont.id = 'ctcl-items-total-cont';

            let taxCont = document.createElement('div');
                taxCont.id = 'ctcl-tax-total-cont'; 

            let totalShippingCont = document.createElement('div');
            totalShippingCont.id = "ctcl-totalshipping-cost";



            let subTotalCont = document.createElement('div');
            subTotalCont.id = "ctcl-subtotal-container";
            subTotalCont.classList.add('ctcl-subtotal-container');

            let listContainer = document.createElement('div');
            listContainer.id = "ctcl-checkout-list-container";
            listContainer.classList.add('ctcl-checkout-list-container');
            prodListCont.appendChild(listContainer);

            let headerDisplay = document.createElement('div');
            headerDisplay.classList.add('ctcl-checkout-item-header');

            let imageHead = document.createElement('span');
            imageHead.className = 'ctcl-co-image-head';
            headerDisplay.appendChild(imageHead);

            let nameHead = document.createElement('span');
            nameHead.className = 'ctcl-co-name-head';
            nameHead.appendChild(document.createTextNode(ctclParams.itemHead));
            headerDisplay.appendChild(nameHead)

            let varHead = document.createElement('span');
            varHead.className = 'ctcl-co-var-head';
            varHead.appendChild(document.createTextNode(ctclParams.varHead));
            headerDisplay.appendChild(varHead)

            let priceHead = document.createElement('span');
            priceHead.className = 'ctcl-co-price-head';
            priceHead.appendChild(document.createTextNode(ctclParams.priceHead));
            headerDisplay.appendChild(priceHead)

            let qunHead = document.createElement('span');
            qunHead.className = 'ctcl-co-qty-head';
            qunHead.appendChild(document.createTextNode(ctclParams.qtyHead));
            headerDisplay.appendChild(qunHead)

            let itemTotHead = document.createElement('span');
            itemTotHead.className = 'ctcl-co-item-total-head';
            itemTotHead.appendChild(document.createTextNode(ctclParams.itemTotalHead));
            headerDisplay.appendChild(itemTotHead)

            let removeHead = document.createElement('span');
            removeHead.className = 'ctcl-co-item-remove-head';
            headerDisplay.appendChild(removeHead);

            listContainer.appendChild(headerDisplay);
            for (let i in cartItems) {

                let itemTotal = parseInt(cartItems[i].qty) * parseFloat(cartItems[i].price);
                shippingCost += (parseInt(cartItems[i].qty) * parseFloat(cartItems[i].shippingCost));
                totalTax += (parseFloat(ctclParams.taxRate) * itemTotal);
                subTotal += itemTotal;

                let itemInput = document.createElement('input');
                itemInput.type = 'hidden';
                itemInput.name = `products[]`;
                itemInput.value = JSON.stringify({ itemName: cartItems[i].name, quantity: cartItems[i].qty, itemTotal: itemTotal.toFixed(2), vari: `${cartItems[i].varOne},${cartItems[i].varTwo}`, postId: cartItems[i].postId });
                prodListCont.appendChild(itemInput);

                let itemDisplay = document.createElement('div');
                itemDisplay.id = `ctcl-checkout-item-${i}`;
                itemDisplay.classList.add('ctcl-checkout-item');

                let imgSpan = document.createElement('span');
                imgSpan.classList.add('ctcl-checkout-item-img-span');
                let itemImg = new Image();
                itemImg.src = cartItems[i].pic;
                imgSpan.appendChild(itemImg);
                itemDisplay.appendChild(imgSpan);

                let itemName = document.createElement('span');
                itemName.classList.add('ctcl-checkout-item-name');
                itemName.appendChild(document.createTextNode(cartItems[i].name));
                itemDisplay.append(itemName);

                let itemVar = document.createElement('span');
                itemVar.classList.add('ctcl-checkout-item-var');
                itemVar.appendChild(document.createTextNode(cartItems[i].varOne + ',' + cartItems[i].varTwo));
                itemDisplay.append(itemVar);

                let itemPrice = document.createElement('span');
                itemPrice.classList.add('ctcl-checkout-item-price');
                itemPrice.appendChild(document.createTextNode(cartItems[i].price));
                itemDisplay.append(itemPrice);

                let itemQty = document.createElement('span');
                itemQty.classList.add('ctcl-checkout-item-qty');
                itemQty.appendChild(document.createTextNode(cartItems[i].qty));
                itemDisplay.append(itemQty);

                let itemTotalSpan = document.createElement('span');
                itemTotalSpan.classList.add('ctcl-checkout-item-total');
                itemTotalSpan.appendChild(document.createTextNode(itemTotal.toFixed(2)));
                itemDisplay.append(itemTotalSpan);

                let itemRemove = document.createElement('span');
                itemRemove.className = 'dashicons-before dashicons-trash ctcl-checkout-item-remove ';
                itemRemove.title = ctclParams.removeItem
                itemRemove.addEventListener('click', () => this.removeItem(i, [listContainer, totalShippingCont, subTotalCont], 'ctclHiddenCart'));
                itemDisplay.append(itemRemove);

                listContainer.appendChild(itemDisplay);
            }


            let itemTotalSpan = document.createElement('span');
            itemTotalSpan.classList.add('ctcl-total-label');
            itemTotalSpan.appendChild(document.createTextNode(ctclParams.itemsTotal + ' (' + ctclParams.currency + ') : '));
            totalCont.appendChild(itemTotalSpan);

            let itemTotal = document.createElement('span');
            itemTotal .classList.add('ctcl-items-total-cost');
            itemTotal .appendChild(document.createTextNode(subTotal.toFixed(2)))
            totalCont .appendChild(itemTotal);
            prodListCont.appendChild(totalCont);

            let itemTotalInput = document.createElement('input');
            itemTotalInput.type = 'hidden';
            itemTotalInput.name = 'items-total';
            itemTotalInput.id   = 'ctcl-items-total';
            itemTotalInput.value = subTotal.toFixed(2);
            prodListCont.appendChild(itemTotalInput);


            let taxTotalVal = (ctclParams.taxRate / 100) * subTotal;
            let taxTotalSpan = document.createElement('span');
            taxTotalSpan.classList.add('ctcl-tax-total-label');
            taxTotalSpan.appendChild(document.createTextNode(ctclParams.taxTotal+ ' (' + ctclParams.taxRate + '%) : '));
            taxCont.appendChild(taxTotalSpan);

            let taxTotal = document.createElement('span');
            taxTotal .classList.add('ctcl-tax-total');
            taxTotal .appendChild(document.createTextNode((taxTotalVal.toFixed(2))))
            taxCont .appendChild( taxTotal);
            prodListCont.appendChild(taxCont);

            let taxTotalInput = document.createElement('input');
            taxTotalInput.type = 'hidden';
            taxTotalInput.name = 'tax-total';
            taxTotalInput.id   = 'ctcl-tax-total';
            taxTotalInput.value = taxTotalVal.toFixed(2);
            prodListCont.appendChild(taxTotalInput);


            let finalShippingCost = undefined != storePickUp ? storePickUp : shippingCost;

            let totalShippingCostLabel = document.createElement('span');
            totalShippingCostLabel.classList.add('ctcl-total-shipping-label');
            totalShippingCostLabel.appendChild(document.createTextNode(ctclParams.totalShipping + ' (' + ctclParams.currency + ') : '));
            totalShippingCont.appendChild(totalShippingCostLabel);

            let totalShippingCost = document.createElement('span');
            totalShippingCost.classList.add('ctcl-total-shipping-cost');
            totalShippingCost.appendChild(document.createTextNode(finalShippingCost.toFixed(2)))
            totalShippingCont.appendChild(totalShippingCost);
            prodListCont.appendChild(totalShippingCont);

            let shippingTotalInput = document.createElement('input');
            shippingTotalInput.type = 'hidden';
            shippingTotalInput.name = 'shipping-total';
            shippingTotalInput.id = "ctcl-shipping-total"
            shippingTotalInput.value = (finalShippingCost).toFixed(2);
            prodListCont.appendChild(shippingTotalInput);


            let subTotalLabel = document.createElement('span');
            subTotalLabel.classList.add('ctcl-sub-total-label');
            subTotalLabel.appendChild(document.createTextNode(ctclParams.subTotal + ' (' + ctclParams.currency + ') : '));
            subTotalCont.appendChild(subTotalLabel);

            let subTotalVal = document.createElement('span');
            subTotalVal.classList.add('ctcl-subtotal-cost');
            subTotalVal.appendChild(document.createTextNode(parseFloat(subTotal + finalShippingCost + ((ctclParams.taxRate / 100) * subTotal)).toFixed(2)));
            subTotalCont.appendChild(subTotalVal);
            prodListCont.appendChild(subTotalCont);

            let subTotalInput = document.createElement('input');
            subTotalInput.type = 'hidden';
            subTotalInput.id = 'ctcl-subtotal-hidden-input'
            subTotalInput.name = 'sub-total';
            subTotalInput.value = (subTotal + finalShippingCost + ((ctclParams.taxRate / 100) * subTotal)).toFixed(2);
            prodListCont.appendChild(subTotalInput);

        } else {

            prodListCont.parentElement.querySelector('.ctcl-checkout-next').style.display ='none'; 
            prodListCont.querySelector('.ctcl-product-list-content').style.display = '';
        }

        if (null != loadingP) {
            loadingP.parentElement.removeChild(loadingP);
        }

    }

    /**
     * Since 2.0.0
     * 
     * Showhide multipart checkout form
     * 
     */

    showHideMultipartForm(){

        document.querySelector('.ctcl-checkout-next').addEventListener('click',e=>{

            e.preventDefault();
            document.querySelector('.ctcl-product-list').style.display = 'none';
            document.querySelector('.ctcl-multip-contact').style.display = '';
        })

        document.querySelector('.ctcl-checkout-back').addEventListener('click',e=>{
            e.preventDefault();
            document.querySelector('.ctcl-product-list').style.display = '';
            document.querySelector('.ctcl-multip-contact').style.display = 'none';
        })

    }

    /**
     *  @since 1.0.0
     *
     * Remove item from cart
     * 
     * @param i item number in cart 
     * @param conts array of container to be removed
     * @param hiddenCart localstorage hidden cart identifier
     */

    removeItem(i, conts, hiddenCart) {

        let setItems = JSON.parse(localStorage.getItem(hiddenCart));
        setItems.splice(i, 1);
        if (1 <= setItems.length) {
            localStorage.setItem(hiddenCart, JSON.stringify(setItems));
                /**
         * Set custom event on add remove product
        */
        document.dispatchEvent(new CustomEvent("addRemoveProduct", { detail: setItems.length}));
        } else {

        /**
        * Set custom event on add remove product
        */
        document.dispatchEvent(new CustomEvent("addRemoveProduct", { detail: 0}));
            localStorage.removeItem('ctclHiddenCart');

        }
        conts.map(x => x.parentElement.removeChild(x));

    
        this.loadCartItems();
    }

    /**
     *  @since 1.0.0
     *
     * Hide display payment container 
     */
    hideShowPaymentContainer() {

        let paymentOption = Array.from(document.querySelectorAll('.ctcl-payment-option'));
        if (1 == paymentOption.length) {

            document.querySelector('#ctcl-payment-type').value = document.querySelector('.ctcl-payment-option').getAttribute('data-name');
          
        } else {

            paymentOption.map(x => {

                x.addEventListener('change', e => {

                    let showContainer = `${e.target.value}_container`;
                    document.querySelector('#ctcl-payment-type').value = e.target.getAttribute('data-name');
                    Array.from(document.querySelectorAll('.ctcl_payment_container')).map(x => {
                        if (x.id == showContainer) {
                            x.style.display = 'block';
                        } else {
                            x.style.display = '';
                        }
                    });
                });

            });
        }
    }

    /**
     * @since 1.0.0
     *
     * Add ahipping option to hidden input field
     */
    onShippingRadioButtonCheck() {
        Array.from(document.querySelectorAll('.ctcl-shipping-option')).map(x => {
            x.addEventListener('change', e => {

                document.querySelector('#ctcl-shipping-type').value = e.target.getAttribute('data-name');

                if (e.target.id == 'store_pickup') {
                    this.loadCartItems(0);
                } else {
                    this.loadCartItems();
                }
            })
        });
    }

   

    /**
     * since 2.0.0
     * 
     * Apply Gallery
     */
  
applyGallery(){

new ctclImgGal('.ctcl-image-gallery',{
    imageEvent:'mouseover' ,
    callBack :(el)=>{
       document.querySelector('.ctclig-image-cont div').classList.add('ctcl-gal-cont')
       setTimeout(()=>{new ctcOverlayViewer('.ctcl-gal-cont');},1000)
        
    },  
});


}


    /**
    * @since 1.0.0
    *
    * Rmove local storage item
    */
    removeLocalstorageItem() {
        localStorage.removeItem('ctclHiddenCart');
    }

}




window.addEventListener('DOMContentLoaded', () => {
    new ctclMain();
});