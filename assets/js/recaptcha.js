function executeRecaptcha(action, siteKey) {
    return new Promise(function(resolve, reject) {
        if (!window.grecaptcha || !siteKey) {
            reject(new Error('reCAPTCHA indisponível'));
            return;
        }

        window.grecaptcha.ready(function() {
            try {
                window.grecaptcha.execute(siteKey, { action: action }).then(resolve, reject);
            } catch (error) {
                reject(error);
            }
        });
    });
}