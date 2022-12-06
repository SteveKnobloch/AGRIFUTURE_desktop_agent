<?php
declare(strict_types = 1);

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormFooterType extends SubmitType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions(
            $resolver
        );

        $resolver->setDefaults([
            'cancel_route' => null,
            'cancel_label' => 'Cancel'
        ]);

        $resolver->setAllowedTypes('cancel_route', ['null', 'string']);
        $resolver->setAllowedTypes('cancel_label', 'string');
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $view->vars['cancel_route'] = $options['cancel_route'];
        $view->vars['cancel_label'] = $options['cancel_label'];

        parent::buildView(
            $view,
            $form,
            $options
        );
    }
}
