(function () {
    'use strict';

    var hex6 = /^#[0-9a-f]{6}$/i;

    function expandHex(value) {
        var short = /^#([0-9a-f])([0-9a-f])([0-9a-f])$/i.exec(value);
        return short ? '#' + short[1] + short[1] + short[2] + short[2] + short[3] + short[3] : value;
    }

    // Color fields: picker and text field stay in sync, the text field may be empty (= PAYONE default).
    function initColorField(wrapper) {
        var picker = wrapper.querySelector('.payone-color__picker');
        var text = wrapper.querySelector('.payone-color__value');
        if (!picker || !text) {
            return;
        }
        var update = function () {
            var value = expandHex(text.value.trim());
            wrapper.classList.toggle('payone-color--empty', value === '');
            if (hex6.test(value)) {
                picker.value = value.toLowerCase();
            }
        };
        picker.addEventListener('input', function () {
            text.value = picker.value.toUpperCase();
            update();
        });
        text.addEventListener('input', update);
        update();
    }

    // Secret fields: show / hide the value.
    function initSecretField(button) {
        var input = document.getElementById(button.getAttribute('data-target'));
        if (!input) {
            return;
        }
        button.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.textContent = show ? button.getAttribute('data-hide') : button.getAttribute('data-show');
            button.setAttribute('aria-pressed', show ? 'true' : 'false');
        });
    }

    // Connection test on the global settings page.
    function initConnectionTest(button) {
        var list = document.getElementById('payone-connection-results');
        var url = button.getAttribute('data-url');
        if (!list || !url) {
            return;
        }
        button.addEventListener('click', function () {
            var rows = list.querySelectorAll('[data-gateway]');
            var pending = rows.length;
            button.disabled = true;
            rows.forEach(function (row) {
                var status = row.querySelector('.payone-test__status');
                status.className = 'payone-test__status payone-badge payone-badge--neutral';
                status.textContent = button.getAttribute('data-running');

                var body = new URLSearchParams();
                body.append('gateway_id', row.getAttribute('data-gateway'));
                fetch(url, {method: 'POST', credentials: 'same-origin', body: body})
                    .then(function (response) { return response.json(); })
                    .then(function (json) {
                        status.className = 'payone-test__status payone-badge ' + (json.result ? 'payone-badge--success' : 'payone-badge--error');
                        status.textContent = json.message;
                    })
                    .catch(function () {
                        status.className = 'payone-test__status payone-badge payone-badge--error';
                        status.textContent = button.getAttribute('data-failed');
                    })
                    .then(function () {
                        pending--;
                        if (pending < 1) {
                            button.disabled = false;
                        }
                    });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.payone-color').forEach(initColorField);
        document.querySelectorAll('.payone-secret__toggle').forEach(initSecretField);
        document.querySelectorAll('.payone-connection-test').forEach(initConnectionTest);
    });
})();
