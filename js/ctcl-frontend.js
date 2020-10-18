window.addEventListener('load', () => {


    /**
     * when user clicks minus quantity
     * 
     */
    Array.from(document.querySelectorAll('.ctcl-minus-qty')).map(x => x.addEventListener('click', e => {

        let superParent = e.target.parentElement.parentElement;
        let qtyInput = superParent.querySelector('.ctcl-quantity').querySelector('.ctcl-qty');
        let addCartBtn = superParent.querySelector('.ctcl-add-cart');
        let prodQty = parseInt(addCartBtn.getAttribute('data-qty'));
        let updateQty = 2 <= prodQty ? prodQty - 1 : 1;
        qtyInput.value = updateQty;
        addCartBtn.setAttribute('data-qty', updateQty);
    }))

    /**
     * when user clicks plus quantity
     * 
     */
    Array.from(document.querySelectorAll('.ctcl-plus-qty')).map(x => x.addEventListener('click', e => {
        let superParent = e.target.parentElement.parentElement;
        let qtyInput = superParent.querySelector('.ctcl-quantity').querySelector('.ctcl-qty');
        let addCartBtn = superParent.querySelector('.ctcl-add-cart');
        let prodQty = parseInt(addCartBtn.getAttribute('data-qty'));
        let updateQty = prodQty + 1;
        qtyInput.value = updateQty;
        addCartBtn.setAttribute('data-qty', updateQty);
    }))

    /**
    * when changes quantity
    * 
    */
    Array.from(document.querySelectorAll('.ctcl-qty')).map(x => x.addEventListener('change', e => {

        let superParent = e.target.parentElement.parentElement;
        let addCartBtn = superParent.querySelector('.ctcl-add-cart');
        addCartBtn.setAttribute('data-qty', e.target.value);
    }))

    /**
     * on add to cart button click
     */


    Array.from(document.querySelectorAll('.ctcl-add-cart')).map((x, i, btnArr) => btnArr[i].addEventListener('click', e => {
        let filteredCartItems = Array();
        let newItem = e.target.getAttribute('data-name');

        if (null === localStorage.getItem('ctclHiddenCart')) {
            localStorage.setItem('ctclHiddenCart', JSON.stringify([{ name: e.target.getAttribute('data-name'), price: e.target.getAttribute('data-price'), qty: e.target.getAttribute('data-qty'), pic: e.target.getAttribute('data-pic'), shippingCost: e.target('data-shipping-cost') }]))
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
            localStorage.setItem('ctclHiddenCart', JSON.stringify(setCartItems))
        }

    }))

});