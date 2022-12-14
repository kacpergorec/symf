<?php
declare (strict_types=1);

namespace App\Form\Type\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'user.username',
                'attr' => [
                    'placeholder' => 'user.placeholder_username'
                ],
                'required' => true
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'user.password',
                'required' => true
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'login.remember_me',
                'mapped' => false,
                'required' => false

            ])
            ->add('login', SubmitType::class, [
                'label' => 'login.sign_in',
                'attr' => [
                    'class' => 'btn btn-primary w-full mt-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}