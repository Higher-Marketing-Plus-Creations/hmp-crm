(function () {
    function serializeForm(form) {
        var formData = new FormData(form);
        var fields = {};

        formData.forEach(function (value, key) {
            if (fields[key] !== undefined) {
                if (!Array.isArray(fields[key])) {
                    fields[key] = [fields[key]];
                }

                fields[key].push(value);
                return;
            }

            fields[key] = value;
        });

        return fields;
    }

    function setMessage(target, text, success) {
        if (!target) {
            return;
        }

        target.textContent = text;
        target.style.display = 'block';
        target.style.color = success ? '#047857' : '#b91c1c';
    }

    window.LeadFormTracker = {
        init: function (config) {
            var selector = config.selector || 'form[data-lead-form]';
            var forms = document.querySelectorAll(selector);

            forms.forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    var fields = serializeForm(form);
                    var statusTarget = config.messageTarget
                        ? document.querySelector(config.messageTarget)
                        : form.querySelector('[data-lead-message]');
                    var honeypotSelector = '[name="' + (config.honeypotField || 'website') + '"]';
                    var honeypotField = form.querySelector(honeypotSelector);

                    var payload = {
                        website_name: config.websiteName || document.title,
                        page_url: window.location.href,
                        form_name: config.formName || form.getAttribute('data-form-name') || form.getAttribute('name') || 'Website Form',
                        form_identifier: form.getAttribute('id') || form.getAttribute('name') || null,
                        honeypot: honeypotField ? honeypotField.value : '',
                        recaptcha_token: typeof config.getRecaptchaToken === 'function' ? config.getRecaptchaToken(form) : null,
                        fields: fields
                    };

                    fetch(config.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-API-KEY': config.apiKey
                        },
                        body: JSON.stringify(payload)
                    })
                        .then(function (response) {
                            return response.json().then(function (data) {
                                if (!response.ok) {
                                    throw new Error(data.message || 'Submission failed.');
                                }

                                return data;
                            });
                        })
                        .then(function () {
                            setMessage(statusTarget, config.successMessage || 'Thank you. Your message has been sent successfully.', true);
                            if (typeof config.onSuccess === 'function') {
                                config.onSuccess(form);
                            } else {
                                form.reset();
                            }
                        })
                        .catch(function (error) {
                            setMessage(statusTarget, config.errorMessage || 'Sorry, we could not submit your request right now.', false);
                            if (typeof config.onError === 'function') {
                                config.onError(error, form);
                            }
                        });
                });
            });
        }
    };
})();
