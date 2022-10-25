<?php
declare (strict_types=1);

namespace App\Form\Type\Url;

use App\Entity\Url;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrlSubmitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('longUrl', UrlType::class, [
                    'label' => 'url.enter_url',
                    'default_protocol' => 'https',
                    'attr' => [
                        'minlength' => '4',
                        'placeholder' => 'url.placeholder'
                    ]
                ]
            )
            ->add('add', SubmitType::class, [
                    'label' => 'interface.shorten_link',
                    'attr' => ['class' => 'btn btn-primary']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Url::class,
        ]);
    }
}