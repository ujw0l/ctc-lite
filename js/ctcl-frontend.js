
class ctclMain {

    constructor() {

        if (1 <= document.querySelectorAll('.ctcl-add-cart').length) {
            this.reduceItemQyty();
            this.increaseItemQty();
            this.addReduceItemQty();
            this.addItemToCart();
        }

        if (null !== document.querySelector('#ctcl-checkout-from')) {
            this.loadCartItems();
        }


    }

    /**
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
    * on add to cart button click
    */

    addItemToCart() {
        Array.from(document.querySelectorAll('.ctcl-add-cart')).map((x, i, btnArr) => btnArr[i].addEventListener('click', e => {
            let newItem = e.target.getAttribute('data-name');

            if (null === localStorage.getItem('ctclHiddenCart')) {
                localStorage.setItem('ctclHiddenCart', JSON.stringify([{ name: e.target.getAttribute('data-name'), price: e.target.getAttribute('data-price'), qty: e.target.getAttribute('data-qty'), pic: e.target.getAttribute('data-pic'), shippingCost: e.target.getAttribute('data-shipping-cost') }]))
            } else {
                let setCartItems = JSON.parse(localStorage.getItem('ctclHiddenCart'));
                let prodNameCart = setCartItems.map(x => x.name);
                for (let i in setCartItems) {
                    if (setCartItems[i].name === newItem && 0 <= prodNameCart.indexOf(newItem)) {
                        setCartItems[i] = { name: e.target.getAttribute('data-name'), price: e.target.getAttribute('data-price'), qty: e.target.getAttribute('data-qty'), pic: e.target.getAttribute('data-pic'), shippingCost: e.target.getAttribute('data-shipping-cost') };
                    } else if (setCartItems[i].name != newItem && -1 === prodNameCart.indexOf(newItem)) {
                        setCartItems.push({ name: e.target.getAttribute('data-name'), price: e.target.getAttribute('data-price'), qty: e.target.getAttribute('data-qty'), pic: e.target.getAttribute('data-pic'), shippingCost: e.target.getAttribute('data-shipping-cost') })
                        prodNameCart.push(newItem);
                    }
                }
                localStorage.setItem('ctclHiddenCart', JSON.stringify(setCartItems));
                ctclCartFunc.map(x => x(setCartItems));
            }

        }))

    }

    /**
     * Load cart item to main checkout cart
     * 
     */

    loadCartItems() {
        let prodListCont = document.querySelector('#ctcl-checkout-product-list');

        if (null !== localStorage.getItem('ctclHiddenCart')) {
            prodListCont.querySelector('p').style.dislay = 'none';


            let cartItems = JSON.parse(localStorage.getItem('ctclHiddenCart'));
            let taxSum = 0;
            let shippingCost = 0;
            let totalTax = 0;
            let subTotal = 0;


            let listContainer = document.createElement('div');
            listContainer.id = "ctcl-checkout-list-container";
            listContainer.classList.add('ctcl-checkout-list-container');
            prodListCont.appendChild(listContainer);

            for (let i in cartItems) {

                let itemTotal = parseInt(cartItems[i].qty) * parseFloat(cartItems[i].price);
                shippingCost += (parseInt(cartItems[i].qty) * parseFloat(cartItems[i].shippingCost));
                totalTax += (parseFloat(ctclParams.taxRate) * itemTotal);
                subTotal += itemTotal;

                let itemInput = document.createElement('input');
                itemInput.type = 'hidden';
                itemInput.name = `item-${i}`;
                itemInput.value = JSON.stringify({ itemName: cartItems[i].name, qunatity: cartItems[i].qty, itemTotal: itemTotal });
                prodListCont.appendChild(itemInput);

                let itemDisplay = document.createElement('div');
                itemDisplay.id = `ctcl-checkout-item-${i}`;
                itemDisplay.classList.add('ctcl-checkput-item');

                let itemName = document.createElement('span');
                itemName.classList.add('ctcl-checkout-item-name');
                itemName.appendChild(document.createTextNode(cartItems[i].name));
                itemDisplay.append(itemName);

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
                itemTotalSpan.appendChild(document.createTextNode(itemTotal));
                itemDisplay.append(itemTotalSpan);

                let itemRemove = document.createElement('span');
                itemRemove.className = 'dashicons-before dashicons-trash ctcl-checkout-item-remove ';
                itemRemove.addEventListener('click', () => this.removeItem(i, listContainer, 'ctclHiddenCart'));
                itemDisplay.append(itemRemove);

                listContainer.appendChild(itemDisplay);
            }
        } else {
            prodListCont.querySelector('p').style.display = '';
        }



    }

    /**
     * Remove item from cart
     * 
     * @param i item number in cart 
     * @param cont item list container 
     * @param hiddenCart localstorage hidden cart identifier
     */

    removeItem(i, cont, hiddenCart) {

        let setItems = JSON.parse(localStorage.getItem(hiddenCart));
        setItems.splice(i, 1);

        localStorage.removeItem(hiddenCart);
        localStorage.setItem(hiddenCart, JSON.stringify(setItems));

        cont.parentElement.removeChild(cont);
        this.loadCartItems();
    }

}




window.addEventListener('load', () => {
    new ctclMain();
});