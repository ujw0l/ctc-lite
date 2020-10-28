window.addEventListener('load', () => {

    if (0 < document.querySelectorAll('.ctcl-payment-tab-fieldset,.ctcl-shipping-tab-fieldset').length) {
        let masonry = new jsMasonry('.ctcl-payment-tab-fieldset,.ctcl-shipping-tab-fieldset', { elSelector: 'fieldset', elWidth: 400, elMargin: 5, callback: (el) => el.style.opacity = 1, });
    }

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
});
