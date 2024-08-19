class ctclAdminJs {

    constructor() {

        this.loadMasonry();
        this.sendSmtpTestEmail();
        this.getPendingOrderDetail();
        this.getCompleteOrderDetail();
        this.infoTabApplyMasonry();
        this.onDismissNoticeClick();
    }

    /**
     * @since 1.0.0
     *
     * Apply masonry to info tab
     */
    infoTabApplyMasonry() {

        if (undefined != document.querySelector('.ctcl-info-tab')) {

            new jsMasonry('.ctcl-info-tab', { elSelector: 'fieldset', elWidth: 450, elMargin: 5, callback: el => el.style.opacity = '1' });
        }
    }
    /**
     *  @since 1.0.0
     *
     * Load masonry 
     */

    loadMasonry() {

        //lay bricks on payment tab 
        if (0 < document.querySelectorAll('.ctcl-payment-tab-fieldset').length) {
            let masonry = new jsMasonry('.ctcl-payment-tab-fieldset', { elSelector: 'fieldset', elWidth: 370, elMargin: 5, callback: (el) => el.style.opacity = 1, });
        }

        //lay bricks on shipping tab
        if (0 < document.querySelectorAll('.ctcl-shipping-tab-fieldset').length) {
            let masonry = new jsMasonry('.ctcl-shipping-tab-fieldset', { elSelector: 'fieldset', elWidth: 525, elMargin: 5, callback: (el) => el.style.opacity = 1, });
        }
    }

    /**
     *  @since 1.0.0
     *
     * Send SMTP email with ajax
     */
    sendSmtpTestEmail() {

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

    }

    /**
     * @since 1.0.0
     *
     * Get pending order detail in modal with ajax
     */
    getPendingOrderDetail() {

        let pendingOrderItems = document.querySelectorAll('.ctcl-get-pending-order-data');


        if (0 < pendingOrderItems.length) {



            Array.from(pendingOrderItems).map(x => x.addEventListener('click', e => {
                let orderId = e.target.getAttribute('data-order-id');

                var xhttp = new XMLHttpRequest();
                xhttp.open('POST', ctclAdminObject.ajaxUrl, true);
                xhttp.responseType = "text";
                xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
                xhttp.addEventListener('load', event => {

                    if (event.target.status >= 200 && event.target.status < 400) {



                        new jsOverlay({ elContent: event.target.response, containerHt: 600, containerWd: 1200, overlayNum: 1 });
                        new jsMasonry('.ctcl-pending-order-detail', { elWidth: 500, heightSort: 'desc', elMargin: 10 });
                        this.addPendingOrderModalEvent();
                    } else {
                        console.log(event.target.statusText);
                    }
                });
                xhttp.send(`action=pendingOrderDetail&orderId=${orderId}`);

            }));
        }
    }

    /**
     *@since 1.0.0
     *
     * All of the required event listener to be loaded after modal id loaded
     */
    addPendingOrderModalEvent() {
        this.printOrderList();
        this.printCustInfo();
        this.vendorNoteSubmit();
        this.cancelPendingOrder();
        this.pendingOrderMarkComplete();
    }

    /**
     *  @since 1.0.0
     *
     * print ordered item list in pending order modal
     */
    printOrderList() {
        document.querySelector('#ctcl-print-order-list').addEventListener('click', () => {
            let content = document.querySelector('#ctcl-orderlist').innerHTML;
            let css = document.querySelector("#ctclAdminCss-css").href;
            let a = window.open('', '', 'height=500, width=550');
            a.document.write('<html>');
            a.document.write('<body >');
            a.document.write(`<link rel="stylesheet" href='${css}'/>`)
            a.document.write(content);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
        });

    }

    /**
     * @since 1.0.0
     *
     * Print pending order customer info
     */
    printCustInfo() {

        document.querySelector('#ctcl-print-cust-info').addEventListener('click', () => {
            let content = document.querySelector('#ctc-pending-customer-info').innerHTML;
            let css = document.querySelector("#ctclAdminCss-css").href;
            let a = window.open('', '', 'height=400, width=400');
            a.document.write('<html>');
            a.document.write('<body >');
            a.document.write(`<link rel="stylesheet" href='${css}'/>`)
            a.document.write(content);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
        });
    }

    /**
     * @since 1.0.0
     *
     * Handle vendor note submit
     */
    vendorNoteSubmit() {
        document.querySelector('.ctcl-vendor-note-submit').addEventListener('click', e => {
            let vendorNote = document.querySelector('#ctcl-order-status-note').value;
            let orderId = document.querySelector('#ctcl-order-id').value;

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
            xhttp.send(`action=updateVendorNote&orderId=${orderId}&vendorNote=${vendorNote}`);

        });

    }

    /**
     * @since 1.0.0
     *
     * Mark  order complete
     */

    pendingOrderMarkComplete() {

        document.querySelector('.ctcl-detail-mark-complete').addEventListener('click', e => {


            let orderId = document.querySelector('#ctcl-order-id').value;

            var xhttp = new XMLHttpRequest();
            xhttp.open('POST', ctclAdminObject.ajaxUrl, true);
            xhttp.responseType = "text";
            xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
            xhttp.addEventListener('load', event => {
                if (event.target.status >= 200 && event.target.status < 400) {
                    let trToRemove = document.querySelector(`#ctcl-pending-order-${orderId}`);
                    trToRemove.parentElement.removeChild(trToRemove);
                    document.querySelector('#overlay-close-btn').click();
                    alert(event.target.response);
                } else {
                    console.log(event.target.statusText);
                }
            })
            xhttp.send(`action=orderMarkComplete&orderId=${orderId}`);

        });


    }


    /**
     * @since 1.0.0
     *
     * Cancel order
     */
    cancelPendingOrder() {
        document.querySelector('.ctcl-detail-cancel-order').addEventListener('click', e => {
            let orderId = document.querySelector('#ctcl-order-id').value;
            if (confirm(ctclAdminObject.confirmCancelOrder)) {
                var xhttp = new XMLHttpRequest();
                xhttp.open('POST', ctclAdminObject.ajaxUrl, true);
                xhttp.responseType = "text";
                xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
                xhttp.addEventListener('load', event => {
                    if (event.target.status >= 200 && event.target.status < 400) {
                        let trToRemove = document.querySelector(`#ctcl-pending-order-${orderId}`);
                        trToRemove.parentElement.removeChild(trToRemove);
                        document.querySelector('#overlay-close-btn').click();
                        alert(event.target.response);
                    } else {
                        console.log(event.target.statusText);
                    }
                })
                xhttp.send(`action=cancelOrder&orderId=${orderId}`);
            }
        });
    }






    /**
     * @since 1.0.0
     *
     * Get complete order detail in modal with ajax
     */
    getCompleteOrderDetail() {

        let pendingOrderItems = document.querySelectorAll('.ctcl-get-complete-order-data');

        if (0 < pendingOrderItems.length) {


            Array.from(pendingOrderItems).map(x => x.addEventListener('click', e => {
                let orderId = e.target.getAttribute('data-order-id');

                var xhttp = new XMLHttpRequest();
                xhttp.open('POST', ctclAdminObject.ajaxUrl, true);
                xhttp.responseType = "text";
                xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
                xhttp.addEventListener('load', event => {
                    if (event.target.status >= 200 && event.target.status < 400) {
                        new jsOverlay({ elContent: event.target.response, containerHt: 570, containerWd: 1080, overlayNum: 1 });
                        new jsMasonry('.ctcl-complete-order-detail', { elWidth: 500, heightSort: 'desc', elMargin: 10 });

                        this.addCompleteOrderModalEvent();
                        this.refundOrder(orderId);
                    } else {
                        console.log(event.target.statusText);
                    }
                });
                xhttp.send(`action=completeOrderDetail&orderId=${orderId}`);

            }));
        }
    }

    /**
     * @since 2.5.0
     * 
     * Refund order on click
     */
    refundOrder(orderId){

        document.querySelector('.ctcl-detail-refund-order').addEventListener('click',e=>{

            if(confirm(ctclAdminObject.confirmRefund)){


                var xhttp = new XMLHttpRequest();
                xhttp.open('POST', ctclAdminObject.ajaxUrl, true);
                xhttp.responseType = "text";
                xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
                xhttp.addEventListener('load', event => {
                    if (event.target.status >= 200 && event.target.status < 400) {
                       document.querySelector(`#ctcl-complete-order-${orderId}`).remove();
                        alert(event.target.response);
                        document.querySelector("#overlay-close-btn").click();

                    } else {
                        console.log(event.target.statusText);
                    }
                });
                xhttp.send(`action=refundOrder&orderId=${orderId}`);
            }


        })


    }

    /**
     * @since 1.0.0
     *
     * All of the required event listener to be loaded after modal id loaded
     */
    addCompleteOrderModalEvent() {
        this.printOrderList();
        this.printCustInfo();
        this.vendorNoteSubmit();

    }

    /**
     * @since 1.0.0
     *
     * Dispacch event resize on notice dismiss
     */
    onDismissNoticeClick() {

        let dismissNotice = document.querySelectorAll('.notice-dismiss');

        if (1 <= dismissNotice.length) {
            console.log(Array.from(dismissNotice));
            Array.from(dismissNotice).map(x => x.addEventListener('click', (e) => setTimeout(() => window.dispatchEvent(new Event('resize')), 200)))
        }

    }

   

}

window.addEventListener('DOMContentLoaded', () => new ctclAdminJs());
