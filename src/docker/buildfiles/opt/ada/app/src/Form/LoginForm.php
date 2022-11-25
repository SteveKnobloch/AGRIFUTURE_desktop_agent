<?php
declare(strict_types = 1);

namespace App\Form;

use App\Form\Entity\Login;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Title of the desktop agent',
                    'help' => 'Please choose a unique title',
                    'attr' => [
                        'placeholder' => 'e.g. Laboratory A'
                    ]
                ]
            )
            ->add(
                'username',
                EmailType::class,
                [
                    'label' => 'Your email address',
                    'help' => 'Username of your AGRIFUTURΞ user account',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Your password',
                    'help' => 'Password of your AGRIFUTURΞ user account'
                ]
            )
            ->add(
                'Submit',
                SubmitType::class,
                [
                    'label' => 'Connect user account',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Login::class,
        ]);
    }
}
