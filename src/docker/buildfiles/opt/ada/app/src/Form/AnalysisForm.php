<?php
declare(strict_types = 1);

namespace App\Form;

use App\Enum\AnalysisType;
use App\Enum\FileFormat;
use App\Form\Entity\AnalysisInput;
use App\Service\UploadService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalysisForm extends AbstractType
{
    public function __construct(
        private readonly UploadService $uploads,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formatOptions = json_decode(
            file_get_contents(
                __DIR__ . '/../../assets/javascript/analysis/register/dataFormatOptions.json'
            ),
            true,
            JSON_THROW_ON_ERROR,
        );

        $flowcellTypes = array_keys($formatOptions);
        $libraryToolkits = array_unique(
            array_merge(
                ...array_values($formatOptions)
            )
        );

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Title of analysis',
                    'help' => 'Please choose a unique title',
                ],
            )
            ->add(
                'host',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Host plant',
                    'attr' => [
                        'placeholder' => 'e.g. Tomato plant, Solanum lycopersicum'
                    ],
                ],
            )
            ->add(
                'country',
                CountryType::class,
                [
                    'placeholder' => 'Please select',
                    'label' => 'Country of sample origin',
                ],
            )
            ->add(
                'city',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Location of sample collection',
                    'attr' => [
                        'placeholder' => 'e.g. city, region or state',
                    ],
                ],
            )
            ->add(
                'coordinates',
                CoordinatesType::class,
                [
                    'label' => 'Geolocation of sample collection site',
                ],
            )
            ->add(
                'directory',
                ChoiceType::class,
                [
                    'label' => 'Upload folder',
                    'help' => 'Please select the folder that contains the raw data, if it is not displayed, reload the Desktop Agent.',
                    ...$this->directorySelectOptions(),
                ],
            )
            ->add(
                'type',
                EnumType::class,
                [
                    'class' => AnalysisType::class,
                    'label' => 'Reference database',
                    'help' => 'Select Pathogen DB only if you want to determine pathogens. Dependent on selection, further fields could be shown.',
                    'placeholder' => 'Please select',
                ],
            )
            ->add(
                'subSpeciesLevel',
                CheckboxType::class,
                [
                    // ToDo Make this nice
                    'required' => false,
                    'data' => false,
                ],
            )
            ->add(
                'sensitiveMode',
                CheckboxType::class,
                [
                    // ToDo Make this nice
                    'required' => false,
                    'data' => false,
                ],
            )
            ->add(
                'format',
                EnumType::class,
                [
                    'class' => FileFormat::class,
                    'label' => 'Format of raw data',
                    'help' => 'Dependent on selection, further fields could be shown.',
                    'attr' => [
                        'aria-expanded' => false,
                        'aria-controls' => 'collapseFast5Options'
                    ],
                    'placeholder' => 'Please select',
                ],
            )
            ->add(
                'flowcellType',
                ChoiceType::class,
                [
                    'placeholder' => 'Please select',
                    'required' => false,
                    'row_attr' => [
                        'class' => 'col-6',
                    ],
                    'choices' => array_combine(
                        $flowcellTypes,
                        $flowcellTypes
                    ),
                ]
            )
            ->add(
                'libraryToolkit',
                ChoiceType::class,
                [
                    'placeholder' => 'Please select',
                    'required' => false,
                    'help' => 'Please select Flowcell-Typ first.',
                    'row_attr' => [
                        'class' => 'col-6 js-disabled',
                    ],
                    'choices' => array_combine(
                        $libraryToolkits,
                        $libraryToolkits,
                    ),
                ],
            )
            ->add(
                'minQualityScore',
                NumberType::class,
                [
                    // ToDo Make this nice
                    'html5' => true,
                    'data' => 8,
                    'row_attr' => [
                        'class' => 'col-6',
                    ],
                ]
            )
            ->add(
                'minSequenceLength',
                NumberType::class,
                [
                    // ToDo Make this nice
                    'html5' => true,
                    'data' => 1000,
                    'row_attr' => [
                        'class' => 'col-6',
                    ],
                ]
            )
            ->add(
                'termsOfServiceAccepted',
                CheckboxType::class,
                [
                    'label' => 'I have read the privacy policy and terms of service',
                ]
            )
            ->add(
                'submit',
                FormFooterType::class,
                [
                    'label' => 'Launch analysis',
                    'cancel_route' => 'app_page_user_account_show',
                    'attr' => [
                        'class' => 'ms-3 btn btn-primary',
                    ],
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnalysisInput::class,
            'attr' => [
                'class' => 'full-width',
            ]
        ]);
    }

    private function directorySelectOptions(): array
    {
        $directories = $this->uploads->getValidDirectories();
        $options = [
            'choices' => [],
            'preferred_choices' => [],
        ];

        foreach ($directories as $directory) {
            $readable = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $directory,
            );

            $options['choices'][$readable] = $directory;
            if ($this->uploads->containsSequence($directory)) {
                $options['preferred_choices'][] = $directory;
            }
        }

        return $options;
    }
}
