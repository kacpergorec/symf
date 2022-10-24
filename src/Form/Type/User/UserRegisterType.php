<?php
declare (strict_types=1);

namespace App\Form\Type\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'user.email',
                'attr' => ['placeholder' => 'register.placeholder.email'],
                'required' => true
            ])
            ->add('username', TextType::class, [
                'label' => 'user.username',
                'attr' => ['placeholder' => 'register.placeholder.username'],
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'label' => 'user.password',
                'help' => 'register.password_help',
                'required' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'register.header',
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