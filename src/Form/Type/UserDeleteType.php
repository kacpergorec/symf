<?php
declare (strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use App\Form\Model\UserDeleteModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                    'help' => 'Type in your current password to delete your account.',
                ]
            )
            ->add('save', SubmitType::class, [
                    'attr' => ['class' => 'btn btn-outline-danger'],
                    'label' => 'Delete account'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDeleteModel::class,
        ]);
    }
}