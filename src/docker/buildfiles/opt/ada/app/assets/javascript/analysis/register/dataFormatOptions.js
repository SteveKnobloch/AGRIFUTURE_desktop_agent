/**
 * @author Rene Gropp <rene.gropp@tritum.de>
 */

// document ready - start
document.addEventListener("DOMContentLoaded", function (event) {
    initOptions();
});

function initOptions() {
    const flowcellTypes = [
        'FLO-FLG001',
        'FLO-FLG111',
        'FLO-MIN106',
        'FLO-MIN107',
        'FLO-MIN110',
        'FLO-MIN111',
        'FLO-MIN112',
        'FLO-MIN114',
        'FLO-MINSP6',
        'FLO-PRO001',
        'FLO-PRO002-ECO',
        'FLO-PRO002M',
        'FLO-PRO002',
        'FLO-PRO111',
        'FLO-PRO112M',
        'FLO-PRO112',
        'FLO-PRO114M',
        'FLO-PRO114'
    ];

    addFlocellTypeOptions(flowcellTypes);
    toggleDependentOptions();
}

function addFlocellTypeOptions(flowcellTypes) {
    const flowcellTypeSelect = document.getElementById('flowcellType');

    // add Flowcell-Type options
    flowcellTypes.forEach(function (type) {
        const option = document.createElement('option');
        option.value = type;
        option.text = type;
        flowcellTypeSelect.appendChild(option);
    });
};

