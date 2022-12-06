<?php
declare(strict_types = 1);

namespace App\Form;

use App\Enum\AnalysisStatus;
use App\Form\Entity\UpdateStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A form for updating the status.
 *
 * Has a single submit button (plus CSRF if enabled) and a hidden field
 * for the target status.
 */
final class UpdateStatusType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $i18n
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $label = $options['label'];
        $icon = $options['icon'];
        $isHtml = false;

        if (!empty($icon)) {
            $isHtml = true;
            $label = "<i class=\"bi bi-$icon\"></i>" .
                $this->i18n->trans($label);
        }

        $builder
            ->add(
                'status',
                HiddenType::class,
                [
                    'data' => $options['status']->value,
                ]
            )->add(
                'submit',
                SubmitType::class,
                [
                    'label' => $label,
                    'label_html' => $isHtml,
                    'attr' => [
                        'class' => $options['class'],
                    ],
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions(
            $resolver
        );

        $resolver->setDefaults([
            'status' => null,
            'label' => 'Submit',
            'icon' => '',
            'class' => 'btn',
            'data_class' => UpdateStatus::class,
        ]);

        $resolver->setAllowedTypes(
            'status',
            AnalysisStatus::class,
        );
        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('icon', 'string');
        $resolver->setAllowedTypes('class', 'string');
    }
}
