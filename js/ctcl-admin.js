window.addEventListener('load', () => {

    //lay bricks on payment tab 
    if (0 < document.querySelectorAll('.ctcl-payment-tab-fieldset').length) {
        let masonry = new jsMasonry('.ctcl-payment-tab-fieldset', { elSelector: 'fieldset', elWidth: 370, elMargin: 5, callback: (el) => el.style.opacity = 1, });
    }

    //lay bricks on shipping tab
    if (0 < document.querySelectorAll('.ctcl-shipping-tab-fieldset').length) {
        let masonry = new jsMasonry('.ctcl-shipping-tab-fieldset', { elSelector: 'fieldset', elWidth: 550, elMargin: 5, callback: (el) => el.style.opacity = 1, });
    }

    //script for email setting tab
    if (null !== document.querySelector('.ctc-smtp-email-setting')) {

        document.querySelector('#ctcl-send-test-email').addEventListener('click', () => {

            let testEmail = document.querySelector('#ctcl-test-email').value;


            if (0 === testEmail.length) {
                alert(ctclAdminObject.emptyTestEmail);
            } else {
                var xhttp = new XMLHttpRequest();
                xhttp.open('POST', ctclAdminObject.ajaxUrl, true);
                xhttp.responseType = "text";
                xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
                xhttp.addEventListener('load', event => {
                    if (event.target.status >= 200 && event.target.status < 400) {
                        alert(event.target.response);
                    } else {
                        console.log(event.target.statusText);
                    }
                })
                xhttp.send(`action=sendTestEmail&email=${testEmail}`);

            }
        });

    }

    /**
     * get order data with ajax
     */
    let pendingOrderItems = document.querySelectorAll('.ctcl-get-order-data');

    if (0 < pendingOrderItems.length) {


        Array.from(pendingOrderItems).map(x => x.addEventListener('click', e => {
            let orderId = e.target.getAttribute('data-order-id');
            new jsOverlay({ ajaxUrl: ctclAdminObject.ajaxUrl, ajaxMethod: 'POST', ajaxData: `action=pendingOrderDetail&orderId=${orderId}`, containerHt: 800, containerWd: 1500 });
        }));
    }


});
