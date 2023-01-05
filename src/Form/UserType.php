<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, ['disabled' => true])
            ->add('lastname', TextType::class, ['disabled' => true])
            ->add('email', TextType::class, ['disabled' => true])
//            ->add('password')
//            ->add('role')
            ->add('avatar')
            ->add('birthday', DateType::Class, array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 100),
                'months' => range(date('m'), 12),
                'days' => range(date('d'), 31),
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
