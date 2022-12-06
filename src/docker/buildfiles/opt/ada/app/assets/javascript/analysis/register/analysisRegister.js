/**
 * @author Rene Gropp <rene.gropp@tritum.de>
 */

window.bootstrap = require('bootstrap');

// document ready - start
document.addEventListener("DOMContentLoaded", function (event) {
    const rawDataFormatSelect = document.getElementById('analysis_form_format'),
        fast5AdditionalFieldset = document.getElementById('collapseFast5Options'),
        referenceDbSelect = document.getElementById('analysis_form_type'),
        pathogenDbAdditionalFieldset = document.getElementById('pathogenDBoptions');

    // init bootstrap collapses
    const rawDataFormatAdditionalFieldsetCollapse = new bootstrap.Collapse('#collapseFast5Options', {
        toggle: false
    });
    const pathogenDbAdditionalFieldsetCollapse = new bootstrap.Collapse('#pathogenDBoptions', {
        toggle: false
    });

    // init toggle functions
    toggleAdditionalFields(rawDataFormatAdditionalFieldsetCollapse, rawDataFormatSelect, fast5AdditionalFieldset, 'chemical/x-seq-na-fast5');
    toggleAdditionalFields(pathogenDbAdditionalFieldsetCollapse, referenceDbSelect, pathogenDbAdditionalFieldset, 'pathogens');

    //remove errors
    removeVisualInvalidStuff();
});

function toggleAdditionalFields(bsCollapse, select, fieldset, triggerValue) {
    const fields = fieldset.querySelectorAll('.form-select');

    if (select) {

        toggleFields();

        select.addEventListener('change', function () {
            toggleFields();
        });

        function toggleFields() {
            const labelSuffixClass = 'required';

            if (select.value === triggerValue) {
                bsCollapse.show();
                select.setAttribute('aria-expanded', 'true')
                for (const field of fields) {
                    const label = field.previousSibling,
                        labelSuffix = document.createElement('span');

                    labelSuffix.classList.add(labelSuffixClass);
                    labelSuffix.innerHTML = '*';
                    label.appendChild(labelSuffix);
                    field.setAttribute('required', 'required');
                }
            } else {
                bsCollapse.hide();
                select.setAttribute('aria-expanded', 'false');
                for (const field of fields) {
                    field.removeAttribute('required', 'required');
                    field.previousSibling.querySelectorAll('.required').forEach(function (suffix) {
                        suffix.remove();
                    });
                }
            }
        }
    }
}

function removeVisualInvalidStuff() {
    const invalidClass = 'is-invalid',
        invalidFields = document.getElementsByClassName(invalidClass);

    for (const field of invalidFields) {
        field.addEventListener('change', function () {
            const fieldParent = field.parentElement,
            invalidMessages = fieldParent.getElementsByClassName('invalid-feedback');

            field.classList.remove(invalidClass);
            for (const msg of invalidMessages) {
                msg.remove();
            }
        });
    }
}
