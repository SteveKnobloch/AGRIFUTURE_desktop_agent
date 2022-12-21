/**
 * @author Rene Gropp <rene.gropp@tritum.de>
 */

import data from './dataFormatOptions.json';

// document ready - start
document.addEventListener("DOMContentLoaded", function (event) {
    toggleDependentOptions(data);
});

function toggleDependentOptions(data)
{
    const flowcellTypeSelect = document.getElementById('analysis_form_flowcellType'),
        libraryKitSelect = document.getElementById('analysis_form_libraryToolkit'),
        libraryKitSelectFirstOption = libraryKitSelect.querySelectorAll('option')[0];

    if (!flowcellTypeSelect.value) {
        libraryKitSelect.setAttribute('disabled', 'disabled');
    }

    libraryKitSelectFirstOption.removeAttribute('selected');// hide "please select"
    addLibraryKitOptions(data);

    flowcellTypeSelect.addEventListener('change', function () {
        addLibraryKitOptions(data);
    });

    function addLibraryKitOptions(data)
    {
        const optionsOld = libraryKitSelect.querySelectorAll('option:not(:first-child)'),
            value = flowcellTypeSelect.value;

        // remove existing options except the first "please select"-option
        optionsOld.forEach(function (optionOld) {
            optionOld.remove();
        });

        if (value) {
            const libraryKitGroup = data[value];

            // add new options dependent on selected Flowcell-Type
            if (libraryKitGroup) {
                for (const libraryKit of libraryKitGroup) {
                    const option = document.createElement('option');
                    option.value = libraryKit;
                    option.text = libraryKit;
                    libraryKitSelect.appendChild(option);
                }
            }

            // enable library-kit select
            libraryKitSelect.removeAttribute('disabled');
        } else {
            libraryKitSelect.setAttribute('disabled', 'disabled');
            libraryKitSelectFirstOption.setAttribute('selected', 'selected');
        }
    }
}
