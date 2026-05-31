(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.querySelector('input[name="codigo"]');
        if (codigoInput) {
            codigoInput.focus();
        }
    });

    const codigoInputs = document.querySelectorAll('input[name="codigo"]');
    codigoInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    });
})();