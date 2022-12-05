<?php
declare(strict_types = 1);

namespace App\Form;

use App\Form\Entity\Coordinates;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoordinatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'longitude',
                NumberType::class,
                [
                    'required' => false,
                    'scale' => 7,
                    'label' => 'Longitude',
                    'attr' => [
                        'placeholder' => '50.1175030',
                    ],
                    'row_attr' => [
                        'class' => 'col-6',
                    ],
                ]
            )
            ->add(
                'latitude',
                NumberType::class,
                [
                    'required' => false,
                    'scale' => 7,
                    'label' => 'Latitude',
                    'attr' => [
                        'placeholder' => '8.6522533',
                    ],
                    'row_attr' => [
                        'class' => 'col-6',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Coordinates::class,
            'attr' => [
                'class' => 'form-element form-element-gridrow row',
            ]
        ]);
    }
}
