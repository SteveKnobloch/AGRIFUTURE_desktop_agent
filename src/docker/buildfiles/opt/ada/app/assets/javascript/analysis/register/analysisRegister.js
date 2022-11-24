/**
 * @author Rene Gropp <rene.gropp@tritum.de>
 */

window.bootstrap = require('bootstrap');

// document ready - start
document.addEventListener("DOMContentLoaded", function (event) {
    const fast5OptionsCollapse = new bootstrap.Collapse('#collapseFast5Options', {
        toggle: false
    });
    toggleFas5Options(fast5OptionsCollapse);
});

function toggleFas5Options(collapse) {
    const dataFormatSelect = document.getElementById('selectDataFormat'),
        fieldset = document.getElementById('collapseFast5Options'),
        fields = fieldset.getElementsByClassName('form-control');

    if (typeof (dataFormatSelect) !== 'undefined' && dataFormatSelect !== null) {

        toggleFields();

        dataFormatSelect.addEventListener('change', function () {
            toggleFields();
        });

        function toggleFields() {
            if (dataFormatSelect.value === 'fast5') {
                collapse.show();
                dataFormatSelect.setAttribute('aria-expanded', 'true')
                for (const field of fields) {
                    field.setAttribute('required', 'required');
                }
            } else {
                collapse.hide();
                dataFormatSelect.setAttribute('aria-expanded', 'false')
                for (const field of fields) {
                    field.removeAttribute('required', 'required');
                }
            }
        }
    }
}