function toggleDependentOptions() {
    const flowcellTypeSelect = document.getElementById('flowcellType'),
        libraryKitSelect = document.getElementById('libraryKit'),
        libraryKitSelectFirstOption = libraryKitSelect.querySelectorAll('option')[0]

    FLO_FLG001 = [
        'SQK-16S024',
        'SQK-CS9109',
        'SQK-DCS108',
        'SQK-DCS109',
        'SQK-LRK001',
        'SQK-LSK108',
        'SQK-LSK109',
        'SQK-LSK109-XL',
        'SQK-LSK110',
        'SQK-LSK110-XL',
        'SQK-LSK111',
        'SQK-LSK111-XL',
        'SQK-LWB001',
        'SQK-LWP001',
        'SQK-MLK111-96-XL',
        'SQK-NBD111-24',
        'SQK-NBD111-96',
        'SQK-PBK004',
        'SQK-PCB109',
        'SQK-PCB110',
        'SQK-PCB111-24',
        'SQK-PCS108',
        'SQK-PCS109',
        'SQK-PCS111',
        'SQK-PSK004',
        'SQK-RAB201',
        'SQK-RAB204',
        'SQK-RAD002',
        'SQK-RAD003',
        'SQK-RAD004',
        'SQK-RAS201',
        'SQK-RBK001',
        'SQK-RBK004',
        'SQK-RBK110-96',
        'SQK-RBK111-24',
        'SQK-RBK111-96',
        'SQK-RLB001',
        'SQK-RLI001',
        'SQK-RNA001',
        'SQK-RNA002',
        'SQK-RPB004',
        'SQK-ULK001',
        'VSK-PTC001',
        'VSK-VBK001',
        'VSK-VMK001',
        'VSK-VMK004',
        'VSK-VPS001',
        'VSK-VSK001',
        'VSK-VSK003',
        'VSK-VSK004'
    ],
        FLO_FLG111 = [
            'SQK-16S024',
            'SQK-CS9109',
            'SQK-DCS108',
            'SQK-DCS109',
            'SQK-LRK001',
            'SQK-LSK108',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK110-XL',
            'SQK-LWB001',
            'SQK-LWP001',
            'SQK-PBK004',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCS108',
            'SQK-PCS109',
            'SQK-PSK004',
            'SQK-RAB201',
            'SQK-RAB204',
            'SQK-RAD002',
            'SQK-RAD003',
            'SQK-RAD004',
            'SQK-RAS201',
            'SQK-RBK001',
            'SQK-RBK004',
            'SQK-RBK110-96',
            'SQK-RLB001',
            'SQK-RLI001',
            'SQK-RPB004',
            'SQK-ULK001',
            'VSK-PTC001',
            'VSK-VBK001',
            'VSK-VMK001',
            'VSK-VSK001',
            'VSK-VSK003'
        ],
        FLO_MIN106 = [
            'SQK-16S024',
            'SQK-CS9109',
            'SQK-DCS108',
            'SQK-DCS109',
            'SQK-LRK001',
            'SQK-LSK108',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK110-XL',
            'SQK-LSK111',
            'SQK-LSK111-XL',
            'SQK-LSK112',
            'SQK-LSK112-XL',
            'SQK-LWB001',
            'SQK-LWP001',
            'SQK-MLK111-96-XL',
            'SQK-NBD111-24',
            'SQK-NBD111-96',
            'SQK-NBD112-24',
            'SQK-NBD112-96',
            'SQK-PBK004',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCB111-24',
            'SQK-PCS108',
            'SQK-PCS109',
            'SQK-PCS111',
            'SQK-PSK004',
            'SQK-RAB201',
            'SQK-RAB204',
            'SQK-RAD002',
            'SQK-RAD003',
            'SQK-RAD004',
            'SQK-RAD112',
            'SQK-RAS201',
            'SQK-RBK001',
            'SQK-RBK004',
            'SQK-RBK110-96',
            'SQK-RBK111-24',
            'SQK-RBK111-96',
            'SQK-RBK112-24',
            'SQK-RBK112-96',
            'SQK-RLB001',
            'SQK-RLI001',
            'SQK-RNA001',
            'SQK-RNA002',
            'SQK-RPB004',
            'SQK-ULK001',
            'VSK-PTC001',
            'VSK-VBK001',
            'VSK-VMK001',
            'VSK-VMK004',
            'VSK-VPS001',
            'VSK-VSK001',
            'VSK-VSK003',
            'VSK-VSK004'
        ],
        FLO_MIN107 = [

            ' SQK-DCS108',
            ' SQK-DCS109',
            ' SQK-LRK001',
            ' SQK-LSK108',
            ' SQK-LSK109',
            ' SQK-LSK308',
            ' SQK-LSK309',
            ' SQK-LSK319',
            ' SQK-LWB001',
            ' SQK-LWP001',
            ' SQK-PBK004',
            ' SQK-PCS108',
            ' SQK-PCS109',
            ' SQK-PSK004',
            ' SQK-RAB201',
            ' SQK-RAB204',
            ' SQK-RAD002',
            ' SQK-RAD003',
            ' SQK-RAD004',
            ' SQK-RAS201',
            ' SQK-RBK001',
            ' SQK-RBK004',
            ' SQK-RLB001',
            ' SQK-RLI001',
            ' SQK-RNA001',
            ' SQK-RNA002',
            ' SQK-RPB004',
            ' VSK-VBK001',
            ' VSK-VMK001',
            ' VSK-VSK001'
        ],
        FLO_MIN110 = [
            'SQK-16S024',
            'SQK-CS9109',
            'SQK-DCS108',
            'SQK-DCS109',
            'SQK-LRK001',
            'SQK-LSK108',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK110-XL',
            'SQK-LWB001',
            'SQK-LWP001',
            'SQK-PBK004',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCS108',
            'SQK-PCS109',
            'SQK-PSK004',
            'SQK-RAB201',
            'SQK-RAB204',
            'SQK-RAD002',
            'SQK-RAD003',
            'SQK-RAD004',
            'SQK-RAS201',
            'SQK-RBK001',
            'SQK-RBK004',
            'SQK-RLB001',
            'SQK-RLI001',
            'SQK-RPB004',
            'VSK-VBK001',
            'VSK-VMK001',
            'VSK-VSK001'
        ],
        FLO_MIN111 = [
            'SQK-16S024',
            'SQK-CS9109',
            'SQK-DCS108',
            'SQK-DCS109',
            'SQK-LRK001',
            'SQK-LSK108',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK110-XL',
            'SQK-LWB001',
            'SQK-LWP001',
            'SQK-PBK004',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCS108',
            'SQK-PCS109',
            'SQK-PSK004',
            'SQK-RAB201',
            'SQK-RAB204',
            'SQK-RAD002',
            'SQK-RAD003',
            'SQK-RAD004',
            'SQK-RAS201',
            'SQK-RBK001',
            'SQK-RBK004',
            'SQK-RBK110-96',
            'SQK-RLB001',
            'SQK-RLI001',
            'SQK-RPB004',
            'SQK-ULK001',
            'VSK-PTC001',
            'VSK-VBK001',
            'VSK-VMK001',
            'VSK-VSK001',
            'VSK-VSK003'
        ],
        FLO_MIN112 = [
            'SQK-LSK112',
            'SQK-LSK112-XL',
            'SQK-NBD112-24',
            'SQK-NBD112-96',
            'SQK-RAD112',
            'SQK-RBK112-24',
            'SQK-RBK112-96'
        ],
        FLO_MIN114 = [
            'SQK-LSK114',
            'SQK-LSK114-XL',
            'SQK-NBD114-24',
            'SQK-NBD114-96',
            'SQK-RAD114',
            'SQK-RBK114-24',
            'SQK-RBK114-96',
            'SQK-ULK114'
        ],
        FLO_MINSP6 = [
            'SQK-16S024',
            'SQK-CS9109',
            'SQK-DCS108',
            'SQK-DCS109',
            'SQK-LRK001',
            'SQK-LSK108',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK110-XL',
            'SQK-LSK111',
            'SQK-LSK111-XL',
            'SQK-LWB001',
            'SQK-LWP001',
            'SQK-MLK111-96-XL',
            'SQK-NBD111-24',
            'SQK-NBD111-96',
            'SQK-PBK004',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCB111-24',
            'SQK-PCS108',
            'SQK-PCS109',
            'SQK-PCS111',
            'SQK-PSK004',
            'SQK-RAB201',
            'SQK-RAB204',
            'SQK-RAD002',
            'SQK-RAD003',
            'SQK-RAD004',
            'SQK-RAS201',
            'SQK-RBK001',
            'SQK-RBK004',
            'SQK-RBK110-96',
            'SQK-RBK111-24',
            'SQK-RBK111-96',
            'SQK-RLB001',
            'SQK-RLI001',
            'SQK-RNA001',
            'SQK-RNA002',
            'SQK-RPB004',
            'SQK-ULK001',
            'VSK-PTC001',
            'VSK-VBK001',
            'VSK-VMK001',
            'VSK-VMK004',
            'VSK-VPS001',
            'VSK-VSK001',
            'VSK-VSK003',
            'VSK-VSK004'
        ],
        FLO_PRO001 = [
            'SQK-DCS109',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK111',
            'SQK-LSK111-XL',
            'SQK-MLK111-96-XL',
            'SQK-NBD111-24',
            'SQK-NBD111-96',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCB111-24',
            'SQK-PCS109',
            'SQK-PCS111',
            'SQK-RBK111-24',
            'SQK-RBK111-96',
            'SQK-RNA002',
            'VSK-VMK004',
            'VSK-VSK004'
        ],
        FLO_PRO002_ECO = [
            'SQK-DCS109',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK111',
            'SQK-LSK111-XL',
            'SQK-MLK111-96-XL',
            'SQK-NBD111-24',
            'SQK-NBD111-96',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCB111-24',
            'SQK-PCS109',
            'SQK-PCS111',
            'SQK-RBK111-24',
            'SQK-RBK111-96',
            'SQK-RNA002',
            'VSK-VMK004',
            'VSK-VSK004'
        ],
        FLO_PRO002M = [
            'SQK-DCS109',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK111',
            'SQK-LSK111-XL',
            'SQK-LSK112',
            'SQK-LSK112-XL',
            'SQK-MLK111-96-XL',
            'SQK-NBD111-24',
            'SQK-NBD111-96',
            'SQK-NBD112-24',
            'SQK-NBD112-96',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCB111-24',
            'SQK-PCS109',
            'SQK-PCS111',
            'SQK-RAD112',
            'SQK-RBK111-24',
            'SQK-RBK111-96',
            'SQK-RBK112-24',
            'SQK-RBK112-96',
            'SQK-RNA002',
            'VSK-VMK004',
            'VSK-VSK004'
        ],
        FLO_PRO002 = [
            'SQK-DCS109',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK111',
            'SQK-LSK111-XL',
            'SQK-LSK112',
            'SQK-LSK112-XL',
            'SQK-MLK111-96-XL',
            'SQK-NBD111-24',
            'SQK-NBD111-96',
            'SQK-NBD112-24',
            'SQK-NBD112-96',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCB111-24',
            'SQK-PCS109',
            'SQK-PCS111',
            'SQK-RAD112',
            'SQK-RBK111-24',
            'SQK-RBK111-96',
            'SQK-RBK112-24',
            'SQK-RBK112-96',
            'SQK-RNA002',
            'VSK-VMK004',
            'VSK-VSK004'
        ],
        FLO_PRO111 = [
            'SQK-16S024',
            'SQK-CS9109',
            'SQK-DCS108',
            'SQK-DCS109',
            'SQK-LRK001',
            'SQK-LSK108',
            'SQK-LSK109',
            'SQK-LSK109-XL',
            'SQK-LSK110',
            'SQK-LSK110-XL',
            'SQK-LWB001',
            'SQK-LWP001',
            'SQK-PBK004',
            'SQK-PCB109',
            'SQK-PCB110',
            'SQK-PCS108',
            'SQK-PCS109',
            'SQK-PSK004',
            'SQK-RAB201',
            'SQK-RAB204',
            'SQK-RAD002',
            'SQK-RAD003',
            'SQK-RAD004',
            'SQK-RAS201',
            'SQK-RBK001',
            'SQK-RBK004',
            'SQK-RBK110-96',
            'SQK-RLB001',
            'SQK-RLI001',
            'SQK-RPB004',
            'SQK-ULK001',
            'VSK-PTC001',
            'VSK-VBK001',
            'VSK-VMK001',
            'VSK-VSK001',
            'VSK-VSK003'
        ],
        FLO_PRO112M = [
            'SQK-LSK112',
            'SQK-LSK112-XL',
            'SQK-NBD112-24',
            'SQK-NBD112-96',
            'SQK-RAD112',
            'SQK-RBK112-24',
            'SQK-RBK112-96'
        ],
        FLO_PRO112 = [
            'SQK-LSK112',
            'SQK-LSK112-XL',
            'SQK-NBD112-24',
            'SQK-NBD112-96',
            'SQK-RAD112',
            'SQK-RBK112-24',
            'SQK-RBK112-96'
        ],
        FLO_PRO114M = [
            'SQK-LSK114',
            'SQK-LSK114-XL',
            'SQK-NBD114-24',
            'SQK-NBD114-96',
            'SQK-RAD114',
            'SQK-RBK114-24',
            'SQK-RBK114-96',
            'SQK-ULK114'
        ],
        FLO_PRO114 = [
            'SQK-LSK114',
            'SQK-LSK114-XL',
            'SQK-NBD114-24',
            'SQK-NBD114-96',
            'SQK-RAD114',
            'SQK-RBK114-24',
            'SQK-RBK114-96',
            'SQK-ULK114'
        ];

    libraryKitSelectFirstOption.removeAttribute('selected');// hide "please select"
    addLibraryKitOptions();

    flowcellTypeSelect.addEventListener('change', function () {
        addLibraryKitOptions();
    });

    function addLibraryKitOptions() {
        const optionsOld = libraryKitSelect.querySelectorAll('option:not(:first-child)'),
            value = flowcellTypeSelect.value,
            libraryKitGroup = eval(value.replace('-', '_'));

        // remove existing options except the first "please select"-option
        optionsOld.forEach(function (optionOld) {
            optionOld.remove();
        });

        // add new options dependent on selected Flowcell-Type
        if (typeof (libraryKitGroup) !== 'undefined' && libraryKitGroup !== null) {
            libraryKitGroup.forEach(function (libraryKit) {
                const option = document.createElement('option');
                option.value = libraryKit;
                option.text = libraryKit;
                libraryKitSelect.appendChild(option);
            });

            // enable library-kit select
            libraryKitSelect.removeAttribute('disabled');
            libraryKitSelectFirstOption.setAttribute('selected', 'selected');
        }
    }
};
